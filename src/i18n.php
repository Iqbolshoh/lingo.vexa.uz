<?php

declare(strict_types=1);

/**
 * UI translation: Uzbek is the source language, English strings live in
 * src/lang/en.php keyed by the exact Uzbek text. Missing keys fall back to
 * Uzbek, so a forgotten string never breaks a page.
 *
 * Locale: ?lang=uz|en (remembered in a cookie) → cookie → Accept-Language.
 */

const LOCALES = ['uz' => 'Özbekça', 'en' => 'English'];

/**
 * Transforms Uzbek text to the new Latin alphabet standard:
 * O‘ / o‘ -> Ö / ö
 * Sh / sh -> Ş / ş
 * Ch / ch -> Ç / ç
 * G‘ / g‘ -> Ğ / ğ
 */
function to_new_latin(string $text): string
{
    if ($text === '') {
        return '';
    }
    $apos = "['\x{2018}\x{2019}\x{02BB}\x{0060}\x{201B}]";
    $text = preg_replace("/O{$apos}/u", 'Ö', $text);
    $text = preg_replace("/o{$apos}/u", 'ö', $text);
    $text = preg_replace("/G{$apos}/u", 'Ğ', $text);
    $text = preg_replace("/g{$apos}/u", 'ğ', $text);
    // Protect specific English terms
    $text = str_replace('Children', '##KIDS_ENG##', $text);
    $text = preg_replace('/CH(?=[A-ZÖĞŞÇ\s\.,!\?:;"\'\)\(\]\}<]|$)/u', 'Ç', $text);
    $text = preg_replace('/Ch/u', 'Ç', $text);
    $text = preg_replace('/ch/u', 'ç', $text);
    $text = preg_replace('/SH(?=[A-ZÖĞŞÇ\s\.,!\?:;"\'\)\(\]\}<]|$)/u', 'Ş', $text);
    $text = preg_replace('/Sh/u', 'Ş', $text);
    $text = preg_replace('/sh/u', 'ş', $text);
    $text = str_replace('##KIDS_ENG##', 'Children', $text);
    return $text;
}

function to_new_latin_html(string $html): string
{
    if ($html === '') {
        return '';
    }
    // Protect raw blocks like <script> and <style>
    $rawBlocks = [];
    $html = preg_replace_callback('/<(script|style)[^>]*>.*?<\/\\1>/si', function ($m) use (&$rawBlocks) {
        $idx = count($rawBlocks);
        $rawBlocks[$idx] = $m[0];
        return "___RAW_BLOCK_{$idx}___";
    }, $html);

    $parts = preg_split('/(<[^>]+>)/u', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
    for ($i = 0; $i < count($parts); $i++) {
        if ($i % 2 === 0) {
            $parts[$i] = to_new_latin($parts[$i]);
        } else {
            $parts[$i] = preg_replace_callback('/(placeholder|title|aria-label|alt)=(["\x27])(.*?)\2/su', function ($attr) {
                return $attr[1] . '=' . $attr[2] . to_new_latin($attr[3]) . $attr[2];
            }, $parts[$i]);
        }
    }
    $html = implode('', $parts);

    if (!empty($rawBlocks)) {
        $html = preg_replace_callback('/___RAW_BLOCK_(\d+)___/', function ($m) use ($rawBlocks) {
            return $rawBlocks[(int) $m[1]] ?? '';
        }, $html);
    }
    return $html;
}

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
        $text = $dict[$uz] ?? $dict[to_new_latin_html($uz)] ?? $uz;
    } else {
        $text = to_new_latin_html($text);
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
    return to_new_latin_html((string) ($row[$field] ?? ''));
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
    'Tarjimani yaşiriş', 'Tarjimani körsatiş', '+:n tanga hisobingizga qöşildi! Jami: :total.',
    'Zör takrorlaş! Tangalar bu dars uçun avval berilgan.', 'Ba’zi savollarga javob berilmagan. Baribir yakunlaysizmi?',
    'Tekşiriş', 'Tinglang', 'Nima deyildi?', 'Tinglaş', 'Tarjima qiling', '«:word» — bu nima?', 'Inglizça ma’nosini tanlang',
    'Özbekça toping', 'Özbekça qanday böladi?', 'Yozing', 'Özbekça yozing', 'Javob…', 'Barakalla!', 'Yaxşi harakat!',
    ':score / :total töğri', 'Endi yakuniy testga tayyorsiz!', 'Luğatni yana bir körib, qayta urinib köring.', 'Yana maşq qiliş',
    'Barakalla! Töğri.', 'Töğri javob:', 'Davom etiş', 'Maşq uçun kamida 2 ta söz kerak.',
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
