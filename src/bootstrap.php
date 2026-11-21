<?php

declare(strict_types=1);

// LingoUz - Application Bootstrap
require_once __DIR__ . '/icons.php';
require_once __DIR__ . '/mascot.php';
require_once __DIR__ . '/i18n.php';

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dbPath = __DIR__ . '/../database/database.sqlite';
        $dbDir = dirname($dbPath);
        if (!is_dir($dbDir)) {
            mkdir($dbDir, 0775, true);
        }
        $pdo = new PDO('sqlite:' . $dbPath, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 5,
        ]);
        $pdo->exec('PRAGMA foreign_keys = ON');
        $pdo->exec('PRAGMA journal_mode = WAL');
        $pdo->exec('PRAGMA synchronous = NORMAL');

        // migrate() also seeds an empty database (see seed_v2_content()).
        require_once __DIR__ . '/schema.php';
        migrate($pdo);
    }
    return $pdo;
}

function start_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => 86400 * 30,
            'path' => '/',
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_name('lingouz_session');
        session_start();
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . htmlspecialchars(csrf_token()) . '">';
    }
}

function verify_csrf(): void
{
    $token = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (empty($_SESSION['_csrf_token']) || !hash_equals($_SESSION['_csrf_token'], (string) $token)) {
        http_response_code(403);
        echo 'Invalid CSRF token. Please refresh and try again.';
        exit;
    }
}

function flash(string $key, string $message): void
{
    $_SESSION['flash'][$key] = $message;
}

function get_flash(string $key): ?string
{
    if (isset($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}

function has_flash(string $key): bool
{
    return isset($_SESSION['flash'][$key]);
}

function auth_user(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }
    static $cachedUser = null;
    if ($cachedUser === null) {
        $stmt = db()->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $cachedUser = $stmt->fetch() ?: null;
    }
    return $cachedUser;
}

function auth_check(): bool
{
    return auth_user() !== null;
}

function is_admin(): bool
{
    $u = auth_user();
    return $u && ($u['role'] === 'admin');
}

function login_user(array $user): void
{
    $_SESSION['user_id'] = $user['id'];
    session_regenerate_id(true);
}

function logout_user(): void
{
    unset($_SESSION['user_id']);
    session_regenerate_id(true);
}

if (!function_exists('redirect')) {
    function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}

if (!function_exists('e')) {
    function e(?string $str): string
    {
        return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
    }
}

function json_response(array $data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function current_path(): string
{
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $path = parse_url($uri, PHP_URL_PATH) ?: '/';
    return rtrim($path, '/') ?: '/';
}

function is_nav_active(string|array $routes): bool
{
    $current = current_path();
    foreach ((array) $routes as $r) {
        $clean = rtrim($r, '/') ?: '/';
        if ($clean === '/') {
            if ($current === '/') {
                return true;
            }
        } else {
            if ($current === $clean || str_starts_with($current, $clean . '/')) {
                return true;
            }
        }
    }
    return false;
}

function render(string $view, array $data = []): void
{
    extract($data);
    $viewPath = __DIR__ . '/views/' . $view . '.php';
    if (!file_exists($viewPath)) {
        throw new RuntimeException("View [$view] not found at: $viewPath");
    }

    ob_start();
    require $viewPath;
    $content = ob_get_clean();

    $layoutPath = __DIR__ . '/views/layout.php';
    if (file_exists($layoutPath)) {
        ob_start();
        require $layoutPath;
        $html = ob_get_clean();
        if (locale() === 'uz') {
            $html = to_new_latin_html($html);
        }
        echo $html;
    } else {
        if (locale() === 'uz') {
            $content = to_new_latin_html($content);
        }
        echo $content;
    }
}
