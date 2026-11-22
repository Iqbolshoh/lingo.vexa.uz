<?php

declare(strict_types=1);

$width = 1200;
$height = 800;

$img = imagecreatetruecolor($width, $height);
imagealphablending($img, true);
imagesavealpha($img, true);

// Colors (Telegram Desktop Dark Theme style)
$bgDark = imagecolorallocate($img, 14, 22, 33);        // #0e1621 TG Dark BG
$chatBg = imagecolorallocate($img, 23, 33, 43);        // #17212b
$headerBg = imagecolorallocate($img, 36, 47, 61);      // #242f3d
$bubbleBg = imagecolorallocate($img, 24, 37, 51);      // #182533
$btnBg = imagecolorallocate($img, 43, 82, 120);        // #2b5278
$btnHover = imagecolorallocate($img, 53, 98, 142);
$primary = imagecolorallocate($img, 16, 185, 129);     // emerald
$cyan = imagecolorallocate($img, 100, 181, 246);       // light blue
$white = imagecolorallocate($img, 255, 255, 255);
$grayLight = imagecolorallocate($img, 226, 232, 240);
$grayMuted = imagecolorallocate($img, 142, 158, 175);
$border = imagecolorallocate($img, 36, 47, 61);

imagefilledrectangle($img, 0, 0, $width, $height, $bgDark);

$fontBold = '/usr/share/fonts/opentype/inter/Inter-Bold.otf';
$fontSemi = '/usr/share/fonts/opentype/inter/Inter-SemiBold.otf';
$fontReg = '/usr/share/fonts/opentype/inter/Inter-Regular.otf';

// Telegram Chat Window Container
imagefilledrectangle($img, 100, 30, 1100, 770, $chatBg);
imagerectangle($img, 100, 30, 1100, 770, $border);

// Telegram Header
imagefilledrectangle($img, 100, 30, 1100, 105, $headerBg);
// Avatar circle
$avatarSrc = @imagecreatefrompng('/var/www/startups/lingo/public/icon-512.png');
if ($avatarSrc) {
    imagecopyresampled($img, $avatarSrc, 125, 42, 0, 0, 52, 52, imagesx($avatarSrc), imagesy($avatarSrc));
}
imagettftext($img, 15, 0, 195, 64, $white, $fontBold, 'LingoUz | O‘zbek Tili Akademiyasi');
imagettftext($img, 11, 0, 195, 87, $cyan, $fontReg, '@vexa_lingo_bot • rasmiy bot • online');

// Bot Message Bubble
imagefilledrectangle($img, 140, 130, 850, 420, $bubbleBg);
imagerectangle($img, 140, 130, 850, 420, $border);

imagettftext($img, 14, 0, 170, 170, $white, $fontBold, 'Assalomu alaykum! 👋 Men Lingo — sizning o‘zbek tili do‘stingizman.');
imagettftext($img, 12, 0, 170, 210, $grayLight, $fontReg, 'Platformadagi yutuqlaringiz, kunlik so‘zlar va mashqlar shu yerda aks etadi:');

$commands = [
    '• 📖 Bugungi so‘z (/word) — Kunlik yangi o‘zbekcha so‘z va ma’nosi',
    '• 🎯 Mini-test (/quiz) — Tezkor bilim tekshiruvchi viktorina',
    '• 🔥 Streak holati (/streak) — Ketma-ket kunlik o‘qish rekordingiz',
    '• 📊 Mening hisobim (/profile) — Shaxsiy XP, daraja va tangalar',
    '• 🌐 Tilni almashtirish (/lang) — O‘zbekcha / English',
    '• 🚀 Saytga kirish (/site) — lingo.vexa.uz platformasiga to‘g‘ridan-to‘g‘ri o‘tish',
];

$y = 250;
foreach ($commands as $cmd) {
    imagettftext($img, 11, 0, 170, $y, $grayLight, $fontReg, $cmd);
    $y += 26;
}

// Bottom Persistent Reply Keyboard Area
imagefilledrectangle($img, 100, 450, 1100, 770, $headerBg);
imagettftext($img, 12, 0, 140, 485, $cyan, $fontBold, '📱 8 TA DOIMIY MENYU TUGMASI (PERSISTENT KEYBOARD):');

// 8 Buttons Grid: 2 rows of 4 buttons
$btns = [
    '📖 Bugungi so‘z',
    '🎯 Mini-test',
    '🔥 Streak holati',
    '📊 Mening hisobim',
    '🏆 Reyting',
    '📚 Ertaklar',
    '🌐 Tilni o‘zgartirish',
    '🚀 Saytga kirish',
];

$startX = 130;
$startY = 510;
$bW = 220;
$bH = 100;
$gapX = 20;
$gapY = 16;

$idx = 0;
for ($r = 0; $r < 2; $r++) {
    for ($c = 0; $c < 4; $c++) {
        $bx = $startX + $c * ($bW + $gapX);
        $by = $startY + $r * ($bH + $gapY);
        $label = $btns[$idx++];
        
        imagefilledrectangle($img, $bx, $by, $bx + $bW, $by + $bH, $btnBg);
        imagerectangle($img, $bx, $by, $bx + $bW, $by + $bH, imagecolorallocate($img, 70, 120, 170));
        
        // Centered button text
        imagettftext($img, 12, 0, $bx + 20, $by + 58, $white, $fontSemi, $label);
    }
}

$out = '/var/www/startups/lingo/public/og/historic-change-24.png';
imagepng($img, $out, 9);
imagedestroy($img);
echo "Bot menyu skrinshoti yaratildi: $out\n";
