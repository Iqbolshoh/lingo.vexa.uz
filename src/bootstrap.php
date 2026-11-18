<?php

declare(strict_types=1);

// LingoUz by Gulomov's Academy - Application Bootstrap

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dbPath = __DIR__ . '/../database/database.sqlite';
        $dbDir = dirname($dbPath);
        if (!is_dir($dbDir)) {
            mkdir($dbDir, 0775, true);
        }
        $isNew = !file_exists($dbPath) || filesize($dbPath) === 0;
        $pdo = new PDO('sqlite:' . $dbPath, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 5,
        ]);
        $pdo->exec('PRAGMA foreign_keys = ON');
        $pdo->exec('PRAGMA journal_mode = WAL');
        $pdo->exec('PRAGMA synchronous = NORMAL');

        require_once __DIR__ . '/schema.php';
        migrate($pdo);

        if ($isNew) {
            require_once __DIR__ . '/content.php';
            seed_initial_data($pdo);
        }
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

function csrf_token(): string
{
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . htmlspecialchars(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $token = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (empty($_SESSION['_csrf_token']) || !hash_equals($_SESSION['_csrf_token'], (string) $token)) {
        http_response_code(403);
        echo 'CSRF token yaroqsiz. Qaytadan urinib ko‘ring.';
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
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function e(?string $str): string
{
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

function json_response(array $data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function render(string $view, array $data = []): void
{
    extract($data);
    $viewPath = __DIR__ . '/views/' . $view . '.php';
    if (!file_exists($viewPath)) {
        throw new RuntimeException("View [$view] topilmadi: $viewPath");
    }

    ob_start();
    require $viewPath;
    $content = ob_get_clean();

    $layoutPath = __DIR__ . '/views/layout.php';
    if (file_exists($layoutPath)) {
        require $layoutPath;
    } else {
        echo $content;
    }
}
