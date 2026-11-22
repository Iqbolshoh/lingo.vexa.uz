<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$chatId = telegram_dev_chat_id();
$topicId = telegram_dev_topic_id('releases'); // 18
$photoPath = __DIR__ . '/../public/og/historic-change-6.png';

$caption = implode("\n", [
    "🚀 <b>6-BOSQICH: SERTIFIKAT VA MATNLAR KONTRASTI, TOZA SVG & MOBIL KO‘RINISH</b>",
    "",
    "📅 <b>Sana:</b> 2026-11-18",
    "⏰ <b>Vaqt:</b> 16:35:24",
    "🌐 <b>Loyiha:</b> <a href=\"https://lingo.vexa.uz\">lingo.vexa.uz</a> (LingoUz)",
    "",
    "📝 <b>Batafsil ma’lumot:</b>",
    "• <b>Sertifikat (Diplom) sahifasi mukammallashtirildi:</b> O‘quvchi bitiruv sertifikati sahifasida matn o‘qilishi (kontrast), Registon naqshlari va bosma (print/PDF) stili yaxshilandi (<code>src/views/certificate.php</code>).",
    "• <b>Qoldiq emojilar butunlay tozalab tashlandi:</b> Dashboard, xatolik sahifasi (404) va ota-onalar qo‘llanmasidagi barcha qolib ketgan kulgichlar o‘rniga toza vektor SVG ikonkalar joylashtirildi.",
    "• <b>Ranglar ravshanligi:</b> Oq fonda xira ko‘ringan kulrang elementlar to‘qroq kontrast ranglarga o‘tkazildi.",
]);

$buttons = [
    'inline_keyboard' => [
        [
            ['text' => '🌐 Saytda ko‘rish', 'url' => 'https://lingo.vexa.uz/certificate'],
        ]
    ]
];

$res = telegram_send_photo($chatId, $photoPath, $caption, $buttons, 'HTML', 'uz', $topicId);

echo "Natija: " . (!empty($res['ok']) ? "Muvaffaqiyatli yuborildi (message_id: " . ($res['result']['message_id'] ?? 'n/a') . ")" : "Xatolik: " . json_encode($res)) . "\n";
