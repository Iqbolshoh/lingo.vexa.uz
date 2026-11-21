<?php

declare(strict_types=1);

// LingoUz by Gulomov's Academy - public and student route handlers

function require_login(string $message = 'Iltimos, avval tizimga kiring.'): array
{
    if (!auth_check()) {
        flash('info', $message);
        redirect('/login');
    }
    return auth_user();
}

function not_found(string $title = 'Sahifa topilmadi'): void
{
    http_response_code(404);
    render('not_found', ['title' => $title]);
}

function client_ip(): string
{
    return (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
}

function page_home(): void
{
    $pdo = db();
    $courses = [];
    foreach (published_courses() as $course) {
        $course['lessons'] = course_lessons((int) $course['id']);
        $courses[] = $course;
    }
    $sampleVocab = $pdo->query('SELECT v.* FROM vocabulary v JOIN lessons l ON l.id = v.lesson_id ORDER BY l.course_id, l.position, v.id LIMIT 4')->fetchAll();
    $culture = $pdo->query("
        SELECT l.* FROM lessons l JOIN courses c ON c.id = l.course_id
        WHERE c.category = 'culture' AND c.status = 'published' AND l.status = 'published'
        ORDER BY c.sort_order, l.position LIMIT 6
    ")->fetchAll();
    $stats = [
        'lessons' => (int) $pdo->query("SELECT count(*) FROM lessons WHERE status = 'published'")->fetchColumn(),
        'words' => (int) $pdo->query('SELECT count(*) FROM vocabulary')->fetchColumn(),
        'exercises' => (int) $pdo->query('SELECT count(*) FROM questions')->fetchColumn(),
    ];

    render('home', [
        'title' => 'LingoUz — O‘zbek tilini hamma uchun onlayn o‘rganish | Learn Uzbek online',
        'courses' => $courses,
        'sampleVocab' => $sampleVocab,
        'culture' => $culture,
        'stats' => $stats,
    ]);
}

function action_enroll(): void
{
    $parentName = trim($_POST['parent_name'] ?? '');
    $parentEmail = trim($_POST['parent_email'] ?? '');
    $parentPhone = trim($_POST['parent_phone'] ?? '');
    $childName = trim($_POST['child_name'] ?? '');
    $childAge = in_array($_POST['child_age'] ?? '', AGE_BANDS, true) ? $_POST['child_age'] : '7-8';
    $languageLevel = in_array($_POST['language_level'] ?? '', LEVELS, true) ? $_POST['language_level'] : 'A0';
    $timezone = normalize_timezone($_POST['timezone'] ?? '');
    $notes = mb_substr(trim($_POST['notes'] ?? ''), 0, 1000);
    $createAccount = !empty($_POST['create_account']);
    $password = (string) ($_POST['password'] ?? '');

    if ($parentName === '' || $childName === '' || !filter_var($parentEmail, FILTER_VALIDATE_EMAIL)) {
        flash('error', 'Iltimos, ota-ona ismi, to‘g‘ri email va bola ismini kiriting.');
        redirect('/#ariza');
    }
    if ($createAccount && mb_strlen($password) < 6) {
        flash('error', 'Akkaunt uchun parol kamida 6 ta belgidan iborat bo‘lishi kerak.');
        redirect('/#ariza');
    }

    $pdo = db();
    $pdo->prepare('
        INSERT INTO enrollments (parent_name, parent_email, parent_phone, child_name, child_age, language_level, timezone, notes, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, \'pending\')
    ')->execute([mb_substr($parentName, 0, 120), $parentEmail, mb_substr($parentPhone, 0, 40), mb_substr($childName, 0, 80), $childAge, $languageLevel, $timezone, $notes]);

    if ($createAccount) {
        $existing = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $existing->execute([$parentEmail]);
        if (!$existing->fetch()) {
            $pdo->prepare('
                INSERT INTO users (role, display_name, email, password_hash, age_band, language_level, timezone)
                VALUES (\'student\', ?, ?, ?, ?, ?, ?)
            ')->execute([mb_substr($childName, 0, 80), $parentEmail, password_hash($password, PASSWORD_DEFAULT), $childAge, $languageLevel, $timezone]);
            login_user(['id' => (int) $pdo->lastInsertId()]);
            flash('success', 'Arizangiz qabul qilindi va o‘quvchi profili yaratildi! Keling, tanishaylik.');
            redirect('/onboarding');
        }
        flash('info', 'Ariza qabul qilindi. Bu email bilan akkaunt allaqachon bor — tizimga kiring.');
        redirect('/login');
    }

    flash('success', 'Rahmat! Arizangiz qabul qilindi. Tez orada mentorlarimiz siz bilan bog‘lanishadi.');
    redirect('/#ariza');
}

function page_login(): void
{
    render('login', ['title' => 'Tizimga kirish — LingoUz', 'currentUser' => auth_user()]);
}

function action_login(): void
{
    $identifier = trim($_POST['email'] ?? '');
    $password = (string) ($_POST['password'] ?? '');
    $ip = client_ip();

    if (login_throttled($ip)) {
        flash('error', 'Juda ko‘p noto‘g‘ri urinish. 15 daqiqadan so‘ng qayta urinib ko‘ring.');
        redirect('/login');
    }
    if ($identifier === '' || $password === '') {
        flash('error', 'Email yoki login va parolni kiriting.');
        redirect('/login');
    }

    $altEmail = str_contains($identifier, '@') ? $identifier : $identifier . '@lingo.uz';
    $stmt = db()->prepare('SELECT * FROM users WHERE email = ? OR email = ?');
    $stmt->execute([$identifier, $altEmail]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        record_login_failure($ip, $identifier);
        flash('error', 'Login yoki parol noto‘g‘ri kiritildi.');
        redirect('/login');
    }

    clear_login_failures($ip);
    login_user($user);
    flash('success', 'Xush kelibsiz, ' . $user['display_name'] . '!');
    redirect($user['role'] === 'admin' ? '/admin' : '/dashboard');
}

function action_logout(): void
{
    logout_user();
    flash('info', 'Tizimdan muvaffaqiyatli chiqdingiz.');
    redirect('/login');
}

function page_register(): void
{
    if (auth_check()) {
        redirect('/dashboard');
    }
    render('register', ['title' => 'O‘quvchini ro‘yxatdan o‘tkazish — LingoUz']);
}

function action_register(): void
{
    $displayName = trim($_POST['display_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = (string) ($_POST['password'] ?? '');
    $ageBand = in_array($_POST['age_band'] ?? '', AGE_BANDS, true) ? $_POST['age_band'] : '7-8';
    $languageLevel = in_array($_POST['language_level'] ?? '', LEVELS, true) ? $_POST['language_level'] : 'A0';
    $timezone = normalize_timezone($_POST['timezone'] ?? '');

    if ($displayName === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($password) < 6) {
        flash('error', 'Ism, to‘g‘ri email va kamida 6 belgili parol kiriting.');
        redirect('/register');
    }

    $pdo = db();
    $check = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $check->execute([$email]);
    if ($check->fetch()) {
        flash('error', 'Bu email orqali allaqachon ro‘yxatdan o‘tilgan. Tizimga kiring.');
        redirect('/login');
    }

    $pdo->prepare('
        INSERT INTO users (role, display_name, email, password_hash, age_band, language_level, timezone)
        VALUES (\'student\', ?, ?, ?, ?, ?, ?)
    ')->execute([mb_substr($displayName, 0, 80), $email, password_hash($password, PASSWORD_DEFAULT), $ageBand, $languageLevel, $timezone]);
    login_user(['id' => (int) $pdo->lastInsertId()]);

    flash('success', 'Xush kelibsiz! Keling, avval bir-birimizni yaxshiroq tanib olaylik.');
    redirect('/onboarding');
}

function page_profile(): void
{
    $user = require_login();
    $stats = db()->prepare('
        SELECT count(*) AS attempts, sum(CASE WHEN passed = 1 THEN 1 ELSE 0 END) AS passed_count, max(percent) AS best_percent
        FROM quiz_attempts WHERE user_id = ?
    ');
    $stats->execute([$user['id']]);

    render('profile', [
        'title' => 'Mening Profilim — ' . $user['display_name'],
        'user' => $user,
        'stats' => $stats->fetch(),
    ]);
}

function action_update_profile(): void
{
    $user = require_login();
    $displayName = trim($_POST['display_name'] ?? '');
    $ageBand = in_array($_POST['age_band'] ?? '', AGE_BANDS, true) ? $_POST['age_band'] : $user['age_band'];
    $languageLevel = in_array($_POST['language_level'] ?? '', LEVELS, true) ? $_POST['language_level'] : $user['language_level'];
    $timezone = normalize_timezone($_POST['timezone'] ?? $user['timezone']);
    $newPassword = (string) ($_POST['new_password'] ?? '');

    if ($displayName === '' || mb_strlen($displayName) > 80) {
        flash('error', 'Ism 1–80 belgidan iborat bo‘lishi kerak.');
        redirect('/profile');
    }
    if ($newPassword !== '' && mb_strlen($newPassword) < 6) {
        flash('error', 'Yangi parol kamida 6 ta belgidan iborat bo‘lishi kerak.');
        redirect('/profile');
    }

    $pdo = db();
    $pdo->prepare('UPDATE users SET display_name = ?, age_band = ?, language_level = ?, timezone = ? WHERE id = ?')
        ->execute([$displayName, $ageBand, $languageLevel, $timezone, $user['id']]);
    if ($newPassword !== '') {
        $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([password_hash($newPassword, PASSWORD_DEFAULT), $user['id']]);
    }

    flash('success', 'Profilingiz yangilandi!');
    redirect('/profile');
}

function page_dashboard(): void
{
    $user = require_login('Iltimos, avval tizimga kiring yoki demo akkaunt orqali sinab ko‘ring.');
    if ($user['role'] === 'student' && empty($user['onboarded_at'])) {
        redirect('/onboarding');
    }
    $pdo = db();

    $courseStates = array_map(fn ($c) => course_state($c, (int) $user['id'], is_admin()), published_courses());
    $continue = null;
    foreach ($courseStates as $cs) {
        if ($cs['next']) {
            $continue = $cs['next'] + ['course_title' => $cs['course']['title']];
            break;
        }
    }

    $badges = $pdo->prepare('
        SELECT b.*, ub.earned_at FROM badges b
        LEFT JOIN user_badges ub ON ub.badge_code = b.code AND ub.user_id = ?
        ORDER BY ub.earned_at IS NULL, b.id
    ');
    $badges->execute([$user['id']]);

    $recent = $pdo->prepare('
        SELECT qa.*, l.title AS lesson_title FROM quiz_attempts qa JOIN lessons l ON l.id = qa.lesson_id
        WHERE qa.user_id = ? ORDER BY qa.id DESC LIMIT 5
    ');
    $recent->execute([$user['id']]);

    $words = $pdo->prepare("
        SELECT count(*) FROM vocabulary v JOIN lesson_progress lp ON lp.lesson_id = v.lesson_id
        WHERE lp.user_id = ? AND lp.status = 'completed'
    ");
    $words->execute([$user['id']]);

    $liveSession = $pdo->query("SELECT * FROM live_sessions WHERE status = 'scheduled' AND starts_at >= datetime('now', '-1 hour') ORDER BY starts_at LIMIT 1")->fetch();

    render('dashboard', [
        'title' => 'O‘quvchi Paneli — ' . $user['display_name'],
        'user' => $user,
        'courseStates' => $courseStates,
        'continue' => $continue,
        'badges' => $badges->fetchAll(),
        'recent' => $recent->fetchAll(),
        'wordsLearned' => (int) $words->fetchColumn(),
        'streak' => current_streak($user),
        'level' => xp_level((int) $user['xp']),
        'liveSession' => $liveSession,
        'todayXp' => today_xp((int) $user['id']),
        'recommended' => recommended_courses($user)[0] ?? null,
        'interests' => decode_list($user['interests_json'], ONB_INTERESTS),
    ]);
}

function page_courses(): void
{
    $user = require_login();
    render('courses', [
        'title' => 'Kurslar — LingoUz',
        'courseStates' => array_map(fn ($c) => course_state($c, (int) $user['id'], is_admin()), published_courses()),
    ]);
}

function page_course(int $courseId): void
{
    $user = require_login();
    $course = find_course($courseId);
    if (!$course || ($course['status'] !== 'published' && !is_admin())) {
        not_found('Kurs topilmadi');
        return;
    }
    render('course', [
        'title' => $course['title'] . ' — LingoUz',
        'state' => course_state($course, (int) $user['id'], is_admin()),
    ]);
}

/** Loads a lesson the user may open, or redirects with a friendly reason. */
function accessible_lesson(int $lessonId, array $user): ?array
{
    $lesson = find_lesson($lessonId);
    if (!$lesson || ($lesson['status'] !== 'published' && !is_admin())) {
        not_found('Dars topilmadi');
        return null;
    }
    $state = lesson_state_for_user($lesson, (int) $user['id'], is_admin());
    if ($state['user_status'] === 'locked') {
        $prev = $state['prev_lesson'];
        flash('error', 'Bu dars hali qulflangan. Avval «' . ($prev['title'] ?? 'oldingi dars') . '» testidan kamida ' . ($prev['passing_score'] ?? 80) . '% to‘plang.');
        redirect('/courses/' . $lesson['course_id']);
    }
    return $state;
}

function page_lesson(int $lessonId): void
{
    $user = require_login('Darsni ko‘rish uchun avval tizimga kiring.');
    $lesson = accessible_lesson($lessonId, $user);
    if (!$lesson) {
        return;
    }

    $vocab = db()->prepare('SELECT id, word_uz, word_en, pronunciation, example FROM vocabulary WHERE lesson_id = ? ORDER BY id');
    $vocab->execute([$lessonId]);
    $progress = user_progress_map((int) $user['id'])[$lessonId] ?? null;
    $course = find_course((int) $lesson['course_id']);

    render('lesson', [
        'title' => $lesson['title'] . ' — LingoUz',
        'lesson' => $lesson,
        'course' => $course,
        'lessonCount' => count(course_lessons((int) $lesson['course_id'])),
        'vocabulary' => $vocab->fetchAll(),
        'quiz' => lesson_quiz($lessonId),
        'questions' => lesson_questions($lessonId),
        'currentProgress' => $progress,
        'user' => $user,
    ]);
}

function action_submit_quiz(int $lessonId): void
{
    $user = require_login();
    $lesson = accessible_lesson($lessonId, $user);
    if (!$lesson) {
        return;
    }
    $pdo = db();
    $quiz = lesson_quiz($lessonId);
    $questions = lesson_questions($lessonId);
    if (!$questions) {
        flash('error', 'Bu darsda hali test savollari yo‘q.');
        redirect('/lessons/' . $lessonId);
    }

    $correct = 0;
    $details = [];
    foreach ($questions as $q) {
        [$ok, $chosen, $right] = grade_question($q, $_POST['q_' . $q['id']] ?? null);
        $correct += $ok ? 1 : 0;
        $details[] = [
            'question_id' => (int) $q['id'],
            'type' => $q['type'],
            'prompt' => $q['prompt'],
            'prompt_en' => $q['prompt_en'],
            'chosen' => $chosen,
            'correct' => $right,
            'is_correct' => $ok,
            'explanation' => $q['explanation'],
        ];
    }

    $total = count($questions);
    $percent = (int) round($correct / $total * 100);
    $passScore = (int) $lesson['passing_score'];
    $passed = $percent >= $passScore;
    $wasCompleted = $lesson['user_status'] === 'completed';

    // XP: points per correct answer, a bonus the first time the lesson is passed.
    $xp = $correct * XP_PER_CORRECT + ($passed && !$wasCompleted ? XP_PASS_BONUS : 0);

    $pdo->beginTransaction();
    $pdo->prepare('
        INSERT INTO quiz_attempts (user_id, quiz_id, lesson_id, score, total, percent, passed, details_json, xp_earned)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ')->execute([$user['id'], $quiz['id'], $lessonId, $correct, $total, $percent, $passed ? 1 : 0, json_encode($details, JSON_UNESCAPED_UNICODE), $xp]);
    $attemptId = (int) $pdo->lastInsertId();

    $status = ($wasCompleted || $passed) ? 'completed' : 'available';
    $pdo->prepare('
        INSERT INTO lesson_progress (user_id, lesson_id, status, best_score, completed_at)
        VALUES (?, ?, ?, ?, CASE WHEN ? = \'completed\' THEN datetime(\'now\') END)
        ON CONFLICT(user_id, lesson_id) DO UPDATE SET
            status = excluded.status,
            best_score = MAX(best_score, excluded.best_score),
            completed_at = COALESCE(completed_at, excluded.completed_at)
    ')->execute([$user['id'], $lessonId, $status, $percent, $status]);

    $reward = reward_user((int) $user['id'], $xp);
    $pdo->commit();

    if ($passed) {
        award_badge((int) $user['id'], 'smart_kid');
        if ((int) $lesson['position'] === 1 && (int) $lesson['course_id'] === 1) {
            award_badge((int) $user['id'], 'first_step');
        }
        if ($percent === 100) {
            award_badge((int) $user['id'], 'perfect_score');
        }
        $course = find_course((int) $lesson['course_id']);
        if (($course['category'] ?? '') === 'culture') {
            award_badge((int) $user['id'], 'culture_explorer');
        }
        $state = course_state($course, (int) $user['id']);
        $allDone = (int) $pdo->query("SELECT count(*) FROM lesson_progress WHERE user_id = {$user['id']} AND status = 'completed'")->fetchColumn();
        if ($allDone >= 3) {
            award_badge((int) $user['id'], 'halfway_hero');
        }
        if ((int) $course['id'] === 1 && $state['completed'] === $state['total']) {
            award_badge((int) $user['id'], 'lingo_master');
        }
    }
    if ($reward['streak'] >= 3) {
        award_badge((int) $user['id'], 'streak_3');
    }
    if ($reward['xp'] >= 500) {
        award_badge((int) $user['id'], 'xp_500');
    }

    redirect("/lessons/{$lessonId}/result?attempt={$attemptId}");
}

function action_game_complete(int $lessonId): void
{
    $user = require_login();
    $lesson = accessible_lesson($lessonId, $user);
    if (!$lesson) {
        return;
    }
    $pdo = db();
    $pdo->prepare('INSERT OR IGNORE INTO lesson_progress (user_id, lesson_id, status) VALUES (?, ?, \'available\')')->execute([$user['id'], $lessonId]);
    $stmt = $pdo->prepare('UPDATE lesson_progress SET game_done = 1 WHERE user_id = ? AND lesson_id = ? AND game_done = 0');
    $stmt->execute([$user['id'], $lessonId]);
    $awarded = $stmt->rowCount() > 0 ? GAME_COINS : 0;
    if ($awarded) {
        reward_user((int) $user['id'], 5, $awarded);
    }
    json_response(['ok' => true, 'coins_awarded' => $awarded, 'coins' => (int) $pdo->query("SELECT coins FROM users WHERE id = {$user['id']}")->fetchColumn()]);
}

function page_quiz_result(int $lessonId): void
{
    $user = require_login();
    $lesson = find_lesson($lessonId);
    if (!$lesson) {
        not_found('Dars topilmadi');
        return;
    }
    $attemptId = (int) ($_GET['attempt'] ?? 0);
    $stmt = db()->prepare('
        SELECT * FROM quiz_attempts WHERE user_id = ? AND lesson_id = ? AND (? = 0 OR id = ?)
        ORDER BY id DESC LIMIT 1
    ');
    $stmt->execute([$user['id'], $lessonId, $attemptId, $attemptId]);
    $attempt = $stmt->fetch();
    if (!$attempt) {
        redirect("/lessons/{$lessonId}");
    }

    render('quiz_result', [
        'title' => 'Test Natijasi — LingoUz',
        'lesson' => $lesson,
        'course' => find_course((int) $lesson['course_id']),
        'result' => [
            'score' => (int) $attempt['score'],
            'total' => (int) $attempt['total'],
            'percent' => (int) $attempt['percent'],
            'passed' => (bool) $attempt['passed'],
            'xp' => (int) $attempt['xp_earned'],
            'details' => json_decode((string) $attempt['details_json'], true) ?: [],
        ],
        'nextLesson' => next_lesson_in_course($lesson),
    ]);
}

function page_live(): void
{
    $user = require_login('Jonli muloqot xonasiga kirish uchun avval tizimga kiring.');
    $sessions = db()->query("
        SELECT * FROM live_sessions WHERE status = 'scheduled' AND starts_at >= datetime('now', '-1 hour')
        ORDER BY starts_at LIMIT 8
    ")->fetchAll();
    award_badge((int) $user['id'], 'speaker');

    render('live', [
        'title' => 'Jonli Muloqot Xonasi (Zoom) — LingoUz',
        'sessions' => $sessions,
        'user' => $user,
    ]);
}

function page_privacy(): void
{
    render('privacy', ['title' => 'Bolalar Maxfiyligi va Xavfsizlik Siyosati (COPPA) — LingoUz']);
}

function page_certificate(): void
{
    $user = require_login('Diplomni ko‘rish uchun avval tizimga kiring.');
    $courseId = (int) ($_GET['course'] ?? 1);
    $course = find_course($courseId);
    if (!$course) {
        not_found('Kurs topilmadi');
        return;
    }
    $state = course_state($course, (int) $user['id']);
    if (($state['total'] === 0 || $state['completed'] < $state['total']) && !is_admin()) {
        render('certificate_locked', [
            'title' => 'Diplom hali yopiq — LingoUz',
            'state' => $state,
        ]);
        return;
    }
    $completedAt = db()->prepare('
        SELECT MAX(lp.completed_at) FROM lesson_progress lp JOIN lessons l ON l.id = lp.lesson_id
        WHERE lp.user_id = ? AND l.course_id = ?
    ');
    $completedAt->execute([$user['id'], $courseId]);

    render('certificate', [
        'title' => 'Diplom — ' . $user['display_name'],
        'student' => $user,
        'course' => $course,
        'state' => $state,
        'completedAt' => $completedAt->fetchColumn() ?: date('Y-m-d'),
    ]);
}

function page_parent_tips(): void
{
    render('parent_tips', ['title' => 'Samarali o‘rganish qo‘llanmasi — LingoUz']);
}
