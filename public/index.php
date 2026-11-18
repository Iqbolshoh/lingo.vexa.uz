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

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = '/' . trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '', '/');

if ($method === 'POST') {
    verify_csrf();
}

require __DIR__ . '/../src/handlers.php';

$routes = [
    'GET /' => 'page_home',
    'POST /enroll' => 'action_enroll',
    'GET /login' => 'page_login',
    'POST /login' => 'action_login',
    'GET /login/quick' => 'action_quick_login',
    'POST /logout' => 'action_logout',
    'GET /register' => 'page_register',
    'POST /register' => 'action_register',
    'GET /dashboard' => 'page_dashboard',
    'GET /live' => 'page_live',
    'GET /admin' => 'page_admin',
    'POST /admin/unlock' => 'action_admin_unlock',
    'GET /admin/export' => 'action_admin_export',
    'GET /privacy' => 'page_privacy',
    'GET /terms' => 'page_privacy',
    'GET /certificate' => 'page_certificate',
    'GET /parent-tips' => 'page_parent_tips',
];

try {
    $routeKey = $method . ' ' . $path;

    if (isset($routes[$routeKey])) {
        $routes[$routeKey]();
    } elseif (preg_match('#^/lessons/(\d+)$#', $path, $m) && $method === 'GET') {
        page_lesson((int)$m[1]);
    } elseif (preg_match('#^/lessons/(\d+)/quiz$#', $path, $m) && $method === 'POST') {
        action_submit_quiz((int)$m[1]);
    } elseif (preg_match('#^/lessons/(\d+)/result$#', $path, $m) && $method === 'GET') {
        page_quiz_result((int)$m[1]);
    } else {
        http_response_code(404);
        render('not_found', ['title' => 'Sahifa topilmadi']);
    }
} catch (Throwable $e) {
    error_log('[lingouz] ' . $e::class . ': ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    http_response_code(500);
    echo '<!doctype html><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
        . '<title>Kichik nosozlik</title><body style="font-family:system-ui;padding:40px;text-align:center">'
        . '<h1>Kichik nosozlik yuz berdi</h1><p>' . htmlspecialchars($e->getMessage()) . '</p><p><a href="/">Bosh sahifaga qaytish</a></p>';
}
