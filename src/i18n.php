<?php

declare(strict_types=1);

/**
 * UI translation: Uzbek is the source language, English strings live in
 * src/lang/en.php keyed by the exact Uzbek text. Missing keys fall back to
 * Uzbek, so a forgotten string never breaks a page.
 *
 * Locale: ?lang=uz|en (remembered in a cookie) → cookie → Accept-Language.
 */

const LOCALES = ['uz' => 'O‘zbekcha', 'en' => 'English'];

function locale(?string $set = null): string
{
    static $locale = null;
    if ($set !== null) {
        $locale = $set;
    }
    return $locale ?? 'uz';
}

function resolve_locale(): void
{
    $requested = $_GET['lang'] ?? null;
    if (is_string($requested) && isset(LOCALES[$requested])) {
        setcookie('lang', $requested, [
            'expires' => time() + 86400 * 365,
            'path' => '/',
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        // Drop ?lang from the URL so links and reloads stay clean.
        $params = $_GET;
        unset($params['lang']);
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        header('Location: ' . $path . ($params ? '?' . http_build_query($params) : ''));
        exit;
    }
    $cookie = $_COOKIE['lang'] ?? null;
    if (is_string($cookie) && isset(LOCALES[$cookie])) {
        locale($cookie);
        return;
    }
    $accept = strtolower((string) ($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? ''));
    $first = substr($accept, 0, 2);
    locale($accept !== '' && !in_array($first, ['uz', 'ru', 'tg', 'kk', 'ky'], true) && str_contains($accept, 'en') ? 'en' : 'uz');
}

function is_en(): bool
{
    return locale() === 'en';
}

/** Translate a UI string; :name placeholders are filled with escaped values. */
function t(string $uz, array $params = [], bool $escape = true): string
{
    static $dict = null;
    $text = $uz;
    if (is_en()) {
        $dict ??= require __DIR__ . '/lang/en.php';
        $text = $dict[$uz] ?? $uz;
    }
    foreach ($params as $k => $v) {
        $text = str_replace(':' . $k, $escape ? e((string) $v) : (string) $v, $text);
    }
    return $text;
}

/** Localized DB field: in English use "{field}_en" when it is filled. */
function loc(array $row, string $field): string
{
    if (is_en() && !empty($row[$field . '_en'])) {
        return (string) $row[$field . '_en'];
    }
    return (string) ($row[$field] ?? '');
}

/** The same URL with ?lang= switched, for the language toggle. */
function lang_url(string $lang): string
{
    $params = $_GET;
    $params['lang'] = $lang;
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    return $path . '?' . http_build_query($params);
}

/** Strings used by public/assets/*.js; sent as window.I18N in English mode. */
const JS_STRINGS = [
    'Tarjimani yashirish', 'Tarjimani ko‘rsatish', '+:n tanga hisobingizga qo‘shildi! Jami: :total.',
    'Zo‘r takrorlash! Tangalar bu dars uchun avval berilgan.', 'Ba’zi savollarga javob berilmagan. Baribir yakunlaysizmi?',
    'Tekshirish', 'Tinglang', 'Nima deyildi?', 'Tinglash', 'Tarjima qiling', '«:word» — bu nima?', 'Inglizcha ma’nosini tanlang',
    'O‘zbekcha toping', 'O‘zbekcha qanday bo‘ladi?', 'Yozing', 'O‘zbekcha yozing', 'Javob…', 'Barakalla!', 'Yaxshi harakat!',
    ':score / :total to‘g‘ri', 'Endi yakuniy testga tayyorsiz!', 'Lug‘atni yana bir ko‘rib, qayta urinib ko‘ring.', 'Yana mashq qilish',
    'Barakalla! To‘g‘ri.', 'To‘g‘ri javob:', 'Davom etish', 'Mashq uchun kamida 2 ta so‘z kerak.',
];

function js_translations(): array
{
    if (!is_en()) {
        return [];
    }
    $out = [];
    foreach (JS_STRINGS as $s) {
        $out[$s] = t($s, [], false);
    }
    return $out;
}
