<?php

declare(strict_types=1);

/**
 * Duolingo-style onboarding after sign-up: why the learner wants Uzbek, where
 * they want to go, what they are into, their own level and a daily goal; then an
 * optional placement test built from the starter course vocabulary.
 */

const ONB_MOTIVES = [
    'family' => ['Oila va ildizlar', 'Bobo-buvim, qarindoshlarim bilan gaplashish', 'users'],
    'travel' => ['Sayohat', 'O‘zbekistonga borib, o‘zim gaplashish', 'map-pin'],
    'work' => ['Ish va biznes', 'Hamkorlar, mijozlar bilan muloqot', 'target'],
    'culture' => ['Madaniyat va tarix', 'Ipak yo‘li, me’morchilik, urf-odatlar', 'registon'],
    'friends' => ['Do‘stlar va yaqinlar', 'Do‘stim yoki turmush o‘rtog‘im o‘zbek', 'heart'],
    'study' => ['O‘qish va maktab', 'Maktab, universitet yoki imtihon uchun', 'book-open'],
    'growth' => ['O‘zimni rivojlantirish', 'Yangi til — miyaga mashq', 'sparkles'],
];

const ONB_PLACES = [
    'tashkent' => ['Toshkent', 'Poytaxt, metro, Chorsu bozori'],
    'samarkand' => ['Samarqand', 'Registon, Shohi Zinda, Go‘ri Amir'],
    'bukhara' => ['Buxoro', 'Minorai Kalon, Ark, Lyabi Hovuz'],
    'khiva' => ['Xiva', 'Ichan qal’a — ochiq osmon ostidagi muzey'],
    'fergana' => ['Farg‘ona vodiysi', 'Marg‘ilon atlasi, Rishton kulolchiligi'],
    'shahrisabz' => ['Shahrisabz', 'Amir Temur vatani, Oqsaroy'],
    'mountains' => ['Chimyon va Chorvoq', 'Tog‘lar, ko‘l, chang‘i'],
    'karakalpak' => ['Qoraqalpog‘iston', 'Nukus Savitskiy muzeyi, Orol'],
    'notsure' => ['Hali bilmayman', 'Avval tilni o‘rganib olay'],
];

const ONB_INTERESTS = [
    'crafts' => ['Hunarmandchilik', 'Kulolchilik, atlas, zardo‘zlik, o‘ymakorlik', 'palette'],
    'food' => ['Milliy taomlar', 'Osh, somsa, non, choy', 'heart'],
    'music' => ['Musiqa va raqs', 'Doira, maqom, lazgi', 'volume-2'],
    'history' => ['Tarix va me’morchilik', 'Temuriylar, madrasalar, gumbazlar', 'registon'],
    'nature' => ['Tabiat va tog‘lar', 'Tog‘lar, cho‘l, ko‘llar', 'map-pin'],
    'holidays' => ['Bayram va urf-odatlar', 'Navro‘z, to‘ylar, mehmondo‘stlik', 'sparkles'],
    'literature' => ['Adabiyot va ertaklar', 'Navoiy, Nasriddin Afandi', 'book-open'],
    'sport' => ['Sport', 'Kurash, futbol, uloq', 'trophy'],
    'trade' => ['Bozor va savdo', 'Savdolashish, ziravorlar, mevalar', 'coins'],
    'cinema' => ['Kino va multfilmlar', 'O‘zbek filmlari, qo‘shiqlar', 'video'],
];

const ONB_LEVELS = [
    'none' => ['Umuman bilmayman', 'Noldan boshlayman', 'A0'],
    'words' => ['Bir nechta so‘z bilaman', '«Salom», «rahmat» kabi', 'A0'],
    'basic' => ['Oddiy suhbat qila olaman', 'Tushunaman, lekin gapirishda qiynalaman', 'A1'],
    'good' => ['Yaxshi gapiraman', 'O‘qish va yozishni mustahkamlamoqchiman', 'A2'],
];

const ONB_GOALS = [
    20 => ['5 daqiqa', 'Yengil', '20 XP'],
    50 => ['10 daqiqa', 'Odatiy', '50 XP'],
    80 => ['15 daqiqa', 'Jiddiy', '80 XP'],
    120 => ['20 daqiqa', 'Kuchli', '120 XP'],
];

