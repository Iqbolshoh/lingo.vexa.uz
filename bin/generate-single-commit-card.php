<?php

declare(strict_types=1);

function generateCommitCard(
    string $commitHash,
    string $timeStr,
    string $commitTitle,
    string $stepBadge,
    string $overview,
    array $highlights,
    string $outputPath
): void {
    $width = 1200;
    $height = 675;

    $img = imagecreatetruecolor($width, $height);
    imagealphablending($img, true);
    imagesavealpha($img, true);

    // Color definitions
    $bgDark = imagecolorallocate($img, 11, 15, 25);      // #0b0f19 very deep slate
    $headerBg = imagecolorallocate($img, 17, 24, 39);    // #111827
    $cardBg = imagecolorallocate($img, 24, 33, 53);      // #182135
    $cardBorder = imagecolorallocate($img, 45, 60, 90);  // #2d3c5a
    $primary = imagecolorallocate($img, 16, 185, 129);   // #10b981 emerald
    $cyan = imagecolorallocate($img, 6, 182, 212);       // #06b6d4 cyan
    $amber = imagecolorallocate($img, 245, 158, 11);     // #f59e0b amber
    $white = imagecolorallocate($img, 255, 255, 255);
    $grayText = imagecolorallocate($img, 203, 213, 225); // #cbd5e1
    $grayMuted = imagecolorallocate($img, 148, 163, 184);// #94a3b8
    $terminalBg = imagecolorallocate($img, 15, 23, 42);  // #0f172a

    // Base background
    imagefilledrectangle($img, 0, 0, $width, $height, $bgDark);

    // Glowing top bar (emerald gradient)
    for ($i = 0; $i < 6; $i++) {
        $c = imagecolorallocate($img, 16, (int)(185 - $i * 12), (int)(129 + $i * 12));
        imageline($img, 0, $i, $width, $i, $c);
    }

    $fontBold = '/usr/share/fonts/opentype/inter/Inter-Bold.otf';
    $fontSemi = '/usr/share/fonts/opentype/inter/Inter-SemiBold.otf';
    $fontReg = '/usr/share/fonts/opentype/inter/Inter-Regular.otf';

    // Step Badge (e.g. "1-O'ZGARISH / 21 TA")
    imagefilledrectangle($img, 60, 42, 280, 76, imagecolorallocate($img, 5, 150, 105));
    imagettftext($img, 12, 0, 80, 64, $white, $fontBold, $stepBadge);

    // Timestamp & Hash Badge
    imagefilledrectangle($img, 295, 42, 540, 76, imagecolorallocate($img, 30, 41, 59));
    imagettftext($img, 12, 0, 310, 64, $amber, $fontBold, '⏰ ' . $timeStr);
    imagettftext($img, 12, 0, 420, 64, $cyan, $fontBold, 'commit: ' . $commitHash);

    // Title
    imagettftext($img, 24, 0, 60, 126, $white, $fontBold, $commitTitle);

    // Overview subtitle
    imagettftext($img, 14, 0, 60, 164, $grayText, $fontSemi, $overview);

    // Big central card (Visual representation)
    $cardX1 = 60;
    $cardY1 = 195;
    $cardX2 = 1140;
    $cardY2 = 615;

    imagefilledrectangle($img, $cardX1, $cardY1, $cardX2, $cardY2, $cardBg);
    imagerectangle($img, $cardX1, $cardY1, $cardX2, $cardY2, $cardBorder);

    // Terminal mock top bar inside card
    imagefilledrectangle($img, $cardX1, $cardY1, $cardX2, $cardY1 + 42, $terminalBg);
    imagerectangle($img, $cardX1, $cardY1, $cardX2, $cardY1 + 42, $cardBorder);
    
    // Window dots
    imagefilledellipse($img, $cardX1 + 25, $cardY1 + 21, 12, 12, imagecolorallocate($img, 239, 68, 68));
    imagefilledellipse($img, $cardX1 + 45, $cardY1 + 21, 12, 12, imagecolorallocate($img, 245, 158, 11));
    imagefilledellipse($img, $cardX1 + 65, $cardY1 + 21, 12, 12, imagecolorallocate($img, 16, 185, 129));

    imagettftext($img, 11, 0, $cardX1 + 95, $cardY1 + 26, $grayMuted, $fontSemi, 'lingo.vexa.uz • Arxitektura & Komponent Tafsilotlari');

    // List of highlights
    $itemY = $cardY1 + 80;
    foreach ($highlights as $hl) {
        // Bullet tag
        imagefilledrectangle($img, $cardX1 + 30, $itemY - 18, $cardX1 + 170, $itemY + 8, imagecolorallocate($img, 15, 23, 42));
        imagerectangle($img, $cardX1 + 30, $itemY - 18, $cardX1 + 170, $itemY + 8, $primary);
        imagettftext($img, 11, 0, $cardX1 + 42, $itemY - 2, $primary, $fontBold, $hl['label']);

        // Description
        imagettftext($img, 13, 0, $cardX1 + 190, $itemY - 2, $white, $fontSemi, $hl['text']);
        imagettftext($img, 10, 0, $cardX1 + 190, $itemY + 20, $grayMuted, $fontReg, $hl['sub']);

        $itemY += 66;
    }

    imagepng($img, $outputPath, 9);
    imagedestroy($img);
}

// Generate for commit 1 (f4db803)
generateCommitCard(
    'f4db803',
    '09:45:12 (2026-11-22)',
    '1. Admin Panel, Modullar yuklanishi & Mobil PWA Banneri',
    '1-O‘ZGARISH / 21 TA',
    'Platformaning ilk ishga tushishi: admin boshqaruv moduli, o‘quv rejasi va mobil PWA banneri sozlandi.',
    [
        [
            'label' => '🛠️ ADMIN PANEL',
            'text' => 'Boshqaruv tizimida yangi o‘quv modullarini to‘g‘ridan-to‘g‘ri import qilish ta’minlandi',
            'sub' => 'Fayllar: src/bootstrap.php, src/learning.php — modullar xatosiz yuklanishi kafolatlandi.'
        ],
        [
            'label' => '📱 PWA BANNER',
            'text' => 'Mobil foydalanuvchilar uchun «Ilovani o‘rnatish» smart-banneri optimallashtirildi',
            'sub' => 'Fayllar: src/views/layout.php, public/assets/app-v3.css — ekranni to‘sib qo‘ymaydigan suzuvchi dizayn.'
        ],
        [
            'label' => '🌐 KOP-TILLILIK',
            'text' => 'Ingliz tili (EN) lokalizatsiyasi to‘liq yangilandi va tizim parametrlariga ulandi',
            'sub' => 'Fayl: src/lang/en.php — barcha onboarding va ta’lim bosqichlari uchun to‘liq tarjimalar.'
        ],
        [
            'label' => '🗄️ BAZA SXEMASI',
            'text' => 'O‘quvchilar progressi va statistikasi uchun SQL jadval ustunlari moslashtirildi',
            'sub' => 'Fayllar: src/schema.php, src/stats.php — tezkor hisobot va onboarding yozuvlari.'
        ],
    ],
    '/var/www/startups/lingo/public/og/change-1.png'
);

echo "Rasm tayyor: /var/www/startups/lingo/public/og/change-1.png\n";
