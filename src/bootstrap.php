<?php

declare(strict_types=1);

// LingoUz - Application Bootstrap
require_once __DIR__ . '/icons.php';
require_once __DIR__ . '/mascot.php';
require_once __DIR__ . '/i18n.php';

class LingoPDO extends PDO
{
    private bool $isMysql;

    public function __construct(string $dsn, ?string $username = null, ?string $password = null, ?array $options = null)
    {
        parent::__construct($dsn, $username, $password, $options);
        $this->isMysql = str_starts_with($dsn, 'mysql:');
    }

    public function isMysql(): bool
    {
        return $this->isMysql;
    }

    public function rewriteSql(string $sql): string
    {
        if (!$this->isMysql) {
            return $sql;
        }

        if (preg_match('/^\s*PRAGMA\b/i', $sql)) {
            return 'SELECT 1';
        }

        // 1. INSERT OR IGNORE INTO -> INSERT IGNORE INTO
        $sql = preg_replace('/INSERT\s+OR\s+IGNORE\s+INTO/i', 'INSERT IGNORE INTO', $sql);

        // 2. INSERT OR REPLACE INTO -> REPLACE INTO
        $sql = preg_replace('/INSERT\s+OR\s+REPLACE\s+INTO/i', 'REPLACE INTO', $sql);

        // 3. datetime('now', '-X days/hours') -> DATE_SUB(NOW(), INTERVAL X DAY/HOUR)
        $sql = preg_replace_callback("/datetime\s*\(\s*['\"]now['\"]\s*,\s*['\"]([+-])(\d+)\s*(days?|hours?)['\"]\s*\)/i", function($m) {
            $op = ($m[1] === '-') ? 'DATE_SUB' : 'DATE_ADD';
            $unit = strtoupper(rtrim($m[3], 's'));
            return "{$op}(NOW(), INTERVAL {$m[2]} {$unit})";
        }, $sql);

        // 4. date('now', '-X days/hours') -> DATE_SUB(CURDATE(), INTERVAL X DAY/HOUR)
        $sql = preg_replace_callback("/date\s*\(\s*['\"]now['\"]\s*,\s*['\"]([+-])(\d+)\s*(days?|hours?)['\"]\s*\)/i", function($m) {
            $op = ($m[1] === '-') ? 'DATE_SUB' : 'DATE_ADD';
            $unit = strtoupper(rtrim($m[3], 's'));
            return "{$op}(CURDATE(), INTERVAL {$m[2]} {$unit})";
        }, $sql);

        // 5. datetime('now') -> NOW()
        $sql = preg_replace("/datetime\s*\(\s*['\"]now['\"]\s*\)/i", 'NOW()', $sql);

        // 6. date('now') -> CURDATE()
        $sql = preg_replace("/date\s*\(\s*['\"]now['\"]\s*\)/i", 'CURDATE()', $sql);

        // 7. strftime('%H', created_at, '+5 hours') -> DATE_FORMAT(DATE_ADD(created_at, INTERVAL 5 HOUR), '%H')
        $sql = preg_replace("/strftime\s*\(\s*['\"]%H['\"]\s*,\s*created_at\s*,\s*['\"]\+5\s*hours?['\"]\s*\)/i", "DATE_FORMAT(DATE_ADD(created_at, INTERVAL 5 HOUR), '%H')", $sql);

        // 8. strftime('%S', ...) -> DATE_FORMAT(..., '%S')
        $sql = preg_replace_callback("/strftime\s*\(\s*['\"]%S['\"]\s*,\s*([^)]+)\s*\)/i", function($m) {
            return "DATE_FORMAT(" . trim($m[1]) . ", '%S')";
        }, $sql);

        // 9. FROM (SELECT ... HAVING count(*) >= 2) -> FROM (SELECT ... HAVING count(*) >= 2) sub
        if (preg_match('/(\bFROM\s*\([^()]+\))(\s*(?:WHERE|ORDER|LIMIT|;|\$))/is', $sql)) {
            $sql = preg_replace('/(\bFROM\s*\([^()]+\))(\s*(?:WHERE|ORDER|LIMIT|;|\$))/is', '$1 sub $2', $sql);
        }

        // 10. startup_meta WHERE key =
        $sql = preg_replace('/(\bWHERE\s+)key(\s*=)/i', '$1`key`$2', $sql);

        // 11. RANDOM() -> RAND()
        $sql = preg_replace('/\bRANDOM\(\)/i', 'RAND()', $sql);

        return $sql;
    }

