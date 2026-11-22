<?php

declare(strict_types=1);

// LingoUz - public and student route handlers

function require_login(string $message = 'Iltimos, avval tizimga kiring.'): array
{
    if (!auth_check()) {
        flash('info', t($message));
        app_redirect('/login');
    }
    return auth_user();
}

function not_found(string $title = 'Sahifa topilmadi'): void
{
    http_response_code(404);
    render('not_found', ['title' => t($title)]);
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
        'title' => t('LingoUz — O‘zbek tilini hamma uchun onlayn o‘rganish | Learn Uzbek online'),
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
        flash('error', t('Iltimos, ota-ona ismi, to‘g‘ri email va bola ismini kiriting.'));
        app_redirect('/register');
    }
    if ($createAccount && mb_strlen($password) < 6) {
        flash('error', t('Akkaunt uchun parol kamida 6 ta belgidan iborat bo‘lishi kerak.'));
        app_redirect('/register');
    }

    $pdo = db();
    $pdo->prepare('
        INSERT INTO enrollments (parent_name, parent_email, parent_phone, child_name, child_age, language_level, timezone, notes, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, \'pending\')
    ')->execute([mb_substr($parentName, 0, 120), $parentEmail, mb_substr($parentPhone, 0, 40), mb_substr($childName, 0, 80), $childAge, $languageLevel, $timezone, $notes]);

    // Notify developer group about new enrollment
    try {
        telegram_notify_new_enrollment([
            'parent_name' => $parentName,
            'parent_email' => $parentEmail,
            'parent_phone' => $parentPhone,
            'child_name' => $childName,
            'child_age' => $childAge,
            'language_level' => $languageLevel,
            'notes' => $notes,
        ]);
    } catch (\Throwable $e) {}

    if ($createAccount) {
        $existing = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $existing->execute([$parentEmail]);
        if (!$existing->fetch()) {
            $pdo->prepare('
                INSERT INTO users (role, display_name, email, password_hash, age_band, language_level, timezone)
                VALUES (\'student\', ?, ?, ?, ?, ?, ?)
            ')->execute([mb_substr($childName, 0, 80), $parentEmail, password_hash($password, PASSWORD_DEFAULT), $childAge, $languageLevel, $timezone]);
            login_user(['id' => (int) $pdo->lastInsertId()]);
            flash('success', t('Arizangiz qabul qilindi va o‘quvchi profili yaratildi! Keling, tanishaylik.'));
            app_redirect('/onboarding');
        }
        flash('info', t('Ariza qabul qilindi. Bu email bilan akkaunt allaqachon bor — tizimga kiring.'));
        app_redirect('/login');
    }

    flash('success', t('Rahmat! Arizangiz qabul qilindi. Tez orada mentorlarimiz siz bilan bog‘lanishadi.'));
    app_redirect('/');
}

function page_login(): void
{
    render('login', ['title' => t('Tizimga kirish — LingoUz'), 'currentUser' => auth_user()]);
}

function action_login(): void
{
    $identifier = trim($_POST['email'] ?? '');
    $password = (string) ($_POST['password'] ?? '');
    $ip = client_ip();

    if (login_throttled($ip)) {
        flash('error', t('Juda ko‘p noto‘g‘ri urinish. 15 daqiqadan so‘ng qayta urinib ko‘ring.'));
        app_redirect('/login');
    }
    if ($identifier === '' || $password === '') {
        flash('error', t('Email yoki login va parolni kiriting.'));
        app_redirect('/login');
    }

    $altEmail = str_contains($identifier, '@') ? $identifier : $identifier . '@lingo.uz';
    $stmt = db()->prepare('SELECT * FROM users WHERE email = ? OR email = ?');
    $stmt->execute([$identifier, $altEmail]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        record_login_failure($ip, $identifier);
        flash('error', t('Login yoki parol noto‘g‘ri kiritildi.'));
        app_redirect('/login');
    }

    clear_login_failures($ip);
    login_user($user);
    flash('success', t('Xush kelibsiz, :name!', ['name' => $user['display_name']], false));
    app_redirect($user['role'] === 'admin' ? '/admin' : '/dashboard');
}

function action_logout(): void
{
    logout_user();
    flash('info', t('Tizimdan muvaffaqiyatli chiqdingiz.'));
    app_redirect('/login');
}

function page_register(): void
{
    if (auth_check()) {
        app_redirect('/dashboard');
    }
    render('register', ['title' => t('O‘quvchini ro‘yxatdan o‘tkazish — LingoUz')]);
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
        flash('error', t('Ism, to‘g‘ri email va kamida 6 belgili parol kiriting.'));
        app_redirect('/register');
    }

    $pdo = db();
    $check = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $check->execute([$email]);
    if ($check->fetch()) {
        flash('error', t('Bu email orqali allaqachon ro‘yxatdan o‘tilgan. Tizimga kiring.'));
        app_redirect('/login');
    }

    $pdo->prepare('
        INSERT INTO users (role, display_name, email, password_hash, age_band, language_level, timezone)
        VALUES (\'student\', ?, ?, ?, ?, ?, ?)
    ')->execute([mb_substr($displayName, 0, 80), $email, password_hash($password, PASSWORD_DEFAULT), $ageBand, $languageLevel, $timezone]);
    login_user(['id' => (int) $pdo->lastInsertId()]);

    flash('success', t('Xush kelibsiz! Keling, avval bir-birimizni yaxshiroq tanib olaylik.'));
    app_redirect('/onboarding');
}

function page_profile(): void
{
    $pdo = db();
    $targetId = isset($_GET['u']) ? (int) $_GET['u'] : 0;
    $targetUser = null;
    if ($targetId > 0) {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$targetId]);
        $targetUser = $stmt->fetch() ?: null;
    }

    if (!$targetUser) {
        $targetUser = require_login();
    }
    $currentUser = auth_user();

    $stats = $pdo->prepare('
        SELECT count(*) AS attempts, sum(CASE WHEN passed = 1 THEN 1 ELSE 0 END) AS passed_count, max(percent) AS best_percent
        FROM quiz_attempts WHERE user_id = ?
    ');
    $stats->execute([$targetUser['id']]);

    render('profile', [
        'title' => t('Mening Profilim — ') . $targetUser['display_name'],
        'user' => $targetUser,
        'currentUser' => $currentUser,
        'stats' => $stats->fetch(),
        'ogTitle' => $targetUser['display_name'] . ' — LingoUz O‘quvchi Profili',
        'ogDescription' => "⭐ {$targetUser['xp']} XP  |  🔥 {$targetUser['streak_days']} kunlik streak. LingoUz orqali o‘zbek tilini o‘rganing!",
        'ogImage' => "https://lingo.vexa.uz/og/profile/{$targetUser['id']}.png?v=" . time(),
        'ogUrl' => "https://lingo.vexa.uz/profile?u={$targetUser['id']}",
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
        flash('error', t('Ism 1–80 belgidan iborat bo‘lishi kerak.'));
        app_redirect('/profile');
    }
    if ($newPassword !== '' && mb_strlen($newPassword) < 6) {
        flash('error', t('Yangi parol kamida 6 ta belgidan iborat bo‘lishi kerak.'));
        app_redirect('/profile');
    }

    $pdo = db();
    $pdo->prepare('UPDATE users SET display_name = ?, age_band = ?, language_level = ?, timezone = ? WHERE id = ?')
        ->execute([$displayName, $ageBand, $languageLevel, $timezone, $user['id']]);
    if ($newPassword !== '') {
        $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([password_hash($newPassword, PASSWORD_DEFAULT), $user['id']]);
    }

    flash('success', t('Profilingiz yangilandi!'));
    app_redirect('/profile');
}

function page_dashboard(): void
{
    $user = require_login(t('Iltimos, avval tizimga kiring yoki demo akkaunt orqali sinab ko‘ring.'));
    if ($user['role'] === 'student' && empty($user['onboarded_at'])) {
        app_redirect('/onboarding');
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
        SELECT qa.*, l.title AS lesson_title, l.title_en AS lesson_title_en FROM quiz_attempts qa JOIN lessons l ON l.id = qa.lesson_id
        WHERE qa.user_id = ? ORDER BY qa.id DESC LIMIT 5
    ');
    $recent->execute([$user['id']]);

    $words = $pdo->prepare("
        SELECT count(*) FROM vocabulary v JOIN lesson_progress lp ON lp.lesson_id = v.lesson_id
        WHERE lp.user_id = ? AND lp.status = 'completed'
    ");
    $words->execute([$user['id']]);

    $liveSession = $pdo->query("SELECT * FROM live_sessions WHERE status = 'scheduled' AND starts_at >= datetime('now', '-1 hour') ORDER BY starts_at LIMIT 1")->fetch();

    $leadersStmt = $pdo->query('
        SELECT u.id, u.display_name, u.xp, u.streak_days, u.language_level,
               (SELECT count(*) FROM lesson_progress lp WHERE lp.user_id = u.id AND lp.status = "completed") as completed_lessons
        FROM users u
        ORDER BY u.xp DESC, completed_lessons DESC, u.streak_days DESC, u.id ASC
        LIMIT 5
    ');
    $topLeaders = $leadersStmt->fetchAll();
    $myRank = 1;
    $found = false;
    foreach ($topLeaders as $idx => $l) {
        if ((int) $l['id'] === (int) $user['id']) {
            $myRank = $idx + 1;
            $found = true;
            break;
        }
    }
    if (!$found) {
        $rankStmt = $pdo->prepare('SELECT count(*) + 1 FROM users WHERE xp > ? OR (xp = ? AND id < ?)');
        $rankStmt->execute([(int) $user['xp'], (int) $user['xp'], (int) $user['id']]);
        $myRank = (int) $rankStmt->fetchColumn();
    }

    render('dashboard', [
        'title' => t('O‘quvchi Paneli — ') . $user['display_name'],
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
        'topLeaders' => $topLeaders,
        'myRank' => $myRank,
        'telegramLinked' => !empty($user['telegram_chat_id']),
        'telegramLinkUrl' => get_telegram_link_url($user),
        'telegramUsername' => (string) ($user['telegram_username'] ?? ''),
        'telegramFirstName' => (string) ($user['telegram_first_name'] ?? ''),
    ]);
}

function page_courses(): void
{
    $user = require_login();
    render('courses', [
        'title' => t('Kurslar — LingoUz'),
        'courseStates' => array_map(fn ($c) => course_state($c, (int) $user['id'], is_admin()), published_courses()),
    ]);
}

function page_course(int $courseId): void
{
    $user = require_login();
    $course = find_course($courseId);
    if (!$course || ($course['status'] !== 'published' && !is_admin())) {
        not_found(t('Kurs topilmadi'));
        return;
    }
    render('course', [
        'title' => loc($course, 'title') . ' — LingoUz',
        'state' => course_state($course, (int) $user['id'], is_admin()),
    ]);
}

/** Loads a lesson the user may open, or redirects with a friendly reason. */
function accessible_lesson(int $lessonId, array $user): ?array
{
    $lesson = find_lesson($lessonId);
    if (!$lesson || ($lesson['status'] !== 'published' && !is_admin())) {
        not_found(t('Dars topilmadi'));
        return null;
    }
    $state = lesson_state_for_user($lesson, (int) $user['id'], is_admin());
    if ($state['user_status'] === 'locked') {
        $prev = $state['prev_lesson'];
        flash('error', t('Bu dars hali qulflangan. Avval «:title» testidan kamida :score% to‘plang.', ['title' => $prev ? loc($prev, 'title') : t('oldingi dars'), 'score' => $prev['passing_score'] ?? 80], false));
        app_redirect('/courses/' . $lesson['course_id']);
    }
    return $state;
}

function page_lesson(int $lessonId): void
{
    $user = require_login(t('Darsni ko‘rish uchun avval tizimga kiring.'));
    $lesson = accessible_lesson($lessonId, $user);
    if (!$lesson) {
        return;
    }

    $vocab = db()->prepare('SELECT id, word_uz, word_en, pronunciation, example FROM vocabulary WHERE lesson_id = ? ORDER BY id');
    $vocab->execute([$lessonId]);
    $progress = user_progress_map((int) $user['id'])[$lessonId] ?? null;
    $course = find_course((int) $lesson['course_id']);

    render('lesson', [
        'title' => loc($lesson, 'title') . ' — LingoUz',
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
        flash('error', t('Bu darsda hali test savollari yo‘q.'));
        app_redirect('/lessons/' . $lessonId);
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
        $allDoneStmt = $pdo->prepare("SELECT count(*) FROM lesson_progress WHERE user_id = ? AND status = 'completed'");
        $allDoneStmt->execute([(int) $user['id']]);
        $allDone = (int) $allDoneStmt->fetchColumn();
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

    app_redirect("/lessons/{$lessonId}/result?attempt={$attemptId}");
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
    $coinStmt = $pdo->prepare("SELECT coins FROM users WHERE id = ?");
    $coinStmt->execute([(int) $user['id']]);
    $currentCoins = (int) $coinStmt->fetchColumn();
    json_response(['ok' => true, 'coins_awarded' => $awarded, 'coins' => $currentCoins]);
}

function page_quiz_result(int $lessonId): void
{
    $user = require_login();
    $lesson = find_lesson($lessonId);
    if (!$lesson) {
        not_found(t('Dars topilmadi'));
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
        app_redirect("/lessons/{$lessonId}");
    }

    render('quiz_result', [
        'title' => t('Test Natijasi — LingoUz'),
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
    $user = require_login(t('Jonli muloqot xonasiga kirish uchun avval tizimga kiring.'));
    $sessions = db()->query("
        SELECT * FROM live_sessions WHERE status = 'scheduled' AND starts_at >= datetime('now', '-1 hour')
        ORDER BY starts_at LIMIT 8
    ")->fetchAll();
    award_badge((int) $user['id'], 'speaker');

    render('live', [
        'title' => t('Jonli Muloqot Xonasi (Zoom) — LingoUz'),
        'sessions' => $sessions,
        'user' => $user,
    ]);
}

function page_privacy(): void
{
    render('privacy', ['title' => t('Bolalar Maxfiyligi va Xavfsizlik Siyosati (COPPA) — LingoUz')]);
}

function page_certificate(): void
{
    $user = require_login(t('Diplomni ko‘rish uchun avval tizimga kiring.'));
    $courseId = (int) ($_GET['course'] ?? 1);
    $course = find_course($courseId);
    if (!$course) {
        not_found(t('Kurs topilmadi'));
        return;
    }
    $state = course_state($course, (int) $user['id']);
    if (($state['total'] === 0 || $state['completed'] < $state['total']) && !is_admin()) {
        render('certificate_locked', [
            'title' => t('Diplom hali yopiq — LingoUz'),
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
        'title' => t('Diplom — ') . $user['display_name'],
        'student' => $user,
        'course' => $course,
        'state' => $state,
        'completedAt' => $completedAt->fetchColumn() ?: date('Y-m-d'),
    ]);
}

function page_parent_tips(): void
{
    render('parent_tips', ['title' => t('Samarali o‘rganish qo‘llanmasi — LingoUz')]);
}

function page_leaderboard(): void
{
    $pdo = db();
    $user = auth_user();

    $stmt = $pdo->query('
        SELECT 
            u.id, 
            u.display_name, 
            u.xp, 
            u.streak_days, 
            u.language_level,
            (SELECT count(*) FROM lesson_progress lp WHERE lp.user_id = u.id AND lp.status = "completed") as completed_lessons,
            (SELECT count(*) FROM user_badges ub WHERE ub.user_id = u.id) as badge_count
        FROM users u
        ORDER BY u.xp DESC, completed_lessons DESC, u.streak_days DESC, u.id ASC
        LIMIT 30
    ');
    $leaders = $stmt->fetchAll();

    $myRank = null;
    if ($user) {
        foreach ($leaders as $idx => $l) {
            if ((int) $l['id'] === (int) $user['id']) {
                $myRank = $idx + 1;
                break;
            }
        }
        if ($myRank === null) {
            $rankStmt = $pdo->prepare('SELECT count(*) + 1 FROM users WHERE xp > ? OR (xp = ? AND id < ?)');
            $rankStmt->execute([(int) $user['xp'], (int) $user['xp'], (int) $user['id']]);
            $myRank = (int) $rankStmt->fetchColumn();
        }
    }

    render('leaderboard', [
        'title' => t('O‘quvchilar Reytingi va Ligasi — LingoUz'),
        'leaders' => $leaders,
        'myRank' => $myRank,
        'currentUser' => $user,
    ]);
}

function page_flashcards(): void
{
    $pdo = db();
    $user = auth_user();
    $courseId = isset($_GET['course']) ? (int) $_GET['course'] : 0;
    $courses = published_courses();

    if ($courseId > 0) {
        $stmt = $pdo->prepare('
            SELECT v.id, v.lesson_id, v.word_uz, v.word_en, v.pronunciation, v.example,
                   l.title as lesson_title, l.title_en as lesson_title_en,
                   c.id as course_id, c.title as course_title, c.theme as course_theme
            FROM vocabulary v
            JOIN lessons l ON l.id = v.lesson_id
            JOIN courses c ON c.id = l.course_id
            WHERE c.id = ?
            ORDER BY RANDOM()
            LIMIT 40
        ');
        $stmt->execute([$courseId]);
    } else {
        $stmt = $pdo->query('
            SELECT v.id, v.lesson_id, v.word_uz, v.word_en, v.pronunciation, v.example,
                   l.title as lesson_title, l.title_en as lesson_title_en,
                   c.id as course_id, c.title as course_title, c.theme as course_theme
            FROM vocabulary v
            JOIN lessons l ON l.id = v.lesson_id
            JOIN courses c ON c.id = l.course_id
            ORDER BY RANDOM()
            LIMIT 40
        ');
    }
    $words = $stmt->fetchAll();

    render('flashcards', [
        'title' => t('Aqlli Flashcards: So‘zlarni takrorlash — LingoUz'),
        'words' => $words,
        'courses' => $courses,
        'selectedCourse' => $courseId,
        'currentUser' => $user,
    ]);
}

function action_flashcards_done(): void
{
    $user = require_login();
    $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $knownCount = max(0, min(100, (int) ($data['known_count'] ?? 0)));
    $earnedXp = $knownCount * 2; // 2 XP per mastered word

    $res = ['streak' => (int) $user['streak_days'], 'xp' => (int) $user['xp']];
    if ($earnedXp > 0) {
        $res = reward_user((int) $user['id'], $earnedXp, 2);
    }

    json_response([
        'ok' => true,
        'earned_xp' => $earnedXp,
        'total_xp' => $res['xp'],
        'streak' => $res['streak'],
    ]);
}

function action_telegram_webhook(): void
{
    // Check webhook secret header if provided
    $secretHeader = $_SERVER['HTTP_X_TELEGRAM_BOT_API_SECRET_TOKEN'] ?? '';
    $expectedSecret = telegram_webhook_secret();
    if ($secretHeader !== '' && $secretHeader !== $expectedSecret) {
        json_response(['ok' => false, 'error' => 'Unauthorized webhook token'], 403);
    }

    $raw = file_get_contents('php://input');
    if (!$raw) {
        json_response(['ok' => false, 'error' => 'No payload received'], 400);
    }

    $update = json_decode($raw, true);
    if (!is_array($update)) {
        json_response(['ok' => false, 'error' => 'Invalid JSON'], 400);
    }

    try {
        handle_telegram_webhook($update);
    } catch (\Throwable $e) {
        error_log('[Telegram Webhook Error] ' . $e->getMessage());
    }

    json_response(['ok' => true]);
}

function action_telegram_status(): void
{
    $user = auth_user();
    if (!$user) {
        json_response(['linked' => false, 'error' => 'Unauthenticated'], 401);
    }

    // Refresh directly from DB to get real-time status after user clicks /start in Telegram
    $stmt = db()->prepare('SELECT telegram_chat_id, telegram_username, telegram_first_name, telegram_linked_at FROM users WHERE id = ?');
    $stmt->execute([(int) $user['id']]);
    $row = $stmt->fetch();

    $linked = !empty($row['telegram_chat_id']);
    json_response([
        'linked' => $linked,
        'username' => (string) ($row['telegram_username'] ?? ''),
        'first_name' => (string) ($row['telegram_first_name'] ?? ''),
        'linked_at' => (string) ($row['telegram_linked_at'] ?? ''),
    ]);
}

function action_telegram_unlink(): void
{
    $user = require_login();
    verify_csrf();

    $stmt = db()->prepare('
        UPDATE users 
        SET telegram_chat_id = NULL, 
            telegram_username = NULL, 
            telegram_first_name = NULL, 
            telegram_linked_at = NULL 
        WHERE id = ?
    ');
    $stmt->execute([(int) $user['id']]);

    set_flash('success', t('Telegram hisobingiz muvaffaqiyatli uzildi.'));
    app_redirect('/dashboard');
}

function page_verify_certificate(string $code): void
{
    $code = strtoupper(trim($code));
    $pdo = db();
    
    // Pattern: LU-{courseId}-{studentId}
    if (!preg_match('/^LU-(\d+)-(\d+)$/', $code, $matches)) {
        render('verify_certificate', [
            'title' => t('Sertifikatni tekshirish — LingoUz'),
            'isValid' => false,
            'code' => $code,
            'error' => t('Sertifikat raqami noto‘g‘ri formatda kiritildi.'),
        ]);
        return;
    }

    $courseId = (int) $matches[1];
    $studentId = (int) $matches[2];

    $course = find_course($courseId);
    $stmt = $pdo->prepare('SELECT id, display_name, email, created_at, xp, coins FROM users WHERE id = ?');
    $stmt->execute([$studentId]);
    $student = $stmt->fetch();

    if (!$course || !$student) {
        render('verify_certificate', [
            'title' => t('Sertifikatni tekshirish — LingoUz'),
            'isValid' => false,
            'code' => $code,
            'error' => t('Bunday identifikatsiya raqamiga ega sertifikat topilmadi.'),
        ]);
        return;
    }

    $state = course_state($course, $studentId);
    $completedAt = $pdo->prepare('
        SELECT MAX(lp.completed_at) FROM lesson_progress lp JOIN lessons l ON l.id = lp.lesson_id
        WHERE lp.user_id = ? AND l.course_id = ?
    ');
    $completedAt->execute([$studentId, $courseId]);
    $date = $completedAt->fetchColumn();

    $isCompleted = ($state['total'] > 0 && $state['completed'] >= $state['total']);

    render('verify_certificate', [
        'title' => t('Sertifikat tasdiqlandi: ') . $code . ' — LingoUz',
        'isValid' => $isCompleted,
        'code' => $code,
        'student' => $student,
        'course' => $course,
        'state' => $state,
        'completedAt' => $date ?: date('Y-m-d'),
        'ogTitle' => '✅ Rasmiy Sertifikat: ' . $student['display_name'] . ' — LingoUz',
        'ogDescription' => "Ushbu sertifikat «{$course['title']}» kursi muvaffaqiyatli tamomlanganligini tasdiqlaydi.",
    ]);
}

function page_telegram_connect(): void
{
    $user = require_login();
    $url = get_telegram_link_url($user);
    app_redirect($url);
}

function page_stories(): void
{
    $user = auth_user();
    $stories = get_all_stories();
    render('stories', [
        'title' => t('Ovozli Ertaklar va Madaniy Hikoyalar — LingoUz'),
        'stories' => $stories,
        'user' => $user,
        'ogTitle' => '📚 O‘zbek Xalq Ertaklari va Ovozli Hikoyalar — LingoUz',
        'ogDescription' => 'Zumrad va Qimmat, Susambil, Oltin Tarvuz va Afandi latifalari. Audio talaffuz va interaktiv o‘rganish!',
    ]);
}

function page_story(string $id): void
{
    $user = auth_user();
    $story = find_story($id);
    if (!$story) {
        not_found(t('Ertak topilmadi'));
        return;
    }
    render('story_show', [
        'title' => (is_en() ? $story['title_en'] : $story['title_uz']) . ' — LingoUz',
        'story' => $story,
        'user' => $user,
        'ogTitle' => '📖 ' . (is_en() ? $story['title_en'] : $story['title_uz']) . ' — Ovozli O‘zbek Xalq Ertagi',
        'ogDescription' => is_en() ? $story['subtitle_en'] : $story['subtitle_uz'],
    ]);
}

function action_story_complete(string $id): void
{
    $user = auth_user();
    if (!$user) {
        json_response(['ok' => false, 'error' => 'unauthenticated'], 401);
    }
    $story = find_story($id);
    if (!$story) {
        json_response(['ok' => false, 'error' => 'not_found'], 404);
    }
    $res = reward_user((int) $user['id'], 20, 2);
    json_response([
        'ok' => true,
        'xp' => $res['xp'],
        'coins' => (int) $user['coins'] + 2,
    ]);
}
