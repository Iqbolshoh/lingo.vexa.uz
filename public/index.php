<?php

declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

// Health check for uptime monitoring
if (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) === '/up') {
    try {
        db()->query('SELECT 1')->fetchColumn();
        header('Content-Type: text/plain');
        exit('OK');
    } catch (Throwable $e) {
        http_response_code(500);
        exit('DB error');
    }
}

start_session();
resolve_locale();

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method === 'HEAD') {
    $method = 'GET';
}
$path = '/' . trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '', '/');

if ($method === 'POST') {
    verify_csrf();
}

require __DIR__ . '/../src/learning.php';
require __DIR__ . '/../src/handlers.php';
require __DIR__ . '/../src/admin.php';
require __DIR__ . '/../src/onboarding.php';
require __DIR__ . '/../src/stats.php';

track_activity();

// [method, pattern, handler]; numeric captures are passed as int arguments.
$routes = [
    ['GET', '/', 'page_home'],
    ['POST', '/enroll', 'action_enroll'],
    ['GET', '/login', 'page_login'],
    ['POST', '/login', 'action_login'],
    ['POST', '/logout', 'action_logout'],
    ['GET', '/register', 'page_register'],
    ['POST', '/register', 'action_register'],
    ['GET', '/dashboard', 'page_dashboard'],
    ['GET', '/onboarding', 'page_onboarding'],
    ['POST', '/onboarding', 'action_onboarding'],
    ['POST', '/onboarding/skip', 'action_onboarding_skip'],
    ['GET', '/onboarding/placement', 'page_placement'],
    ['POST', '/onboarding/placement', 'action_placement'],
    ['GET', '/onboarding/done', 'page_onboarding_done'],
    ['GET', '/courses', 'page_courses'],
    ['GET', '/courses/(\d+)', 'page_course'],
    ['GET', '/lessons/(\d+)', 'page_lesson'],
    ['POST', '/lessons/(\d+)/quiz', 'action_submit_quiz'],
    ['POST', '/lessons/(\d+)/game', 'action_game_complete'],
    ['GET', '/lessons/(\d+)/result', 'page_quiz_result'],
    ['GET', '/profile', 'page_profile'],
    ['POST', '/profile', 'action_update_profile'],
    ['GET', '/live', 'page_live'],
    ['GET', '/certificate', 'page_certificate'],
    ['GET', '/privacy', 'page_privacy'],
    ['GET', '/terms', 'page_privacy'],
    ['GET', '/parent-tips', 'page_parent_tips'],

    ['GET', '/admin', 'page_admin'],
    ['GET', '/admin/courses', 'page_admin_courses'],
    ['GET', '/admin/courses/create', 'page_admin_course_form'],
    ['POST', '/admin/courses', 'action_admin_course_save'],
    ['GET', '/admin/courses/(\d+)', 'page_admin_course'],
    ['GET', '/admin/courses/(\d+)/edit', 'page_admin_course_form'],
    ['POST', '/admin/courses/(\d+)', 'action_admin_course_save'],
    ['POST', '/admin/courses/(\d+)/delete', 'action_admin_course_delete'],
    ['GET', '/admin/courses/(\d+)/lessons/create', 'page_admin_lesson_create'],
    ['POST', '/admin/courses/(\d+)/lessons', 'action_admin_lesson_store'],
    ['GET', '/admin/lessons/(\d+)', 'page_admin_lesson'],
    ['POST', '/admin/lessons/(\d+)', 'action_admin_lesson_update'],
    ['POST', '/admin/lessons/(\d+)/delete', 'action_admin_lesson_delete'],
    ['POST', '/admin/lessons/(\d+)/move', 'action_admin_lesson_move'],
    ['POST', '/admin/lessons/(\d+)/vocab', 'action_admin_vocab_save'],
    ['POST', '/admin/lessons/(\d+)/vocab/(\d+)', 'action_admin_vocab_save'],
    ['POST', '/admin/lessons/(\d+)/vocab/(\d+)/delete', 'action_admin_vocab_delete'],
    ['POST', '/admin/lessons/(\d+)/questions', 'action_admin_question_save'],
    ['POST', '/admin/lessons/(\d+)/questions/(\d+)', 'action_admin_question_save'],
    ['POST', '/admin/lessons/(\d+)/questions/(\d+)/delete', 'action_admin_question_delete'],
    ['POST', '/admin/lessons/(\d+)/questions/(\d+)/move', 'action_admin_question_move'],
    ['GET', '/admin/students', 'page_admin_students'],
    ['GET', '/admin/students/(\d+)', 'page_admin_student'],
    ['POST', '/admin/students/(\d+)/password', 'action_admin_student_reset_password'],
    ['POST', '/admin/students/(\d+)/delete', 'action_admin_student_delete'],
    ['POST', '/admin/unlock', 'action_admin_unlock'],
    ['GET', '/admin/gradebook', 'page_admin_gradebook'],
    ['GET', '/admin/stats', 'page_admin_stats'],
    ['GET', '/admin/students/create', 'page_admin_student_create'],
    ['POST', '/admin/students', 'action_admin_student_store'],
    ['POST', '/admin/students/(\d+)/update', 'action_admin_student_update'],
    ['POST', '/admin/students/(\d+)/reset-progress', 'action_admin_student_reset_progress'],
    ['GET', '/admin/export/students', 'action_admin_export_students'],
    ['GET', '/admin/export/gradebook', 'action_admin_export_gradebook'],
    ['GET', '/admin/settings', 'page_admin_settings'],
    ['POST', '/admin/settings/password', 'action_admin_password'],
    ['POST', '/admin/settings/admins', 'action_admin_create_admin'],
    ['GET', '/admin/enrollments', 'page_admin_enrollments'],
    ['POST', '/admin/enrollments/status', 'action_admin_enrollment_status'],
    ['GET', '/admin/export', 'action_admin_export'],
    ['GET', '/admin/live', 'page_admin_live'],
    ['POST', '/admin/live', 'action_admin_live_save'],
    ['POST', '/admin/live/(\d+)', 'action_admin_live_save'],
    ['POST', '/admin/live/(\d+)/delete', 'action_admin_live_delete'],
];

try {
    foreach ($routes as [$verb, $pattern, $handler]) {
        if ($verb === $method && preg_match('#^' . $pattern . '$#', $path, $m)) {
            $handler(...array_map('intval', array_slice($m, 1)));
            exit;
        }
    }
    if ($method === 'GET' && $path === '/logout') {
        redirect(auth_check() ? '/profile' : '/login');
    }
    http_response_code(404);
    render('not_found', ['title' => 'Sahifa topilmadi']);
} catch (Throwable $e) {
    error_log('[lingouz] ' . $e::class . ': ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    http_response_code(500);
    echo '<!doctype html><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
        . '<title>Kıçik nosozlik</title><body style="font-family:system-ui;padding:40px;text-align:center;color:#0c4a6e">'
        . '<h1>Kıçik nosozlik yuz berdi</h1><p>Biz buni allaqaçon tuzatyapmiz. Birozdan söng qayta urinib köring.</p><p><a href="/">Boş sahifaga qaytiş</a></p>';
}