    public function query(string $query, ?int $fetchMode = null, mixed ...$fetchModeArgs): PDOStatement|false
    {
        $sql = $this->rewriteSql($query);
        return parent::query($sql, ...($fetchMode !== null ? [$fetchMode, ...$fetchModeArgs] : []));
    }

    public function prepare(string $query, array $options = []): PDOStatement|false
    {
        $sql = $this->rewriteSql($query);
        return parent::prepare($sql, $options);
    }

    public function exec(string $statement): int|false
    {
        $sql = $this->rewriteSql($statement);
        return parent::exec($sql);
    }
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $envFile = __DIR__ . '/../.env';
        $env = [];
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#')) continue;
                if (str_contains($line, '=')) {
                    [$k, $v] = explode('=', $line, 2);
                    $k = trim($k);
                    $v = trim($v, " \t\n\r\0\x0B\"'");
                    $env[$k] = $v;
                }
            }
        }

        $connection = function_exists('env') && env('DB_CONNECTION') ? env('DB_CONNECTION') : ($env['DB_CONNECTION'] ?? 'mysql');

        if ($connection === 'mysql') {
            $host = function_exists('env') && env('DB_HOST') ? env('DB_HOST') : ($env['DB_HOST'] ?? '127.0.0.1');
            $port = function_exists('env') && env('DB_PORT') ? env('DB_PORT') : ($env['DB_PORT'] ?? '3306');
            $database = function_exists('env') && env('DB_DATABASE') ? env('DB_DATABASE') : ($env['DB_DATABASE'] ?? 'lingo_vexa_db');
            $username = function_exists('env') && env('DB_USERNAME') ? env('DB_USERNAME') : ($env['DB_USERNAME'] ?? 'lingo_vexa_user');
            $password = function_exists('env') && env('DB_PASSWORD') !== null ? (string) env('DB_PASSWORD') : ($env['DB_PASSWORD'] ?? '');

            $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
            $pdo = new LingoPDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT => 5,
            ]);
        } else {
            $dbPath = function_exists('env') && env('DB_DATABASE') ? env('DB_DATABASE') : ($env['DB_DATABASE'] ?? __DIR__ . '/../database/database.sqlite');
            $dbDir = dirname($dbPath);
            if (!is_dir($dbDir)) {
                mkdir($dbDir, 0775, true);
            }
            $pdo = new LingoPDO('sqlite:' . $dbPath, null, null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT => 5,
            ]);
            $pdo->exec('PRAGMA foreign_keys = ON');
            $pdo->exec('PRAGMA journal_mode = WAL');
            $pdo->exec('PRAGMA synchronous = NORMAL');
        }

        // migrate() also seeds an empty database (see seed_v2_content()).
        require_once __DIR__ . '/schema.php';
        migrate($pdo);
    }
    return $pdo;
}

function start_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        $sessionDir = __DIR__ . '/../storage/framework/sessions';
        if (!is_dir($sessionDir)) {
            @mkdir($sessionDir, 0775, true);
        }
        session_save_path($sessionDir);
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

function app_redirect(string $url): never
{
    while (ob_get_level() > 0) {
        @ob_end_clean();
    }
    if (!headers_sent()) {
        header('Location: ' . $url, true, 302);
    } else {
        echo '<script>window.location.replace(' . json_encode($url) . ');</script>';
    }
    exit;
}

if (!function_exists('redirect')) {
    function redirect(string $url): void
    {
        app_redirect($url);
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
    exit;
}

require_once __DIR__ . '/learning.php';
require_once __DIR__ . '/onboarding.php';
require_once __DIR__ . '/telegram.php';
require_once __DIR__ . '/stories.php';
require_once __DIR__ . '/og.php';
