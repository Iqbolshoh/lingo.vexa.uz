<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$chatId = telegram_dev_chat_id();
$topicId = telegram_dev_topic_id('releases'); // 18
$photoPath = __DIR__ . '/../public/og/historic-change-18.png';

$caption = implode("\n", [
    "🚀 <b>18-BOSQICH: PWA BARQARORLIGI, SERVICE WORKER KESHI & TABIIY O‘ZBEKCHA TALAFFUZ (TTS)</b>",
    "",
    "📅 <b>Sana:</b> 2026-11-21",
    "⏰ <b>Vaqt:</b> 18:21:53",
    "🌐 <b>Loyiha:</b> <a href=\"https://lingo.vexa.uz\">lingo.vexa.uz</a> (LingoUz)",
    "",
    "📝 <b>Batafsil ma’lumot:</b>",
    "• <b>Tabiiy o‘zbekcha talaffuz (TTS):</b> Web Speech API orqali o‘zbek tili fonetikasi uchun maxsus urg‘u va fonetik o‘zgarishlarni to‘g‘ri aytadigan nutq sintezatori yo‘lga qo‘yildi (<code>public/assets/app.js</code>).",
    "• <b>PWA Service Worker optimallashtirildi:</b> Oflayn ishlashda va sahifalar yangilanganda konsolda chiqqan ogohlantirishlar butunlay tozalandi (<code>public/sw.js</code>).",
    "• <b>FastCGI va PHP View barqarorligi:</b> PHP-FPM shablonlarni render qilish jarayonidagi xavfsizlik va ruxsatlar to‘liq tekshirilib sozlandi.",
]);

$buttons = [
    'inline_keyboard' => [
        [
            ['text' => '🌐 Saytda ko‘rish', 'url' => 'https://lingo.vexa.uz'],
        ]
    ]
];

$res = telegram_send_photo($chatId, $photoPath, $caption, $buttons, 'HTML', 'uz', $topicId);

echo "Natija: " . (!empty($res['ok']) ? "Muvaffaqiyatli yuborildi (message_id: " . ($res['result']['message_id'] ?? 'n/a') . ")" : "Xatolik: " . json_encode($res)) . "\n";
