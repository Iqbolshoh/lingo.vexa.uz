<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/i18n.php';

/**
 * Helper to draw a rounded rectangle in GD.
 */
function gd_draw_rounded_rect($im, int $x1, int $y1, int $x2, int $y2, int $radius, int $color): void
{
    imagefilledrectangle($im, $x1 + $radius, $y1, $x2 - $radius, $y2, $color);
    imagefilledrectangle($im, $x1, $y1 + $radius, $x2, $y2 - $radius, $color);
    imagefilledellipse($im, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($im, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($im, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($im, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
}

/**
 * Generates or retrieves cached OG profile card image (1200x630).
 */
function generate_og_profile_image(int $userId): string
{
    $cacheDir = __DIR__ . '/../public/og/profile';
    if (!is_dir($cacheDir)) {
        @mkdir($cacheDir, 0775, true);
    }

    $pdo = db();
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    if (!$user) {
        return generate_og_default_image();
    }

    $cacheFile = $cacheDir . '/' . $userId . '.png';
    // Regenerate if file missing or older than 10 minutes
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < 600)) {
        return $cacheFile;
    }

    $w = 1200;
    $h = 630;
    $im = imagecreatetruecolor($w, $h);

    // Rich Dark Cyan/Navy gradient (#0b172a to #0369a1)
    for ($y = 0; $y < $h; $y++) {
        $p = $y / $h;
        $r = (int) (11 + (3 - 11) * $p);
        $g = (int) (23 + (105 - 23) * $p);
        $b = (int) (42 + (161 - 42) * $p);
        $col = imagecolorallocate($im, $r, $g, $b);
        imageline($im, 0, $y, $w, $y, $col);
    }

    // Inner frosted container
    $glassBg = imagecolorallocatealpha($im, 255, 255, 255, 115);
    $glassBorder = imagecolorallocatealpha($im, 255, 255, 255, 95);
    gd_draw_rounded_rect($im, 40, 40, $w - 40, $h - 40, 24, $glassBg);

    // Fonts
    $fontBold = '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf';
    $fontReg = '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf';

    $white = imagecolorallocate($im, 255, 255, 255);
    $cyan = imagecolorallocate($im, 56, 189, 248);
    $gold = imagecolorallocate($im, 251, 191, 36);
    $emerald = imagecolorallocate($im, 52, 211, 153);
    $muted = imagecolorallocate($im, 203, 213, 225);

    // Brand Title
    imagettftext($im, 20, 0, 85, 105, $cyan, $fontBold, "LINGOUZ  ·  O‘ZBEK TILINI O‘RGANISH PLATFORMASI");

    // Student Display Name
    $displayName = to_new_latin($user['display_name']);
    if (mb_strlen($displayName) > 24) {
        $displayName = mb_substr($displayName, 0, 22) . '...';
    }
    imagettftext($im, 46, 0, 85, 200, $white, $fontBold, $displayName);

    // Subtitle / Level
    $levelName = to_new_latin($user['language_level'] ?: 'A0 Boshlang‘ich');
    imagettftext($im, 22, 0, 85, 250, $muted, $fontReg, "O‘quvchi profili  |  Daraja: " . $levelName);

    // Stat Box 1: Streak
    $boxBg = imagecolorallocatealpha($im, 0, 0, 0, 100);
    gd_draw_rounded_rect($im, 85, 290, 310, 410, 16, $boxBg);
    imagettftext($im, 17, 0, 110, 335, $gold, $fontBold, "KETMA-KETLIK");
    imagettftext($im, 34, 0, 110, 385, $white, $fontBold, (int) $user['streak_days'] . " kun");

    // Stat Box 2: XP
    gd_draw_rounded_rect($im, 330, 290, 555, 410, 16, $boxBg);
    imagettftext($im, 17, 0, 355, 335, $cyan, $fontBold, "UMUMIY XP");
    imagettftext($im, 34, 0, 355, 385, $white, $fontBold, (int) $user['xp'] . " XP");

    // Stat Box 3: Tangalar
    gd_draw_rounded_rect($im, 575, 290, 800, 410, 16, $boxBg);
    imagettftext($im, 17, 0, 600, 335, $emerald, $fontBold, "TANGALAR");
    imagettftext($im, 34, 0, 600, 385, $white, $fontBold, (int) $user['coins'] . " ta");

    // Footer info
    imagettftext($im, 18, 0, 85, 545, $white, $fontBold, "👉 lingo.vexa.uz");
    imagettftext($im, 16, 0, 260, 545, $muted, $fontReg, "— O‘zbek tilini qiziqarli darslar orqali o‘rganing!");

    // Composite Lingo Mascot
    $mascotPath = __DIR__ . '/../public/assets/icon-512.png';
    if (file_exists($mascotPath)) {
        $mascot = @imagecreatefrompng($mascotPath);
        if ($mascot) {
            $circleBg = imagecolorallocate($im, 255, 255, 255);
            imagefilledellipse($im, 980, 290, 270, 270, $circleBg);
            imagecopyresampled($im, $mascot, 860, 170, 0, 0, 240, 240, imagesx($mascot), imagesy($mascot));
            imagedestroy($mascot);
        }
    }

    imagepng($im, $cacheFile);
    imagedestroy($im);
    return $cacheFile;
}

/**
 * Generates or retrieves cached OG word card image (1200x630).
 */
function generate_og_word_image(int $wordId): string
{
    $cacheDir = __DIR__ . '/../public/og/word';
    if (!is_dir($cacheDir)) {
        @mkdir($cacheDir, 0775, true);
    }

    $pdo = db();
    $stmt = $pdo->prepare('SELECT * FROM vocabulary WHERE id = ?');
    $stmt->execute([$wordId]);
    $word = $stmt->fetch();
    if (!$word) {
        return generate_og_default_image();
    }

    $cacheFile = $cacheDir . '/' . $wordId . '.png';
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < 86400)) {
        return $cacheFile;
    }

    $w = 1200;
    $h = 630;
    $im = imagecreatetruecolor($w, $h);

    // Warm Turquoise Gradient (#0f766e to #0369a1)
    for ($y = 0; $y < $h; $y++) {
        $p = $y / $h;
        $r = (int) (15 + (3 - 15) * $p);
        $g = (int) (118 + (105 - 118) * $p);
        $b = (int) (110 + (161 - 110) * $p);
        $col = imagecolorallocate($im, $r, $g, $b);
        imageline($im, 0, $y, $w, $y, $col);
    }

    $fontBold = '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf';
    $fontReg = '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf';

    $white = imagecolorallocate($im, 255, 255, 255);
    $gold = imagecolorallocate($im, 254, 240, 138);
    $cyan = imagecolorallocate($im, 153, 246, 228);
    $muted = imagecolorallocate($im, 226, 232, 240);

    // Inner card
    $glassBg = imagecolorallocatealpha($im, 0, 0, 0, 110);
    gd_draw_rounded_rect($im, 40, 40, $w - 40, $h - 40, 24, $glassBg);

    imagettftext($im, 20, 0, 85, 105, $cyan, $fontBold, "LINGOUZ  ·  KUNLIK SO‘Z / WORD OF THE DAY");

    // Uzbek Word
    $wordUz = to_new_latin($word['word_uz']);
    imagettftext($im, 56, 0, 85, 220, $gold, $fontBold, $wordUz);

    // Pronunciation
    if (!empty($word['pronunciation'])) {
        imagettftext($im, 24, 0, 85, 275, $cyan, $fontReg, "[" . $word['pronunciation'] . "]");
    }

    // Translation
    imagettftext($im, 32, 0, 85, 350, $white, $fontBold, "Tarjimasi: " . $word['word_en']);

    // Example
    if (!empty($word['example'])) {
        $example = to_new_latin($word['example']);
        imagettftext($im, 22, 0, 85, 430, $muted, $fontReg, "Misol: «" . $example . "»");
    }

    imagettftext($im, 18, 0, 85, 545, $white, $fontBold, "👉 lingo.vexa.uz/flashcards");
    imagettftext($im, 16, 0, 380, 545, $muted, $fontReg, "— O‘zbek tilini audio va interaktiv o‘rganing!");

    // Mascot
    $mascotPath = __DIR__ . '/../public/assets/icon-512.png';
    if (file_exists($mascotPath)) {
        $mascot = @imagecreatefrompng($mascotPath);
        if ($mascot) {
            $circleBg = imagecolorallocate($im, 255, 255, 255);
            imagefilledellipse($im, 980, 290, 270, 270, $circleBg);
            imagecopyresampled($im, $mascot, 860, 170, 0, 0, 240, 240, imagesx($mascot), imagesy($mascot));
            imagedestroy($mascot);
        }
    }

    imagepng($im, $cacheFile);
    imagedestroy($im);
    return $cacheFile;
}

