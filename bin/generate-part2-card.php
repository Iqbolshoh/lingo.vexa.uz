<?php

declare(strict_types=1);

$width = 1200;
$height = 675;

$img = imagecreatetruecolor($width, $height);
imagealphablending($img, true);
imagesavealpha($img, true);

// Colors
$bgDark = imagecolorallocate($img, 15, 23, 42); // #0f172a slate 900
$cardBg = imagecolorallocate($img, 30, 41, 59); // #1e293b slate 800
$cardBorder = imagecolorallocate($img, 51, 65, 85); // #334155
$primary = imagecolorallocate($img, 16, 185, 129); // #10b981 emerald 500
$cyan = imagecolorallocate($img, 6, 182, 212); // #06b6d4 cyan 500
$accent = imagecolorallocate($img, 245, 158, 11); // #f59e0b amber 500
$white = imagecolorallocate($img, 255, 255, 255);
$grayLight = imagecolorallocate($img, 226, 232, 240); // #e2e8f0
$grayMuted = imagecolorallocate($img, 148, 163, 184); // #94a3b8
$tagBg = imagecolorallocate($img, 4, 120, 87); // emerald dark

imagefilledrectangle($img, 0, 0, $width, $height, $bgDark);

// Top glowing gradient strip
for ($i = 0; $i < 6; $i++) {
    $c = imagecolorallocate($img, 16, (int)(185 - $i * 10), (int)(129 + $i * 15));
    imageline($img, 0, $i, $width, $i, $c);
}

$fontBold = '/usr/share/fonts/opentype/inter/Inter-Bold.otf';
$fontSemi = '/usr/share/fonts/opentype/inter/Inter-SemiBold.otf';
$fontReg = '/usr/share/fonts/opentype/inter/Inter-Regular.otf';

// Pill badge
imagefilledrectangle($img, 60, 40, 310, 74, $tagBg);
imagettftext($img, 13, 0, 80, 63, $white, $fontBold, 'LingoUz • 2026-11-22 Reliz');

// Title
imagettftext($img, 28, 0, 60, 125, $white, $fontBold, '2-QISM: Bot Menyusi, Guruh, Topiclar & Header');

// Subtitle
imagettftext($img, 14, 0, 60, 160, $cyan, $fontSemi, 'Xronologik O‘zgarishlar Tafsiloti (10:47 – 11:54) • 10 ta Muhim Yangilanish');

// 2 Columns of cards (5 items each)
$itemsCol1 = [
    ['time' => '10:47', 'hash' => '4b4445e', 'title' => 'PWA v8 Kesh & Lingo Timsoli', 'desc' => 'Service Worker v8 ishga tushirildi, kesh yangilandi, timsol nomi Lingo deb belgilandi.'],
    ['time' => '10:50', 'hash' => '4c9a928', 'title' => 'Gamifikatsiya Tovushlari', 'desc' => 'Test natijalari (80%+ fanfara), streak olovi, diplom sahifasi va sakrash effektlari.'],
    ['time' => '11:11', 'hash' => 'cea759f', 'title' => 'Bot Nomi & Rasmiy Menyu', 'desc' => 'LingoUz | O‘zbek Tili Akademiyasi, rasmiy avatar yuklandi, 8 ta menyu tugmasi.'],
    ['time' => '11:39', 'hash' => 'a921bbe', 'title' => 'Dasturchilar Guruhi & Til', 'desc' => 'Bot til tugmalari tozalandi (O‘zbekcha, Change language), notify-deploy ulandi.'],
    ['time' => '11:41', 'hash' => '6232903', 'title' => 'Hodisalar Monitoringi (Alerts)', 'desc' => 'Yangi o‘quvchi, arizalar, 100% test natijasi va streak rekordlari avto-bildirishnomasi.'],
];

$itemsCol2 = [
    ['time' => '11:43', 'hash' => '93196d0', 'title' => 'FastCGI Log Xatosi Bartaraf', 'desc' => 'Bot loglari telegram.log ga ajratildi, Nginx stderr xatoliklari 0 ga tushirildi.'],
    ['time' => '11:47', 'hash' => '08f54b4', 'title' => 'Guruh Forum Topic Ajratilishi', 'desc' => 'Guruh 6 ta Forum Topic ga ajratildi va har bir xabarnoma o‘z mavzusiga yo‘naltirildi.'],
    ['time' => '11:48', 'hash' => '3626a03', 'title' => 'Qisqa Inglizcha Topic Nomlari', 'desc' => 'Releases, Students, Leads, Milestones, Reports, Alerts mavzulariga tartiblandi.'],
    ['time' => '11:49', 'hash' => 'de87c61', 'title' => 'Guruh Xabarlarini Filtrlash', 'desc' => 'Guruh suhbatlari filtrlandi, bot guruhda jim turishi va shaxsiyda javob berishi sozlandi.'],
    ['time' => '11:54', 'hash' => '3aca3dc', 'title' => 'Zamonaviy Responsiv Header', 'desc' => 'Kapsula menyu, ixcham asboblar, ortiqcha havolalar olib tashlandi, PWA v9 yangilandi.'],
];

function drawCardCol($img, $items, $startX, $startY, $cardWidth, $cardHeight, $gap, $cardBg, $cardBorder, $primary, $accent, $white, $grayLight, $grayMuted, $fontBold, $fontSemi, $fontReg) {
    $y = $startY;
    foreach ($items as $item) {
        // Card Box
        imagefilledrectangle($img, $startX, $y, $startX + $cardWidth, $y + $cardHeight, $cardBg);
        imagerectangle($img, $startX, $y, $startX + $cardWidth, $y + $cardHeight, $cardBorder);
        
        // Time & Commit Hash
        $timeText = $item['time'];
        $hashText = '[' . $item['hash'] . ']';
        imagettftext($img, 11, 0, $startX + 14, $y + 22, $accent, $fontBold, $timeText);
        imagettftext($img, 10, 0, $startX + 62, $y + 22, $primary, $fontSemi, $hashText);
        
        // Title
        imagettftext($img, 12, 0, $startX + 14, $y + 45, $white, $fontBold, $item['title']);
        
        // Desc
        imagettftext($img, 9, 0, $startX + 14, $y + 68, $grayMuted, $fontReg, $item['desc']);
        
        $y += $cardHeight + $gap;
    }
}

drawCardCol($img, $itemsCol1, 60, 195, 520, 84, 10, $cardBg, $cardBorder, $primary, $accent, $white, $grayLight, $grayMuted, $fontBold, $fontSemi, $fontReg);
drawCardCol($img, $itemsCol2, 620, 195, 520, 84, 10, $cardBg, $cardBorder, $primary, $accent, $white, $grayLight, $grayMuted, $fontBold, $fontSemi, $fontReg);

$outPath = '/var/www/startups/lingo/public/og/part2-changelog.png';
imagepng($img, $outPath, 9);
imagedestroy($img);

echo "Rasm muvaffaqiyatli yaratildi: $outPath (" . filesize($outPath) . " bytes)\n";