function decode_list(?string $json, array $allowed): array
{
    return array_values(array_intersect(json_decode((string) $json, true) ?: [], array_keys($allowed)));
}

/** Course ids ordered by how well they match the learner's answers. */
function recommended_courses(array $user): array
{
    $motives = decode_list($user['motives_json'] ?? null, ONB_MOTIVES);
    $interests = decode_list($user['interests_json'] ?? null, ONB_INTERESTS);
    $places = decode_list($user['places_json'] ?? null, ONB_PLACES);
    $scored = [];
    foreach (published_courses() as $c) {
        $score = 0;
        $isTravel = str_contains(mb_strtolower($c['title']), 'sayohat');
        if ($isTravel) {
            $score += in_array('travel', $motives, true) ? 5 : 0;
            $score += count(array_diff($places, ['notsure'])) > 0 ? 2 : 0;
            $score += in_array('trade', $interests, true) ? 1 : 0;
        } elseif ($c['category'] === 'culture') {
            $score += count(array_intersect($motives, ['culture', 'family'])) * 2;
            $score += count(array_intersect($interests, ['crafts', 'food', 'history', 'holidays', 'music', 'literature']));
        } else {
            $score += 3; // the starter course is always a good base
            $score += count(array_intersect($motives, ['family', 'study', 'friends', 'growth']));
        }
        $scored[] = ['course' => $c, 'score' => $score];
    }
    usort($scored, fn ($a, $b) => [$b['score'], -$a['course']['sort_order']] <=> [$a['score'], -$b['course']['sort_order']]);
    return array_map(fn ($s) => $s['course'], $scored);
}

/** Two vocabulary questions per starter-course lesson, easiest lesson first. */
function build_placement(): array
{
    $pdo = db();
    $starter = $pdo->query("SELECT id FROM courses WHERE status = 'published' AND category = 'language' ORDER BY sort_order, id LIMIT 1")->fetchColumn();
    if (!$starter) {
        return [];
    }
    $lessons = course_lessons((int) $starter);
    $allWords = $pdo->query('SELECT word_en FROM vocabulary')->fetchAll(PDO::FETCH_COLUMN);
    $questions = [];
    foreach ($lessons as $l) {
        $stmt = $pdo->prepare('SELECT word_uz, word_en FROM vocabulary WHERE lesson_id = ? ORDER BY random() LIMIT 2');
        $stmt->execute([$l['id']]);
        foreach ($stmt->fetchAll() as $v) {
            $wrong = array_values(array_diff($allWords, [$v['word_en']]));
            shuffle($wrong);
            $options = array_merge([$v['word_en']], array_slice($wrong, 0, 3));
            shuffle($options);
            $questions[] = [
                'lesson_id' => (int) $l['id'],
                'word' => $v['word_uz'],
                'options' => $options,
                'answer' => array_search($v['word_en'], $options, true),
            ];
        }
    }
    return $questions;
}

function require_student_login(): array
{
    if (!auth_check()) {
        redirect('/login');
    }
    return auth_user();
}

function page_onboarding(): void
{
    $user = require_student_login();
    render('onboarding', [
        'title' => t('Keling, tanishaylik — LingoUz'),
        'user' => $user,
        'motives' => decode_list($user['motives_json'], ONB_MOTIVES),
        'places' => decode_list($user['places_json'], ONB_PLACES),
        'interests' => decode_list($user['interests_json'], ONB_INTERESTS),
    ]);
}

