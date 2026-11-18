<?php

declare(strict_types=1);

// LingoUz by Gulomov's Academy - Route Handlers

function page_home(): void
{
    $pdo = db();
    $stmt = $pdo->query('SELECT * FROM lessons ORDER BY sequence_number ASC LIMIT 5');
    $lessons = $stmt->fetchAll();

    // Sample lesson vocab for demo
    $sampleVocab = $pdo->query('SELECT * FROM vocabulary WHERE lesson_id = 1 LIMIT 4')->fetchAll();

    render('home', [
        'title' => 'LingoUz by Gulomov’s Academy — Onlayn O‘zbek Tili Maktabi',
        'lessons' => $lessons,
        'sampleVocab' => $sampleVocab,
    ]);
}

function action_enroll(): void
{
    $parentName = trim($_POST['parent_name'] ?? '');
    $parentEmail = trim($_POST['parent_email'] ?? '');
    $parentPhone = trim($_POST['parent_phone'] ?? '');
    $childName = trim($_POST['child_name'] ?? '');
    $childAge = trim($_POST['child_age'] ?? '7-8');
    $languageLevel = trim($_POST['language_level'] ?? 'A0');
    $timezone = trim($_POST['timezone'] ?? 'America/New_York (EST)');
    $notes = trim($_POST['notes'] ?? '');
    $createAccount = !empty($_POST['create_account']);
    $password = trim($_POST['password'] ?? '');

    if ($parentName === '' || $parentEmail === '' || $childName === '') {
        flash('error', 'Iltimos, barcha majburiy maydonlarni to‘ldiring.');
        redirect('/#ariza');
    }

    $pdo = db();
    $stmt = $pdo->prepare('
        INSERT INTO enrollments (parent_name, parent_email, parent_phone, child_name, child_age, language_level, timezone, notes, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, "pending")
    ');
    $stmt->execute([$parentName, $parentEmail, $parentPhone, $childName, $childAge, $languageLevel, $timezone, $notes]);

    // Optionally create student user right away
    if ($createAccount && $password !== '') {
        $existing = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $existing->execute([$parentEmail]);
        if (!$existing->fetch()) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $userStmt = $pdo->prepare('
                INSERT INTO users (role, display_name, email, password_hash, age_band, language_level, timezone)
                VALUES ("student", ?, ?, ?, ?, ?, ?)
            ');
            $userStmt->execute([$childName, $parentEmail, $hash, $childAge, $languageLevel, $timezone]);
            $newUserId = (int)$pdo->lastInsertId();

            // Unlock Lesson 1
            $pdo->prepare('INSERT OR IGNORE INTO lesson_progress (user_id, lesson_id, status) VALUES (?, 1, "available")')
                ->execute([$newUserId]);
            for ($i = 2; $i <= 5; $i++) {
                $pdo->prepare('INSERT OR IGNORE INTO lesson_progress (user_id, lesson_id, status) VALUES (?, ?, "locked")')
                    ->execute([$newUserId, $i]);
            }

            // Auto login
            login_user(['id' => $newUserId, 'role' => 'student', 'display_name' => $childName, 'email' => $parentEmail]);
            flash('success', 'Arizangiz qabul qilindi va o‘quvchi profilingiz yaratildi! Darslarni boshlashingiz mumkin.');
            redirect('/dashboard');
        }
    }

    flash('success', 'Rahmat! Arizangiz muvaffaqiyatli qabul qilindi. Tez orada mentorlarimiz siz bilan bog‘lanishadi.');
    redirect('/#ariza');
}

function page_login(): void
{
    if (auth_check()) {
        redirect('/dashboard');
    }
    render('login', [
        'title' => 'Tizimga kirish — LingoUz',
    ]);
}

function action_login(): void
{
    $identifier = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($identifier === '' || $password === '') {
        flash('error', 'Email yoki login va parolni kiriting.');
        redirect('/login');
    }

    $altEmail = str_contains($identifier, '@') ? $identifier : $identifier . '@lingo.uz';

    $stmt = db()->prepare('SELECT * FROM users WHERE email = ? OR email = ?');
    $stmt->execute([$identifier, $altEmail]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        flash('error', 'Login yoki parol noto‘g‘ri kiritildi.');
        redirect('/login');
    }

    login_user($user);
    flash('success', 'Xush kelibsiz, ' . e($user['display_name']) . '!');
    redirect($user['role'] === 'admin' ? '/admin' : '/dashboard');
}

function page_profile(): void
{
    if (!auth_check()) {
        redirect('/login');
    }
    $user = auth_user();
    $pdo = db();

    // User's quiz stats
    $stats = $pdo->prepare('
        SELECT count(*) as attempts,
               sum(CASE WHEN passed = 1 THEN 1 ELSE 0 END) as passed_count,
               max(percent) as best_percent
        FROM quiz_attempts
        WHERE user_id = ?
    ');
    $stats->execute([$user['id']]);
    $userStats = $stats->fetch();

    render('profile', [
        'title' => 'Mening Profilim — ' . e($user['display_name']),
        'user' => $user,
        'stats' => $userStats,
    ]);
}

function action_update_profile(): void
{
    if (!auth_check()) {
        redirect('/login');
    }
    $user = auth_user();
    $displayName = trim($_POST['display_name'] ?? '');
    $ageBand = trim($_POST['age_band'] ?? $user['age_band']);
    $languageLevel = trim($_POST['language_level'] ?? $user['language_level']);
    $timezone = trim($_POST['timezone'] ?? $user['timezone']);
    $newPassword = trim($_POST['new_password'] ?? '');

    if ($displayName === '') {
        flash('error', 'Ism yoki taxallus bo‘sh bo‘lishi mumkin emas.');
        redirect('/profile');
    }

    $pdo = db();
    if ($newPassword !== '') {
        if (strlen($newPassword) < 4) {
            flash('error', 'Yangi parol kamida 4 ta belgidan iborat bo‘lishi kerak.');
            redirect('/profile');
        }
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('
            UPDATE users SET display_name = ?, age_band = ?, language_level = ?, timezone = ?, password_hash = ?
            WHERE id = ?
        ');
        $stmt->execute([$displayName, $ageBand, $languageLevel, $timezone, $hash, $user['id']]);
    } else {
        $stmt = $pdo->prepare('
            UPDATE users SET display_name = ?, age_band = ?, language_level = ?, timezone = ?
            WHERE id = ?
        ');
        $stmt->execute([$displayName, $ageBand, $languageLevel, $timezone, $user['id']]);
    }

    flash('success', 'Profilingiz muvaffaqiyatli yangilandi!');
    redirect('/profile');
}

function action_admin_enrollment_status(): void
{
    if (!is_admin()) {
        http_response_code(403);
        exit('Ruxsat berilmagan');
    }

    $id = (int)($_POST['enrollment_id'] ?? 0);
    $status = trim($_POST['status'] ?? 'pending');

    if ($id > 0 && in_array($status, ['pending', 'contacted', 'approved', 'rejected'], true)) {
        $stmt = db()->prepare('UPDATE enrollments SET status = ? WHERE id = ?');
        $stmt->execute([$status, $id]);
        flash('success', "Ariza #{$id} holati '{$status}' ga o‘zgartirildi.");
    }

    redirect('/admin');
}

function action_logout(): void
{
    logout_user();
    flash('info', 'Tizimdan muvaffaqiyatli chiqdingiz.');
    redirect('/');
}

function page_register(): void
{
    if (auth_check()) {
        redirect('/dashboard');
    }
    render('register', [
        'title' => 'O‘quvchini ro‘yxatdan o‘tkazish — LingoUz',
    ]);
}

function action_register(): void
{
    $displayName = trim($_POST['display_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $ageBand = trim($_POST['age_band'] ?? '7-8');
    $languageLevel = trim($_POST['language_level'] ?? 'A0');
    $timezone = trim($_POST['timezone'] ?? 'America/New_York (EST)');

    if ($displayName === '' || $email === '' || strlen($password) < 4) {
        flash('error', 'Iltimos, barcha maydonlarni to‘ldiring (parol kamida 4 belgi).');
        redirect('/register');
    }

    $pdo = db();
    $check = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $check->execute([$email]);
    if ($check->fetch()) {
        flash('error', 'Bu email orqali allaqachon ro‘yxatdan o‘tilgan. Tizimga kiring.');
        redirect('/login');
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('
        INSERT INTO users (role, display_name, email, password_hash, age_band, language_level, timezone)
        VALUES ("student", ?, ?, ?, ?, ?, ?)
    ');
    $stmt->execute([$displayName, $email, $hash, $ageBand, $languageLevel, $timezone]);
    $userId = (int)$pdo->lastInsertId();

    // Initialize progress: Lesson 1 open, 2-5 locked
    $pdo->prepare('INSERT INTO lesson_progress (user_id, lesson_id, status) VALUES (?, 1, "available")')
        ->execute([$userId]);
    for ($i = 2; $i <= 5; $i++) {
        $pdo->prepare('INSERT INTO lesson_progress (user_id, lesson_id, status) VALUES (?, ?, "locked")')
            ->execute([$userId, $i]);
    }

    $user = ['id' => $userId, 'role' => 'student', 'display_name' => $displayName, 'email' => $email];
    login_user($user);

    flash('success', 'Xush kelibsiz! Siz LingoUz akademiyasining ilk darsini boshlashga tayyorsiz!');
    redirect('/dashboard');
}

function page_dashboard(): void
{
    if (!auth_check()) {
        flash('info', 'Iltimos, avval tizimga kiring yoki demo akkaunt orqali sinab ko‘ring.');
        redirect('/login');
    }

    $user = auth_user();
    $pdo = db();

    // Fetch all lessons
    $lessons = $pdo->query('SELECT * FROM lessons ORDER BY sequence_number ASC')->fetchAll();

    // Fetch progress for this user
    $progStmt = $pdo->prepare('SELECT * FROM lesson_progress WHERE user_id = ?');
    $progStmt->execute([$user['id']]);
    $progressRows = $progStmt->fetchAll();

    $progressMap = [];
    foreach ($progressRows as $p) {
        $progressMap[$p['lesson_id']] = $p;
    }

    // Determine state for each lesson
    $completedCount = 0;
    $firstPendingLessonId = null;

    foreach ($lessons as &$l) {
        $lid = $l['id'];
        $p = $progressMap[$lid] ?? null;

        if ($p && $p['status'] === 'completed') {
            $l['user_status'] = 'completed';
            $l['best_score'] = $p['best_score'];
            $completedCount++;
        } elseif ($lid === 1 || ($p && ($p['status'] === 'available' || $p['manual_override'] == 1))) {
            $l['user_status'] = 'available';
            $l['best_score'] = $p['best_score'] ?? 0;
            if ($firstPendingLessonId === null) {
                $firstPendingLessonId = $lid;
            }
        } else {
            // Check if previous lesson was completed with >= 80%
            $prevLid = $lid - 1;
            $prevP = $progressMap[$prevLid] ?? null;
            if ($prevP && $prevP['status'] === 'completed') {
                $l['user_status'] = 'available';
                $l['best_score'] = $p['best_score'] ?? 0;
                if ($firstPendingLessonId === null) {
                    $firstPendingLessonId = $lid;
                }
            } else {
                $l['user_status'] = 'locked';
                $l['best_score'] = 0;
            }
        }
    }
    unset($l);

    $totalLessons = count($lessons);
    $progressPercent = $totalLessons > 0 ? (int)round(($completedCount / $totalLessons) * 100) : 0;
    $nextLessonId = $firstPendingLessonId ?? 1;

    // Badges
    $badgesStmt = $pdo->prepare('
        SELECT b.*, ub.earned_at
        FROM badges b
        LEFT JOIN user_badges ub ON ub.badge_code = b.code AND ub.user_id = ?
        ORDER BY b.id ASC
    ');
    $badgesStmt->execute([$user['id']]);
    $badges = $badgesStmt->fetchAll();

    // Upcoming Live Speaking Session
    $liveSession = $pdo->query('SELECT * FROM live_sessions WHERE status = "scheduled" ORDER BY starts_at ASC LIMIT 1')->fetch();

    render('dashboard', [
        'title' => 'O‘quvchi Paneli — ' . e($user['display_name']),
        'user' => $user,
        'lessons' => $lessons,
        'completedCount' => $completedCount,
        'totalLessons' => $totalLessons,
        'progressPercent' => $progressPercent,
        'nextLessonId' => $nextLessonId,
        'badges' => $badges,
        'liveSession' => $liveSession,
    ]);
}

function page_lesson(int $lessonId): void
{
    if (!auth_check()) {
        flash('info', 'Darsni ko‘rish uchun avval tizimga kiring.');
        redirect('/login');
    }

    $user = auth_user();
    $pdo = db();

    // Lesson
    $stmt = $pdo->prepare('SELECT * FROM lessons WHERE id = ?');
    $stmt->execute([$lessonId]);
    $lesson = $stmt->fetch();

    if (!$lesson) {
        http_response_code(404);
        render('not_found', ['title' => 'Dars topilmadi']);
        return;
    }

    // PRD 8.1 - 8.3: 80% Content Locking Check
    if ($lessonId > 1 && !is_admin()) {
        $prevId = $lessonId - 1;
        $prevProgStmt = $pdo->prepare('SELECT * FROM lesson_progress WHERE user_id = ? AND lesson_id = ?');
        $prevProgStmt->execute([$user['id'], $prevId]);
        $prevProg = $prevProgStmt->fetch();

        // Check if current lesson has manual override by admin
        $curProgStmt = $pdo->prepare('SELECT * FROM lesson_progress WHERE user_id = ? AND lesson_id = ?');
        $curProgStmt->execute([$user['id'], $lessonId]);
        $curProg = $curProgStmt->fetch();

        $hasOverride = $curProg && !empty($curProg['manual_override']);
        $prevPassed = $prevProg && $prevProg['status'] === 'completed' && $prevProg['best_score'] >= 80;

        if (!$prevPassed && !$hasOverride) {
            flash('error', "Ushbu dars qulflangan! Darsni ochish uchun avval {$prevId}-dars testidan kamida 80% to‘plashingiz kerak.");
            redirect('/dashboard');
            return;
        }
    }

    // Vocabulary
    $vocabStmt = $pdo->prepare('SELECT * FROM vocabulary WHERE lesson_id = ? ORDER BY id ASC');
    $vocabStmt->execute([$lessonId]);
    $vocabulary = $vocabStmt->fetchAll();

    // Quiz and Questions
    $quizStmt = $pdo->prepare('SELECT * FROM quizzes WHERE lesson_id = ?');
    $quizStmt->execute([$lessonId]);
    $quiz = $quizStmt->fetch();

    $questions = [];
    if ($quiz) {
        $qStmt = $pdo->prepare('SELECT * FROM questions WHERE quiz_id = ? ORDER BY order_num ASC');
        $qStmt->execute([$quiz['id']]);
        $questions = $qStmt->fetchAll();
        foreach ($questions as &$q) {
            $q['options'] = json_decode($q['options_json'], true) ?: [];
        }
        unset($q);
    }

    // User's best score for this lesson
    $curProgStmt = $pdo->prepare('SELECT * FROM lesson_progress WHERE user_id = ? AND lesson_id = ?');
    $curProgStmt->execute([$user['id'], $lessonId]);
    $currentProgress = $curProgStmt->fetch();

    render('lesson', [
        'title' => e($lesson['title']) . ' — LingoUz',
        'lesson' => $lesson,
        'vocabulary' => $vocabulary,
        'quiz' => $quiz,
        'questions' => $questions,
        'currentProgress' => $currentProgress,
        'user' => $user,
    ]);
}

function action_submit_quiz(int $lessonId): void
{
    if (!auth_check()) {
        flash('error', 'Iltimos, avval tizimga kiring.');
        redirect('/login');
    }

    $user = auth_user();
    $pdo = db();

    $quizStmt = $pdo->prepare('SELECT * FROM quizzes WHERE lesson_id = ?');
    $quizStmt->execute([$lessonId]);
    $quiz = $quizStmt->fetch();

    if (!$quiz) {
        flash('error', 'Test topilmadi.');
        redirect('/dashboard');
    }

    $qStmt = $pdo->prepare('SELECT * FROM questions WHERE quiz_id = ? ORDER BY order_num ASC');
    $qStmt->execute([$quiz['id']]);
    $questions = $qStmt->fetchAll();

    $total = count($questions);
    $correct = 0;
    $details = [];

    foreach ($questions as $q) {
        $submittedAns = isset($_POST['q_' . $q['id']]) ? (int)$_POST['q_' . $q['id']] : -1;
        $isCorrect = ($submittedAns === (int)$q['correct_index']);
        if ($isCorrect) {
            $correct++;
        }
        $options = json_decode($q['options_json'], true) ?: [];
        $details[] = [
            'prompt' => $q['prompt'],
            'chosen' => $options[$submittedAns] ?? 'Tanlanmadi',
            'correct' => $options[$q['correct_index']] ?? '',
            'is_correct' => $isCorrect,
            'explanation' => $q['explanation'],
        ];
    }

    $percent = $total > 0 ? (int)round(($correct / $total) * 100) : 0;
    $passed = $percent >= 80 ? 1 : 0;

    // Save Quiz Attempt
    $attStmt = $pdo->prepare('
        INSERT INTO quiz_attempts (user_id, quiz_id, lesson_id, score, total, percent, passed)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ');
    $attStmt->execute([$user['id'], $quiz['id'], $lessonId, $correct, $total, $percent, $passed]);
    $attemptId = (int)$pdo->lastInsertId();

    // Update Lesson Progress
    $curProgStmt = $pdo->prepare('SELECT * FROM lesson_progress WHERE user_id = ? AND lesson_id = ?');
    $curProgStmt->execute([$user['id'], $lessonId]);
    $curProg = $curProgStmt->fetch();

    $newBest = max($percent, (int)($curProg['best_score'] ?? 0));
    $newStatus = ($curProg && $curProg['status'] === 'completed') ? 'completed' : ($passed ? 'completed' : 'available');

    $progUpsert = $pdo->prepare('
        INSERT INTO lesson_progress (user_id, lesson_id, status, best_score, completed_at)
        VALUES (?, ?, ?, ?, datetime("now"))
        ON CONFLICT(user_id, lesson_id) DO UPDATE SET
            status = excluded.status,
            best_score = excluded.best_score,
            completed_at = CASE WHEN excluded.status = "completed" THEN datetime("now") ELSE completed_at END
    ');
    $progUpsert->execute([$user['id'], $lessonId, $newStatus, $newBest]);

    // If passed 80%, unlock the NEXT lesson automatically! (PRD 8.1 - 8.3)
    if ($passed) {
        $nextLessonId = $lessonId + 1;
        if ($nextLessonId <= 5) {
            $unlockStmt = $pdo->prepare('
                INSERT INTO lesson_progress (user_id, lesson_id, status)
                VALUES (?, ?, "available")
                ON CONFLICT(user_id, lesson_id) DO UPDATE SET
                    status = CASE WHEN status = "locked" THEN "available" ELSE status END
            ');
            $unlockStmt->execute([$user['id'], $nextLessonId]);
        }

        // Award Badges
        $awardBadge = function (string $badgeCode) use ($pdo, $user) {
            $pdo->prepare('INSERT OR IGNORE INTO user_badges (user_id, badge_code) VALUES (?, ?)')
                ->execute([$user['id'], $badgeCode]);
        };

        if ($lessonId === 1) {
            $awardBadge('first_step');
        }
        $awardBadge('smart_kid');

        // Check if 3 lessons completed
        $compCount = (int)$pdo->query("SELECT count(*) FROM lesson_progress WHERE user_id = {$user['id']} AND status = 'completed'")->fetchColumn();
        if ($compCount >= 3) {
            $awardBadge('halfway_hero');
        }
        if ($compCount >= 5) {
            $awardBadge('lingo_master');
        }
    }

    $_SESSION['last_quiz_result'] = [
        'attempt_id' => $attemptId,
        'lesson_id' => $lessonId,
        'score' => $correct,
        'total' => $total,
        'percent' => $percent,
        'passed' => (bool)$passed,
        'details' => $details,
    ];

    redirect("/lessons/{$lessonId}/result");
}

function page_quiz_result(int $lessonId): void
{
    if (!auth_check()) {
        redirect('/login');
    }

    $result = $_SESSION['last_quiz_result'] ?? null;
    $lesson = db()->query("SELECT * FROM lessons WHERE id = {$lessonId}")->fetch();

    if (!$result || (int)$result['lesson_id'] !== $lessonId) {
        // Fallback to latest attempt
        $stmt = db()->prepare('
            SELECT * FROM quiz_attempts
            WHERE user_id = ? AND lesson_id = ?
            ORDER BY id DESC LIMIT 1
        ');
        $stmt->execute([auth_user()['id'], $lessonId]);
        $latest = $stmt->fetch();
        if ($latest) {
            $result = [
                'score' => $latest['score'],
                'total' => $latest['total'],
                'percent' => $latest['percent'],
                'passed' => (bool)$latest['passed'],
                'details' => [],
            ];
        } else {
            redirect("/lessons/{$lessonId}");
            return;
        }
    }

    render('quiz_result', [
        'title' => 'Test Natijasi — LingoUz',
        'lesson' => $lesson,
        'result' => $result,
        'nextLessonId' => $lessonId < 5 ? ($lessonId + 1) : null,
    ]);
}

function page_live(): void
{
    if (!auth_check()) {
        flash('info', 'Jonli muloqot xonasiga kirish uchun avval ro‘yxatdan o‘ting.');
        redirect('/login');
    }

    $user = auth_user();
    $sessions = db()->query('SELECT * FROM live_sessions ORDER BY starts_at ASC')->fetchAll();

    // Mark badge for visiting live room
    db()->prepare('INSERT OR IGNORE INTO user_badges (user_id, badge_code) VALUES (?, "speaker")')
        ->execute([$user['id']]);

    render('live', [
        'title' => 'Live Speaking Room (Zoom) — LingoUz',
        'sessions' => $sessions,
        'user' => $user,
    ]);
}

function page_admin(): void
{
    if (!is_admin()) {
        http_response_code(403);
        flash('error', 'Bu sahifaga faqat administratorlar kira oladi.');
        redirect('/dashboard');
        return;
    }

    $pdo = db();

    // Students with progress
    $students = $pdo->query('
        SELECT u.*,
               (SELECT count(*) FROM lesson_progress lp WHERE lp.user_id = u.id AND lp.status = "completed") as completed_lessons,
               (SELECT max(score) FROM quiz_attempts qa WHERE qa.user_id = u.id) as max_score
        FROM users u
        WHERE u.role = "student"
        ORDER BY u.id DESC
    ')->fetchAll();

    // Parent Enrollments
    $enrollments = $pdo->query('SELECT * FROM enrollments ORDER BY id DESC')->fetchAll();

    // Recent Quiz Attempts
    $attempts = $pdo->query('
        SELECT qa.*, u.display_name, l.title as lesson_title
        FROM quiz_attempts qa
        JOIN users u ON u.id = qa.user_id
        JOIN lessons l ON l.id = qa.lesson_id
        ORDER BY qa.id DESC LIMIT 20
    ')->fetchAll();

    // Lessons overview
    $lessons = $pdo->query('SELECT * FROM lessons ORDER BY sequence_number ASC')->fetchAll();

    render('admin', [
        'title' => 'Loyiha Boshqaruvi (Admin Panel) — LingoUz',
        'students' => $students,
        'enrollments' => $enrollments,
        'attempts' => $attempts,
        'lessons' => $lessons,
    ]);
}

function action_admin_unlock(): void
{
    if (!is_admin()) {
        http_response_code(403);
        exit('Ruxsat berilmagan');
    }

    $userId = (int)($_POST['user_id'] ?? 0);
    $lessonId = (int)($_POST['lesson_id'] ?? 0);

    if ($userId > 0 && $lessonId > 0) {
        $pdo = db();
        $stmt = $pdo->prepare('
            INSERT INTO lesson_progress (user_id, lesson_id, status, manual_override)
            VALUES (?, ?, "available", 1)
            ON CONFLICT(user_id, lesson_id) DO UPDATE SET
                status = "available",
                manual_override = 1
        ');
        $stmt->execute([$userId, $lessonId]);
        flash('success', "Foydalanuvchi ID {$userId} uchun {$lessonId}-dars muvaffaqiyatli ochildi (Manual Override).");
    }

    redirect('/admin');
}

function action_admin_export(): void
{
    if (!is_admin()) {
        http_response_code(403);
        exit('Ruxsat berilmagan');
    }

    $enrollments = db()->query('SELECT * FROM enrollments ORDER BY id DESC')->fetchAll();

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="lingouz_enrollments_' . date('Y-m-d') . '.csv"');

    $out = fopen('php://output', 'w');
    // UTF-8 BOM for Excel compatibility
    fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
    fputcsv($out, ['ID', 'Ota-ona Ismi', 'Email', 'Telefon', 'Bola Ismi', 'Yoshi', 'Darajasi', 'Vaqt Zonasi', 'Izoh', 'Holat', 'Sana']);

    foreach ($enrollments as $e) {
        fputcsv($out, [
            $e['id'],
            $e['parent_name'],
            $e['parent_email'],
            $e['parent_phone'] ?? '',
            $e['child_name'],
            $e['child_age'],
            $e['language_level'],
            $e['timezone'],
            $e['notes'] ?? '',
            $e['status'],
            $e['created_at'],
        ]);
    }
    fclose($out);
    exit;
}

function page_privacy(): void
{
    render('privacy', [
        'title' => 'Bolalar Maxfiyligi va Xavfsizlik Siyosati (COPPA) — LingoUz',
    ]);
}

function page_certificate(): void
{
    if (!auth_check()) {
        flash('info', 'Sertifikatni ko‘rish uchun avval tizimga kiring.');
        redirect('/login');
    }

    $user = auth_user();
    render('certificate', [
        'title' => 'O‘zbek Tili Bilimdoni Diplomi — ' . e($user['display_name']),
        'student' => $user,
    ]);
}

function page_parent_tips(): void
{
    render('parent_tips', [
        'title' => 'Ota-onalar uchun 5 ta Oltin Qoida — LingoUz',
    ]);
}

