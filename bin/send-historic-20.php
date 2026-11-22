<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$chatId = telegram_dev_chat_id();
$topicId = telegram_dev_topic_id('releases'); // 18
$photoPath = __DIR__ . '/../public/og/historic-change-20.png';

$caption = implode("\n", [
    "🚀 <b>20-BOSQICH: RASMIY TELEGRAM BOT INTEGRATSIYASI VA 1-BOSISHDA ULANISH BANNERI</b>",
    "",
    "📅 <b>Sana:</b> 2026-11-22",
    "⏰ <b>Vaqt:</b> 10:15:27",
    "🌐 <b>Loyiha:</b> <a href=\"https://lingo.vexa.uz\">lingo.vexa.uz</a> (LingoUz)",
    "",
    "📝 <b>Batafsil ma’lumot:</b>",
    "• <b>Telegram bot integratsiyasi:</b> Rasmiy @vexa_lingo_bot webhooki ulandi, avtomatik xabar almashish mexanizmlari ishga tushirildi (<code>src/telegram.php</code>).",
    "• <b>Dashboardda 1-bosishda ulanuvchi banner:</b> O‘quvchi shaxsiy kabinetida «Telegram Botni Ulang» interaktiv ko‘k banneri paydo bo‘lib, o‘quvchiga 1 ta bosishda hisobini bot bilan sinxronlash imkonini berdi.",
    "• <b>Kunlik so‘z va streak eslatmalari:</b> Har kuni ertalab soat 09:00 da yangi o‘zbekcha so‘z va kechki soat 20:00 da streak yo‘qotmaslik eslatmasi cron rejalashtirildi.",
]);

$buttons = [
    'inline_keyboard' => [
        [
            ['text' => '🌐 Dashboardni ko‘rish', 'url' => 'https://lingo.vexa.uz/dashboard'],
            ['text' => '🤖 Rasmiy Bot', 'url' => 'https://t.me/' . telegram_bot_username()],
        ]
    ]
];

$res = telegram_send_photo($chatId, $photoPath, $caption, $buttons, 'HTML', 'uz', $topicId);

echo "Natija: " . (!empty($res['ok']) ? "Muvaffaqiyatli yuborildi (message_id: " . ($res['result']['message_id'] ?? 'n/a') . ")" : "Xatolik: " . json_encode($res)) . "\n";