function action_onboarding(): void
{
    $user = require_student_login();
    $pick = fn (string $key, array $allowed) => array_values(array_intersect((array) ($_POST[$key] ?? []), array_keys($allowed)));
    $selfLevel = isset(ONB_LEVELS[$_POST['self_level'] ?? '']) ? $_POST['self_level'] : 'none';
    $goal = isset(ONB_GOALS[(int) ($_POST['daily_goal'] ?? 0)]) ? (int) $_POST['daily_goal'] : 50;

    db()->prepare('
        UPDATE users SET motives_json = ?, places_json = ?, interests_json = ?, self_level = ?, daily_goal_xp = ?,
                         language_level = ?, onboarded_at = COALESCE(onboarded_at, datetime(\'now\'))
        WHERE id = ?
    ')->execute([
        json_encode($pick('motives', ONB_MOTIVES)),
        json_encode($pick('places', ONB_PLACES)),
        json_encode($pick('interests', ONB_INTERESTS)),
        $selfLevel,
        $goal,
        ONB_LEVELS[$selfLevel][2],
        $user['id'],
    ]);

    redirect($selfLevel === 'none' ? '/onboarding/done' : '/onboarding/placement');
}

function action_onboarding_skip(): void
{
    $user = require_student_login();
    db()->prepare("UPDATE users SET onboarded_at = COALESCE(onboarded_at, datetime('now')) WHERE id = ?")->execute([$user['id']]);
    redirect('/dashboard');
}

function page_placement(): void
{
    require_student_login();
    $questions = build_placement();
    if (!$questions) {
        redirect('/onboarding/done');
    }
    $_SESSION['placement'] = $questions;
    render('placement', ['title' => t('Daraja testi — LingoUz'), 'questions' => $questions]);
}

function action_placement(): void
{
    $user = require_student_login();
    $questions = $_SESSION['placement'] ?? [];
    unset($_SESSION['placement']);
    if (!$questions) {
        redirect('/onboarding/placement');
    }

    $correct = 0;
    $perLesson = [];
    foreach ($questions as $i => $q) {
        $ok = isset($_POST['p_' . $i]) && (int) $_POST['p_' . $i] === (int) $q['answer'];
        $correct += $ok ? 1 : 0;
        $perLesson[$q['lesson_id']] = ($perLesson[$q['lesson_id']] ?? true) && $ok;
    }
    $percent = (int) round($correct / count($questions) * 100);
    $level = $percent >= 80 ? 'A2' : ($percent >= 40 ? 'A1' : 'A0');

    // Lessons answered fully right, in order from the first, are known: open the next one.
    $unlocked = 0;
    $pdo = db();
    $open = $pdo->prepare('
        INSERT INTO lesson_progress (user_id, lesson_id, status, manual_override) VALUES (?, ?, \'available\', 1)
        ON CONFLICT(user_id, lesson_id) DO UPDATE SET manual_override = 1
    ');
    $lessonIds = array_keys($perLesson);
    foreach ($lessonIds as $idx => $lessonId) {
        if (!$perLesson[$lessonId]) {
            break;
        }
        if (isset($lessonIds[$idx + 1])) {
            $open->execute([$user['id'], $lessonIds[$idx + 1]]);
            $unlocked++;
        }
    }

    $pdo->prepare('UPDATE users SET placement_score = ?, language_level = ? WHERE id = ?')->execute([$percent, $level, $user['id']]);
    $_SESSION['placement_result'] = ['percent' => $percent, 'correct' => $correct, 'total' => count($questions), 'level' => $level, 'unlocked' => $unlocked];
    redirect('/onboarding/done');
}

function page_onboarding_done(): void
{
    $user = require_student_login();
    $stmt = db()->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$user['id']]);
    $user = $stmt->fetch();
    $result = $_SESSION['placement_result'] ?? null;
    unset($_SESSION['placement_result']);
    render('onboarding_done', [
        'title' => t('Tayyor! — LingoUz'),
        'user' => $user,
        'result' => $result,
        'recommended' => array_slice(recommended_courses($user), 0, 3),
    ]);
}

/** Today's XP (from quiz attempts) for the daily goal ring. */
function today_xp(int $userId): int
{
    $stmt = db()->prepare("SELECT COALESCE(SUM(xp_earned), 0) FROM quiz_attempts WHERE user_id = ? AND date(created_at) = date('now')");
    $stmt->execute([$userId]);
    return (int) $stmt->fetchColumn();
}

/** One row per active user per day, written at most once per session per day. */
function track_activity(): void
{
    $user = auth_user();
    if (!$user) {
        return;
    }
    $today = gmdate('Y-m-d');
    if (($_SESSION['seen_day'] ?? '') === $today) {
        return;
    }
    $_SESSION['seen_day'] = $today;
    db()->prepare('INSERT OR IGNORE INTO activity_days (user_id, day) VALUES (?, ?)')->execute([$user['id'], $today]);
    db()->prepare("UPDATE users SET last_seen_at = datetime('now') WHERE id = ?")->execute([$user['id']]);
}
