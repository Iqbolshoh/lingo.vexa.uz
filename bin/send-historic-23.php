<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$chatId = telegram_dev_chat_id();
$topicId = telegram_dev_topic_id('releases'); // 18
$photoPath = __DIR__ . '/../public/og/historic-change-23.png';

$caption = implode("\n", [
    "🚀 <b>23-BOSQICH: GAMIFIKATSIYA OVOZLARI VA G‘ALABA ANIMATSIYALARI (80%+ FANFARA)</b>",
    "",
    "📅 <b>Sana:</b> 2026-11-22",
    "⏰ <b>Vaqt:</b> 10:50:46",
    "🌐 <b>Loyiha:</b> <a href=\"https://lingo.vexa.uz\">lingo.vexa.uz</a> (LingoUz)",
    "",
    "📝 <b>Batafsil ma’lumot:</b>",
    "• <b>Test natijalarida tantanali fanfara:</b> O‘quvchi testdan 80%+ natija ko‘rsatganda tantanali g‘alaba tovushi va yorqin zarralar nurlanishi (confetti) ishga tushirildi (<code>src/views/quiz_result.php</code>).",
    "• <b>Streak olovi quvvati:</b> O‘quvchi kunlik ketma-ketlikni oshirganda olovli animatsiya va streak ovoz effekti ulandi.",
    "• <b>Talaffuz to‘lqini va Lingo sakrashi:</b> O‘quvchi so‘zlarni to‘g‘ri aytganda yoki tangalar qo‘shilganda qahramon quvonch bilan sakraydigan mikro-animatsiyalar joriy etildi.",
]);

$buttons = [
    'inline_keyboard' => [
        [
            ['text' => '🌐 Platformada ko‘rish', 'url' => 'https://lingo.vexa.uz'],
        ]
    ]
];

$res = telegram_send_photo($chatId, $photoPath, $caption, $buttons, 'HTML', 'uz', $topicId);

echo "Natija: " . (!empty($res['ok']) ? "Muvaffaqiyatli yuborildi (message_id: " . ($res['result']['message_id'] ?? 'n/a') . ")" : "Xatolik: " . json_encode($res)) . "\n";
