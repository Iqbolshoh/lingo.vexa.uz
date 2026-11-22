<?php

declare(strict_types=1);

// Admin statistics page and the remaining admin tools (student create/edit/reset, CSV exports, settings).

function stat_count(string $sql, array $params = []): int
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return (int) $stmt->fetchColumn();
}

/** Counts of each key across users' JSON lists (motives/places/interests). */
function stat_json_distribution(string $column, array $labels): array
{
    $counts = array_fill_keys(array_keys($labels), 0);
    foreach (db()->query("SELECT {$column} FROM users WHERE role = 'student' AND {$column} IS NOT NULL") as $row) {
        foreach (decode_list($row[$column], $labels) as $k) {
            $counts[$k]++;
        }
    }
    arsort($counts);
    $out = [];
    foreach ($counts as $k => $n) {
        $out[] = ['label' => $labels[$k][0], 'value' => $n];
    }
    return $out;
}

function stat_column_distribution(string $column, array $labels = []): array
{
    $rows = db()->query("SELECT COALESCE({$column}, '') AS k, count(*) AS n FROM users WHERE role = 'student' GROUP BY k ORDER BY n DESC")->fetchAll();
    return array_map(fn ($r) => ['label' => $r['k'] === '' ? 'Javob bermagan' : ($labels[$r['k']] ?? $r['k']), 'value' => (int) $r['n']], $rows);
}

function stat_daily(string $sql, int $days): array
{
    $rows = db()->query($sql)->fetchAll(PDO::FETCH_KEY_PAIR);
    $out = [];
    for ($i = $days - 1; $i >= 0; $i--) {
        $d = gmdate('Y-m-d', strtotime("-{$i} days"));
        $out[$d] = (int) ($rows[$d] ?? 0);
    }
    return $out;
}

