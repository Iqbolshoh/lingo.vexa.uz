<?php

declare(strict_types=1);

/**
 * Learning domain helpers: course catalogue, lesson unlocking (passing_score of the
 * previous lesson in the same course), mastery, exercise grading, XP and streaks.
 */

const QUESTION_TYPES = [
    'choice' => 'Variant tanlash',
    'listen' => 'Tinglab topish',
    'build' => 'Gap tuzish (so‘z bloklari)',
    'fill' => 'So‘zni yozish',
];

const COURSE_THEMES = ['lapis' => 'Lojuvard (ko‘k)', 'gold' => 'Oltin', 'emerald' => 'Zumrad', 'ruby' => 'Yoqut'];

const LESSON_ICONS = ['book-open', 'message-square', 'users', 'hash', 'palette', 'apple', 'heart', 'sparkles', 'registon', 'coins', 'star', 'map-pin', 'video', 'target', 'trophy'];

const XP_PER_CORRECT = 10;
const XP_PASS_BONUS = 20;
const GAME_COINS = 20;

function published_courses(): array
{
    return db()->query("SELECT * FROM courses WHERE status = 'published' ORDER BY sort_order, id")->fetchAll();
}

function find_course(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM courses WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

function find_lesson(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM lessons WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

function course_lessons(int $courseId, bool $publishedOnly = true): array
{
    $sql = 'SELECT * FROM lessons WHERE course_id = ?' . ($publishedOnly ? " AND status = 'published'" : '') . ' ORDER BY position, id';
    $stmt = db()->prepare($sql);
    $stmt->execute([$courseId]);
    return $stmt->fetchAll();
}

function user_progress_map(int $userId): array
{
    $stmt = db()->prepare('SELECT * FROM lesson_progress WHERE user_id = ?');
    $stmt->execute([$userId]);
    $map = [];
    foreach ($stmt->fetchAll() as $row) {
        $map[(int) $row['lesson_id']] = $row;
    }
    return $map;
}

/**
 * Annotates the course lessons with user_status (completed|available|locked),
 * best_score and the previous lesson. A lesson is open when it is the first one,
 * the previous lesson was passed at its own passing_score, or an admin unlocked it.
 */
function annotate_course_lessons(array $lessons, array $progress, bool $isAdmin = false): array
{
    $prev = null;
    foreach ($lessons as &$lesson) {
        $p = $progress[(int) $lesson['id']] ?? null;
        $best = (int) ($p['best_score'] ?? 0);
        $completed = $p && $p['status'] === 'completed' && $best >= (int) $lesson['passing_score'];
        $prevPassed = $prev === null || $prev['user_status'] === 'completed';
        $override = $p && (int) $p['manual_override'] === 1;

        $lesson['best_score'] = $best;
        $lesson['prev_lesson'] = $prev ? ['id' => $prev['id'], 'title' => $prev['title'], 'position' => $prev['position'], 'passing_score' => $prev['passing_score']] : null;
        if ($completed) {
            $lesson['user_status'] = 'completed';
        } elseif ($prevPassed || $override || $isAdmin) {
            $lesson['user_status'] = 'available';
        } else {
            $lesson['user_status'] = 'locked';
        }
        $prev = $lesson;
    }
    unset($lesson);
    return $lessons;
}

/** Course summary for one user: lessons, counts, mastery and the next lesson to take. */
function course_state(array $course, int $userId, bool $isAdmin = false): array
{
    $lessons = annotate_course_lessons(course_lessons((int) $course['id']), user_progress_map($userId), $isAdmin);
    $total = count($lessons);
    $completed = count(array_filter($lessons, fn ($l) => $l['user_status'] === 'completed'));
    $mastery = $total > 0 ? (int) round(array_sum(array_column($lessons, 'best_score')) / $total) : 0;
    $next = null;
    foreach ($lessons as $l) {
        if ($l['user_status'] === 'available') {
            $next = $l;
            break;
        }
    }
    return [
        'course' => $course,
        'lessons' => $lessons,
        'total' => $total,
        'completed' => $completed,
        'percent' => $total > 0 ? (int) round($completed / $total * 100) : 0,
        'mastery' => min(100, $mastery),
        'next' => $next,
    ];
}

function lesson_state_for_user(array $lesson, int $userId, bool $isAdmin = false): array
{
    foreach (course_state(find_course((int) $lesson['course_id']) ?? ['id' => $lesson['course_id']], $userId, $isAdmin)['lessons'] as $l) {
        if ((int) $l['id'] === (int) $lesson['id']) {
            return $l;
        }
    }
    return $lesson + ['user_status' => $isAdmin ? 'available' : 'locked', 'best_score' => 0, 'prev_lesson' => null];
}

function next_lesson_in_course(array $lesson): ?array
{
    $stmt = db()->prepare("
        SELECT * FROM lessons
        WHERE course_id = ? AND status = 'published' AND (position > ? OR (position = ? AND id > ?))
        ORDER BY position, id LIMIT 1
    ");
    $stmt->execute([$lesson['course_id'], $lesson['position'], $lesson['position'], $lesson['id']]);
    return $stmt->fetch() ?: null;
}

function lesson_questions(int $lessonId): array
{
    $stmt = db()->prepare('
        SELECT q.* FROM questions q JOIN quizzes z ON z.id = q.quiz_id
        WHERE z.lesson_id = ? ORDER BY q.order_num, q.id
    ');
    $stmt->execute([$lessonId]);
    $questions = $stmt->fetchAll();
    foreach ($questions as &$q) {
        $q['options'] = json_decode((string) $q['options_json'], true) ?: [];
        if ($q['type'] === 'build') {
            $q['tiles'] = build_tiles($q);
        }
    }
    unset($q);
    return $questions;
}

function lesson_quiz(int $lessonId): array
{
    $stmt = db()->prepare('SELECT * FROM quizzes WHERE lesson_id = ? ORDER BY id LIMIT 1');
    $stmt->execute([$lessonId]);
    $quiz = $stmt->fetch();
    if ($quiz) {
        return $quiz;
    }
    $lesson = find_lesson($lessonId);
    db()->prepare('INSERT INTO quizzes (lesson_id, title, pass_score) VALUES (?, ?, ?)')
        ->execute([$lessonId, ($lesson['title'] ?? 'Dars') . ' — Yakuniy Test', (int) ($lesson['passing_score'] ?? 80)]);
    return lesson_quiz($lessonId);
}

/** Answer words plus distractors, shuffled deterministically per question. */
function build_tiles(array $q): array
{
    $words = preg_split('/\s+/u', trim(preg_replace('/[.,!?«»"]/u', '', (string) $q['answer_text']))) ?: [];
    $tiles = array_values(array_filter(array_merge($words, $q['options'] ?? []), fn ($w) => $w !== ''));
    mt_srand(crc32((string) $q['id'] . $q['answer_text']));
    shuffle($tiles);
    mt_srand();
    return $tiles;
}

/** Lowercase, unify the many o‘/g‘ apostrophes, strip punctuation and extra spaces. */
function normalize_answer(string $text): string
{
    $text = mb_strtolower(trim($text), 'UTF-8');
    $text = str_replace(['‘', '’', 'ʻ', 'ʼ', '`', '´', "'"], "'", $text);
    $text = preg_replace('/[.,!?«»"“”:;]/u', '', $text);
    return trim(preg_replace('/\s+/u', ' ', $text));
}

/** Grades one submitted answer; returns [isCorrect, chosenLabel, correctLabel]. */
function grade_question(array $q, mixed $submitted): array
{
    if (in_array($q['type'], ['build', 'fill'], true)) {
        $given = trim((string) $submitted);
        $ok = $given !== '' && normalize_answer($given) === normalize_answer((string) $q['answer_text']);
        return [$ok, $given !== '' ? $given : 'Javob berilmadi', (string) $q['answer_text']];
    }
    $idx = is_numeric($submitted) ? (int) $submitted : -1;
    $options = $q['options'];
    return [$idx === (int) $q['correct_index'], $options[$idx] ?? 'Tanlanmadi', $options[(int) $q['correct_index']] ?? ''];
}

/** Counts today's activity towards the streak and adds XP/coins. */
function reward_user(int $userId, int $xp, int $coins = 0): array
{
    $pdo = db();
    $user = $pdo->query("SELECT xp, coins, streak_days, last_active_date, timezone FROM users WHERE id = {$userId}")->fetch();
    $tz = @timezone_open(explode(' ', (string) ($user['timezone'] ?? ''))[0]) ?: new DateTimeZone('America/New_York');
    $today = (new DateTimeImmutable('now', $tz))->format('Y-m-d');
    $yesterday = (new DateTimeImmutable('yesterday', $tz))->format('Y-m-d');
    $streak = (int) $user['streak_days'];
    if ($user['last_active_date'] !== $today) {
        $streak = $user['last_active_date'] === $yesterday ? $streak + 1 : 1;
    }
    $pdo->prepare('UPDATE users SET xp = xp + ?, coins = coins + ?, streak_days = ?, last_active_date = ? WHERE id = ?')
        ->execute([$xp, $coins, $streak, $today, $userId]);
    return ['streak' => $streak, 'xp' => (int) $user['xp'] + $xp];
}

/** The streak is only "alive" if the user practised today or yesterday. */
function current_streak(array $user): int
{
    $tz = @timezone_open(explode(' ', (string) ($user['timezone'] ?? ''))[0]) ?: new DateTimeZone('America/New_York');
    $yesterday = (new DateTimeImmutable('yesterday', $tz))->format('Y-m-d');
    return ($user['last_active_date'] ?? '') >= $yesterday ? (int) $user['streak_days'] : 0;
}

/** Simple level curve: 100 XP for level 2, +50 more per level after that. */
function xp_level(int $xp): array
{
    $level = 1;
    $need = 100;
    $floor = 0;
    while ($xp >= $floor + $need) {
        $floor += $need;
        $level++;
        $need += 50;
    }
    return ['level' => $level, 'into' => $xp - $floor, 'need' => $need, 'percent' => (int) round(($xp - $floor) / $need * 100)];
}

function award_badge(int $userId, string $code): void
{
    // Select from badges so an unknown code is a no-op instead of a foreign key error.
    db()->prepare('INSERT OR IGNORE INTO user_badges (user_id, badge_code) SELECT ?, code FROM badges WHERE code = ?')->execute([$userId, $code]);
}

function mastery_label(int $percent): array
{
    return match (true) {
        $percent >= 90 => ['A’lo', 'excellent'],
        $percent >= 80 => ['O‘zlashtirildi', 'good'],
        $percent >= 50 => ['Rivojlanmoqda', 'mid'],
        $percent > 0 => ['Qayta ishlash kerak', 'low'],
        default => ['Boshlanmagan', 'none'],
    };
}

/** Zones every live session is shown in: Uzbekistan, Europe and the US. */
function live_timezones(): array
{
    return ['Asia/Tashkent' => 'Toshkent', 'Europe/London' => 'London', 'America/New_York' => 'ET', 'America/Chicago' => 'CT', 'America/Los_Angeles' => 'PT'];
}

/** starts_at is stored in UTC; returns "Sat, Nov 22 · 11:00 ET" style labels for the US zones. */
function live_time_labels(string $startsAtUtc): array
{
    $labels = [];
    try {
        $utc = new DateTimeImmutable($startsAtUtc, new DateTimeZone('UTC'));
    } catch (Throwable) {
        return [];
    }
    foreach (live_timezones() as $zone => $short) {
        $labels[$short] = $utc->setTimezone(new DateTimeZone($zone))->format('H:i');
    }
    return $labels;
}

function uz_date(string $utc, string $zone = 'America/New_York'): string
{
    $months = [1 => 'yanvar', 'fevral', 'mart', 'aprel', 'may', 'iyun', 'iyul', 'avgust', 'sentabr', 'oktabr', 'noyabr', 'dekabr'];
    $days = [1 => 'Dushanba', 'Seshanba', 'Chorshanba', 'Payshanba', 'Juma', 'Shanba', 'Yakshanba'];
    try {
        $d = (new DateTimeImmutable($utc, new DateTimeZone('UTC')))->setTimezone(new DateTimeZone($zone));
    } catch (Throwable) {
        return $utc;
    }
    return $days[(int) $d->format('N')] . ', ' . (int) $d->format('j') . '-' . $months[(int) $d->format('n')];
}

/** Login throttling: 5 failures per IP in 15 minutes. */
function login_throttled(string $ip): bool
{
    $stmt = db()->prepare('SELECT count(*) FROM login_attempts WHERE ip = ? AND created_at > ?');
    $stmt->execute([$ip, time() - 900]);
    return (int) $stmt->fetchColumn() >= 5;
}

function record_login_failure(string $ip, string $identifier): void
{
    $pdo = db();
    $pdo->prepare('INSERT INTO login_attempts (ip, identifier, created_at) VALUES (?, ?, ?)')->execute([$ip, mb_substr($identifier, 0, 120), time()]);
    $pdo->exec('DELETE FROM login_attempts WHERE created_at < ' . (time() - 86400));
}

function clear_login_failures(string $ip): void
{
    db()->prepare('DELETE FROM login_attempts WHERE ip = ?')->execute([$ip]);
}

const AGE_BANDS = ['7-8', '9-10', '11', '12-17', '18+'];
const AGE_LABELS = ['7-8' => '7–8 yosh', '9-10' => '9–10 yosh', '11' => '11 yosh', '12-17' => '12–17 yosh', '18+' => 'Kattalar (18+)'];
const LEVELS = ['A0', 'A1', 'A2'];
const TIMEZONES = [
    'Asia/Tashkent' => 'Toshkent (UZT)',
    'Europe/London' => 'London (GMT/BST)',
    'Europe/Berlin' => 'Markaziy Yevropa (CET)',
    'Europe/Moscow' => 'Moskva (MSK)',
    'Asia/Seoul' => 'Seul / Tokio (KST)',
    'America/New_York' => 'AQSh Sharqiy (ET) — Nyu-York',
    'America/Chicago' => 'AQSh Markaziy (CT) — Chikago',
    'America/Denver' => 'AQSh Tog‘li (MT) — Denver',
    'America/Los_Angeles' => 'AQSh Tinch okeani (PT) — Los-Anjeles',
];

/** Old rows hold labels like "America/New_York (EST)"; map them to a known key. */
function normalize_timezone(?string $tz): string
{
    $key = explode(' ', trim((string) $tz))[0];
    return isset(TIMEZONES[$key]) ? $key : 'America/New_York';
}
