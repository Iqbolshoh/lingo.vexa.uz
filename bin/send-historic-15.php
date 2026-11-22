<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$chatId = telegram_dev_chat_id();
$topicId = telegram_dev_topic_id('releases'); // 18
$photoPath = __DIR__ . '/../public/og/historic-change-15.png';

$caption = implode("\n", [
    "🚀 <b>15-BOSQICH: REYTING (LEADERBOARD), SMART FLASHCARDS VA PWA ILОВАSI</b>",
    "",
    "📅 <b>Sana:</b> 2026-11-21",
    "⏰ <b>Vaqt:</b> 17:51:42",
    "🌐 <b>Loyiha:</b> <a href=\"https://lingo.vexa.uz\">lingo.vexa.uz</a> (LingoUz)",
    "",
    "📝 <b>Batafsil ma’lumot:</b>",
    "• <b>Jonli O‘quvchilar Reytingi (/leaderboard):</b> Butun platforma bo‘yicha eng faol o‘quvchilar top-ro‘yxati, XP ballari, darajalari va medallar podiumi ishga tushirildi (<code>src/views/leaderboard.php</code>).",
    "• <b>Smart Flashcards trenajyori (/flashcards):</b> So‘zlarni mustahkamlash uchun intervalli takrorlash algoritmi (leitner tizimi) va ovozli talaffuzli kartochkalar ulandi.",
    "• <b>PWA (Progressive Web App):</b> Saytni telefonga mustaqil ilova qilib o‘rnatish (manifest, oflayn Service Worker va ilova piktogrammalari) yo‘lga qo‘yildi.",
]);

$buttons = [
    'inline_keyboard' => [
        [
            ['text' => '🌐 Reytingni ko‘rish', 'url' => 'https://lingo.vexa.uz/leaderboard'],
        ]
    ]
];

$res = telegram_send_photo($chatId, $photoPath, $caption, $buttons, 'HTML', 'uz', $topicId);

echo "Natija: " . (!empty($res['ok']) ? "Muvaffaqiyatli yuborildi (message_id: " . ($res['result']['message_id'] ?? 'n/a') . ")" : "Xatolik: " . json_encode($res)) . "\n";