function page_admin_stats(): void
{
    require_admin();
    $pdo = db();
    $students = stat_count("SELECT count(*) FROM users WHERE role = 'student'");
    $kpi = [
        'students' => $students,
        'new7' => stat_count("SELECT count(*) FROM users WHERE role = 'student' AND created_at >= datetime('now', '-7 days')"),
        'new30' => stat_count("SELECT count(*) FROM users WHERE role = 'student' AND created_at >= datetime('now', '-30 days')"),
        'dau' => stat_count("SELECT count(DISTINCT a.user_id) FROM activity_days a JOIN users u ON u.id = a.user_id WHERE u.role = 'student' AND a.day = date('now')"),
        'wau' => stat_count("SELECT count(DISTINCT a.user_id) FROM activity_days a JOIN users u ON u.id = a.user_id WHERE u.role = 'student' AND a.day >= date('now', '-6 days')"),
        'mau' => stat_count("SELECT count(DISTINCT a.user_id) FROM activity_days a JOIN users u ON u.id = a.user_id WHERE u.role = 'student' AND a.day >= date('now', '-29 days')"),
        'returning' => stat_count("SELECT count(*) FROM (SELECT a.user_id FROM activity_days a JOIN users u ON u.id = a.user_id WHERE u.role = 'student' GROUP BY a.user_id HAVING count(*) >= 2) sub"),
        'onboarded' => stat_count("SELECT count(*) FROM users WHERE role = 'student' AND motives_json IS NOT NULL"),
        'placement' => (int) round((float) $pdo->query("SELECT avg(placement_score) FROM users WHERE placement_score IS NOT NULL")->fetchColumn()),
        'attempts' => stat_count('SELECT count(*) FROM quiz_attempts'),
        'pass_rate' => (int) round((float) $pdo->query('SELECT avg(passed) * 100 FROM quiz_attempts')->fetchColumn()),
        'avg_xp' => (int) round((float) $pdo->query("SELECT avg(xp) FROM users WHERE role = 'student'")->fetchColumn()),
        'completed' => stat_count("SELECT count(*) FROM lesson_progress WHERE status = 'completed'"),
    ];

    $signups = stat_daily("SELECT date(created_at) d, count(*) FROM users WHERE role = 'student' AND created_at >= date('now', '-29 days') GROUP BY d", 30);
    $active = stat_daily("SELECT a.day, count(*) FROM activity_days a JOIN users u ON u.id = a.user_id WHERE u.role = 'student' AND a.day >= date('now', '-29 days') GROUP BY a.day", 30);

    // Funnel: how many learners completed each lesson, per course.
    $funnels = [];
    foreach ($pdo->query('SELECT * FROM courses ORDER BY sort_order, id')->fetchAll() as $c) {
        $stmt = $pdo->prepare("
            SELECT l.title, l.position,
                   (SELECT count(*) FROM lesson_progress lp WHERE lp.lesson_id = l.id AND (lp.best_score > 0 OR lp.status = 'completed')) AS started,
                   (SELECT count(*) FROM lesson_progress lp WHERE lp.lesson_id = l.id AND lp.status = 'completed') AS done
            FROM lessons l WHERE l.course_id = ? ORDER BY l.position, l.id
        ");
        $stmt->execute([$c['id']]);
        $funnels[] = ['course' => $c, 'rows' => $stmt->fetchAll()];
    }

    // Accuracy by exercise type, from stored per-question details.
    $types = array_fill_keys(array_keys(QUESTION_TYPES), ['ok' => 0, 'all' => 0]);
    foreach ($pdo->query('SELECT details_json FROM quiz_attempts WHERE details_json IS NOT NULL') as $row) {
        foreach (json_decode((string) $row['details_json'], true) ?: [] as $d) {
            $t = $d['type'] ?? 'choice';
            if (isset($types[$t])) {
                $types[$t]['all']++;
                $types[$t]['ok'] += empty($d['is_correct']) ? 0 : 1;
            }
        }
    }

    // Activity by hour, Tashkent time.
    $hours = array_fill(0, 24, 0);
    foreach ($pdo->query("SELECT strftime('%H', created_at, '+5 hours') AS h, count(*) AS n FROM quiz_attempts GROUP BY h") as $r) {
        $hours[(int) $r['h']] = (int) $r['n'];
    }

    render_admin('stats', 'stats', [
        'title' => 'Admin — Statistika',
        'kpi' => $kpi,
        'signups' => $signups,
        'active' => $active,
        'funnels' => $funnels,
        'types' => $types,
        'hours' => $hours,
        'motives' => stat_json_distribution('motives_json', ONB_MOTIVES),
        'places' => stat_json_distribution('places_json', ONB_PLACES),
        'interests' => stat_json_distribution('interests_json', ONB_INTERESTS),
        'selfLevels' => stat_column_distribution('self_level', array_map(fn ($l) => $l[0], ONB_LEVELS)),
        'levels' => stat_column_distribution('language_level'),
        'ages' => stat_column_distribution('age_band', AGE_LABELS),
        'zones' => stat_column_distribution('timezone', TIMEZONES),
        'goals' => stat_column_distribution('daily_goal_xp', array_map(fn ($g) => $g[0], ONB_GOALS)),
        'leaders' => $pdo->query("SELECT id, display_name, xp, streak_days, last_active_date, coins FROM users WHERE role = 'student' ORDER BY xp DESC, id LIMIT 10")->fetchAll(),
        'badgeStats' => $pdo->query('SELECT b.title, b.icon, (SELECT count(*) FROM user_badges ub WHERE ub.badge_code = b.code) AS n FROM badges b ORDER BY n DESC, b.id')->fetchAll(),
    ]);
}

// ---------------------------------------------------------------- students: create / edit / reset

function student_form_data(): array
{
    return [
        'display_name' => input_str('display_name', 80),
        'email' => input_str('email', 160),
        'age_band' => in_array($_POST['age_band'] ?? '', AGE_BANDS, true) ? $_POST['age_band'] : '18+',
        'language_level' => in_array($_POST['language_level'] ?? '', LEVELS, true) ? $_POST['language_level'] : 'A0',
        'timezone' => normalize_timezone($_POST['timezone'] ?? ''),
    ];
}

function email_taken(string $email, int $exceptId = 0): bool
{
    return stat_count('SELECT count(*) FROM users WHERE email = ? AND id <> ?', [$email, $exceptId]) > 0;
}

function page_admin_student_create(): void
{
    require_admin();
    render_admin('student_create', 'students', ['title' => 'Yangi o‘quvchi']);
}

function action_admin_student_store(): void
{
    require_admin();
    $d = student_form_data();
    $password = (string) ($_POST['password'] ?? '');
    if ($d['display_name'] === '' || !filter_var($d['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($password) < 6) {
        flash('error', 'Ism, to‘g‘ri email va kamida 6 belgili parol kiriting.');
        app_redirect('/admin/students/create');
    }
    if (email_taken($d['email'])) {
        flash('error', 'Bu email bilan foydalanuvchi allaqachon bor.');
        app_redirect('/admin/students/create');
    }
    db()->prepare("INSERT INTO users (role, display_name, email, password_hash, age_band, language_level, timezone) VALUES ('student', ?, ?, ?, ?, ?, ?)")
        ->execute([$d['display_name'], $d['email'], password_hash($password, PASSWORD_DEFAULT), $d['age_band'], $d['language_level'], $d['timezone']]);
    flash('success', 'O‘quvchi yaratildi. Birinchi kirishda u tanishuv savollariga javob beradi.');
    app_redirect('/admin/students/' . db()->lastInsertId());
}

function action_admin_student_update(int $userId): void
{
    require_admin();
    $d = student_form_data();
    if ($d['display_name'] === '' || !filter_var($d['email'], FILTER_VALIDATE_EMAIL)) {
        flash('error', 'Ism va to‘g‘ri email kiriting.');
    } elseif (email_taken($d['email'], $userId)) {
        flash('error', 'Bu email boshqa foydalanuvchida bor.');
    } else {
        db()->prepare("UPDATE users SET display_name = ?, email = ?, age_band = ?, language_level = ?, timezone = ? WHERE id = ? AND role = 'student'")
            ->execute([...array_values($d), $userId]);
        flash('success', 'O‘quvchi ma’lumotlari saqlandi.');
    }
    app_redirect("/admin/students/{$userId}");
}

function action_admin_student_reset_progress(int $userId): void
{
    require_admin();
    $pdo = db();
    $pdo->beginTransaction();
    foreach (['lesson_progress', 'quiz_attempts', 'user_badges'] as $table) {
        $pdo->prepare("DELETE FROM {$table} WHERE user_id = ?")->execute([$userId]);
    }
    $pdo->prepare("UPDATE users SET xp = 0, coins = 0, streak_days = 0, last_active_date = NULL WHERE id = ? AND role = 'student'")->execute([$userId]);
    $pdo->commit();
    flash('success', 'O‘quvchining natijalari, XP va nishonlari nolga tushirildi.');
    app_redirect("/admin/students/{$userId}");
}

// ---------------------------------------------------------------- CSV exports

function csv_download(string $name, array $header, iterable $rows): void
{
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $name . '_' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
    fputcsv($out, $header, ',', '"', '');
    foreach ($rows as $r) {
        fputcsv($out, $r, ',', '"', '');
    }
    fclose($out);
    exit;
}

function action_admin_export_students(): void
{
    require_admin();
    $labels = fn (?string $json, array $map) => implode('; ', array_map(fn ($k) => $map[$k][0], decode_list($json, $map)));
    $rows = [];
    foreach (db()->query("
        SELECT u.*, (SELECT count(*) FROM lesson_progress lp WHERE lp.user_id = u.id AND lp.status = 'completed') AS done
        FROM users u WHERE role = 'student' ORDER BY id
    ") as $u) {
        $rows[] = [$u['id'], $u['display_name'], $u['email'], AGE_LABELS[$u['age_band']] ?? $u['age_band'], $u['language_level'],
            ONB_LEVELS[$u['self_level'] ?? '']['0'] ?? '', $u['placement_score'] ?? '', $u['timezone'], $u['xp'], $u['done'], $u['streak_days'],
            $labels($u['motives_json'], ONB_MOTIVES), $labels($u['places_json'], ONB_PLACES), $labels($u['interests_json'], ONB_INTERESTS),
            $u['created_at'], $u['last_seen_at']];
    }
    csv_download('lingouz_students', ['ID', 'Ism', 'Email', 'Yosh', 'Daraja', 'O‘z bahosi', 'Daraja testi %', 'Vaqt zonasi', 'XP', 'Tugatgan darslar', 'Ketma-ket kun',
        'Maqsadlar', 'Joylar', 'Qiziqishlar', 'Ro‘yxatdan o‘tgan', 'Oxirgi faollik'], $rows);
}

function action_admin_export_gradebook(): void
{
    require_admin();
    $course = find_course((int) ($_GET['course'] ?? 0));
    if (!$course) {
        app_redirect('/admin/gradebook');
    }
    $lessons = course_lessons((int) $course['id'], false);
    $grid = [];
    foreach (db()->query('SELECT user_id, lesson_id, best_score FROM lesson_progress') as $p) {
        $grid[(int) $p['user_id']][(int) $p['lesson_id']] = (int) $p['best_score'];
    }
    $rows = [];
    foreach (db()->query("SELECT id, display_name FROM users WHERE role = 'student' ORDER BY id") as $s) {
        $scores = array_map(fn ($l) => $grid[(int) $s['id']][(int) $l['id']] ?? 0, $lessons);
        $rows[] = [$s['display_name'], ...$scores, $scores ? (int) round(array_sum($scores) / count($scores)) : 0];
    }
    csv_download('lingouz_gradebook_' . $course['id'], ['O‘quvchi', ...array_map(fn ($l) => $l['position'] . '. ' . $l['title'], $lessons), 'O‘rtacha'], $rows);
}

// ---------------------------------------------------------------- settings

function page_admin_settings(): void
{
    $me = require_admin();
    render_admin('settings', 'settings', [
        'title' => 'Admin — Sozlamalar',
        'me' => $me,
        'admins' => db()->query("SELECT id, display_name, email, created_at FROM users WHERE role = 'admin' ORDER BY id")->fetchAll(),
    ]);
}

function action_admin_password(): void
{
    $me = require_admin();
    $current = (string) ($_POST['current_password'] ?? '');
    $new = (string) ($_POST['new_password'] ?? '');
    $row = db()->query('SELECT password_hash FROM users WHERE id = ' . (int) $me['id'])->fetchColumn();
    if (!password_verify($current, (string) $row)) {
        flash('error', 'Joriy parol noto‘g‘ri.');
    } elseif (mb_strlen($new) < 8) {
        flash('error', 'Yangi parol kamida 8 ta belgi bo‘lsin.');
    } else {
        db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([password_hash($new, PASSWORD_DEFAULT), $me['id']]);
        flash('success', 'Parolingiz yangilandi.');
    }
    app_redirect('/admin/settings');
}

function action_admin_create_admin(): void
{
    require_admin();
    $name = input_str('display_name', 80);
    $email = input_str('email', 160);
    $password = (string) ($_POST['password'] ?? '');
    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($password) < 8) {
        flash('error', 'Ism, to‘g‘ri email va kamida 8 belgili parol kiriting.');
    } elseif (email_taken($email)) {
        flash('error', 'Bu email bilan foydalanuvchi allaqachon bor.');
    } else {
        db()->prepare("INSERT INTO users (role, display_name, email, password_hash, onboarded_at) VALUES ('admin', ?, ?, ?, datetime('now'))")
            ->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
        flash('success', 'Yangi administrator qo‘shildi.');
    }
    app_redirect('/admin/settings');
}
