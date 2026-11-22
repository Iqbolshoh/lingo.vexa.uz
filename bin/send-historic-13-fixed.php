<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$chatId = telegram_dev_chat_id();
$topicId = telegram_dev_topic_id('releases'); // 18
$photoPath = __DIR__ . '/../public/og/historic-change-13-real.png';

$caption = implode("\n", [
    "🚀 <b>13-BOSQICH: OVOZLI TALAFFUZNI BAHOLASH (SPEECH-TO-TEXT) & YANGI LOTIN IMLOSI (HAQIQIY DARS SAHIFASI)</b>",
    "",
    "📅 <b>Sana:</b> 2026-11-21",
    "⏰ <b>Vaqt:</b> 16:52:55",
    "🌐 <b>Loyiha:</b> <a href=\"https://lingo.vexa.uz\">lingo.vexa.uz</a> (LingoUz)",
    "",
    "📝 <b>Batafsil ma’lumot:</b>",
    "• <b>Interaktiv dars muhiti (Haqiqiy ekran):</b> Dars sarlavhasi «Salomlaşiş va Özini Taniştiriş», so‘zlar lug‘ati, audio talaffuz to‘lqini va mikrofon orqali gapirish moduli to‘liq aks ettirildi.",
    "• <b>Speech Recognition & Pronunciation:</b> O‘quvchi mikrofon orqali gapirganda Web Speech API talaffuz aniqligini (foizda) baholab, xatolarni ko‘rsatadi.",
    "• <b>Yangi lotin imlosi:</b> Barcha darsliklar rasmiy yangi o‘zbek lotin imlosiga («Ö/ö», «Ş/ş», «Ç/ç», «Ğ/ğ») o‘tkazildi.",
]);

$buttons = [
    'inline_keyboard' => [
        [
            ['text' => '🌐 Darsni ko‘rish', 'url' => 'https://lingo.vexa.uz/courses'],
        ]
    ]
];

$res = telegram_send_photo($chatId, $photoPath, $caption, $buttons, 'HTML', 'uz', $topicId);

echo "Natija: " . (!empty($res['ok']) ? "Muvaffaqiyatli yuborildi (message_id: " . ($res['result']['message_id'] ?? 'n/a') . ")" : "Xatolik: " . json_encode($res)) . "\n";
