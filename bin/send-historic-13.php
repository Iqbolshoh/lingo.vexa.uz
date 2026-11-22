<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$chatId = telegram_dev_chat_id();
$topicId = telegram_dev_topic_id('releases'); // 18
$photoPath = __DIR__ . '/../public/og/historic-change-13.png';

$caption = implode("\n", [
    "🚀 <b>13-BOSQICH: OVOZLI TALAFFUZNI BAHOLASH (SPEECH-TO-TEXT) & YANGI LOTIN IMLOSI</b>",
    "",
    "📅 <b>Sana:</b> 2026-11-21",
    "⏰ <b>Vaqt:</b> 16:52:55",
    "🌐 <b>Loyiha:</b> <a href=\"https://lingo.vexa.uz\">lingo.vexa.uz</a> (LingoUz)",
    "",
    "📝 <b>Batafsil ma’lumot:</b>",
    "• <b>Ovozni tanish va talaffuzni tekshirish (STT):</b> Darslarda o‘quvchilar mikrofon orqali o‘zbekcha so‘zlarni aytganda tizim real vaqt rejimida talaffuzni tahlil qilib ball beradigan mexanizm ishga tushirildi (<code>public/assets/lesson.js</code>).",
    "• <b>Qat'iy yangi lotin alifbosi standarti:</b> Barcha darslar, mashqlar va lug‘atlar rasmiy yangi o‘zbek lotin imlosiga («Ö/ö», «Ş/ş», «Ç/ç», «Ğ/ğ») to‘liq o‘tkazildi.",
    "• <b>Dars interfeysi mukammalligi:</b> Savollar bloki, audio to‘lqin va talaffuz tugmasi o‘quvchi uchun eng qulay holatga keltirildi.",
]);

$buttons = [
    'inline_keyboard' => [
        [
            ['text' => '🌐 Darsni ko‘rish', 'url' => 'https://lingo.vexa.uz/courses/bolalar/lessons/1'],
        ]
    ]
];

$res = telegram_send_photo($chatId, $photoPath, $caption, $buttons, 'HTML', 'uz', $topicId);

echo "Natija: " . (!empty($res['ok']) ? "Muvaffaqiyatli yuborildi (message_id: " . ($res['result']['message_id'] ?? 'n/a') . ")" : "Xatolik: " . json_encode($res)) . "\n";