/**
 * Generates default platform OG image.
 */
function generate_og_default_image(): string
{
    $cacheDir = __DIR__ . '/../public/og';
    if (!is_dir($cacheDir)) {
        @mkdir($cacheDir, 0775, true);
    }
    $cacheFile = $cacheDir . '/default.png';
    if (file_exists($cacheFile)) {
        return $cacheFile;
    }

    $w = 1200;
    $h = 630;
    $im = imagecreatetruecolor($w, $h);

    for ($y = 0; $y < $h; $y++) {
        $p = $y / $h;
        $r = (int) (2 + (7 - 2) * $p);
        $g = (int) (132 + (89 - 132) * $p);
        $b = (int) (199 + (133 - 199) * $p);
        $col = imagecolorallocate($im, $r, $g, $b);
        imageline($im, 0, $y, $w, $y, $col);
    }

    $fontBold = '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf';
    $fontReg = '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf';

    $white = imagecolorallocate($im, 255, 255, 255);
    $gold = imagecolorallocate($im, 254, 240, 138);
    $cyan = imagecolorallocate($im, 224, 242, 254);

    imagettftext($im, 22, 0, 85, 120, $cyan, $fontBold, "LINGOUZ PLATFORMASI");
    imagettftext($im, 48, 0, 85, 230, $white, $fontBold, "O‘zbek Tilini O‘rganing!");
    imagettftext($im, 26, 0, 85, 300, $gold, $fontBold, "Learn Uzbek Online — Interaktiv, Ovozli va Qiziqarli");
    imagettftext($im, 20, 0, 85, 380, $white, $fontReg, "• 17+ Interaktiv darslar va video darsliklar");
    imagettftext($im, 20, 0, 85, 425, $white, $fontReg, "• Ovozli audio lug‘at va mikrofonda talaffuz tekshirish");
    imagettftext($im, 20, 0, 85, 470, $white, $fontReg, "• Rasmiy Telegram Bot va kunlik o‘quv trenajyori");

    imagettftext($im, 20, 0, 85, 550, $cyan, $fontBold, "👉 https://lingo.vexa.uz");

    $mascotPath = __DIR__ . '/../public/assets/icon-512.png';
    if (file_exists($mascotPath)) {
        $mascot = @imagecreatefrompng($mascotPath);
        if ($mascot) {
            $circleBg = imagecolorallocate($im, 255, 255, 255);
            imagefilledellipse($im, 980, 290, 270, 270, $circleBg);
            imagecopyresampled($im, $mascot, 860, 170, 0, 0, 240, 240, imagesx($mascot), imagesy($mascot));
            imagedestroy($mascot);
        }
    }

    imagepng($im, $cacheFile);
    imagedestroy($im);
    return $cacheFile;
}

/**
 * Controller actions for serving OG images.
 */
function action_og_profile(int $id): void
{
    $file = generate_og_profile_image($id);
    header('Content-Type: image/png');
    header('Cache-Control: public, max-age=3600');
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;
}

function action_og_word(int $id): void
{
    $file = generate_og_word_image($id);
    header('Content-Type: image/png');
    header('Cache-Control: public, max-age=86400');
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;
}

function action_og_default(): void
{
    $file = generate_og_default_image();
    header('Content-Type: image/png');
    header('Cache-Control: public, max-age=86400');
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;
}
