<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$chatId = telegram_dev_chat_id();
$topicId = telegram_dev_topic_id('releases'); // 18
$photoPath = __DIR__ . '/../public/og/historic-change-1.png';

$caption = implode("\n", [
    "🚀 <b>LOYIHAGA ASOS SOLINISHI — 1-BOSQICH (ENG BIRINCHI PAYTIDAGI ASLIY KO‘RINIShI)</b>",
    "",
    "📅 <b>Sana:</b> 2026-11-18",
    "⏰ <b>Vaqt:</b> 16:05:53",
    "🌐 <b>Loyiha:</b> <a href=\"https://lingo.vexa.uz\">lingo.vexa.uz</a> (LingoUz)",
    "",
    "📸 <i>Yuqoridagi rasm — loyiha ilk bor 2026-11-18 da ishga tushirilgan daqiqasidagi platformaning 100% asl tarixiy skrinshoti!</i>",
    "",
    "📝 <b>Batafsil ma’lumot:</b>",
    "• <b>Dastlabki MVP ko‘rinishi:</b> Yashil ranglar gammasi, ilk 5 ta interaktiv dars, 80% o‘tish cheklovi va talaffuz audiolari.",
    "• <b>Boshqaruv tizimi:</b> O‘quvchi paneli (Dashboard) va ilk ota-onalar arizasi (Enrollment) shakllantirilgan.",
    "• <b>Baza arxitekturasi:</b> Darslar, savollar va progress jadvallari tuzilgan.",
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
