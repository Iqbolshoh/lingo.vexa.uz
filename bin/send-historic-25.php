<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$chatId = telegram_dev_chat_id();
$topicId = telegram_dev_topic_id('releases'); // 18
$photoPath = __DIR__ . '/../public/og/historic-change-25.png';

$caption = implode("\n", [
    "🚀 <b>25-BOSQICH: BOT TIL TUGMALARINI TOZALASH & DASTURCHILAR GURUHI INTEGRATSIYASI</b>",
    "",
    "📅 <b>Sana:</b> 2026-11-22",
    "⏰ <b>Vaqt:</b> 11:39:49",
    "🌐 <b>Loyiha:</b> <a href=\"https://lingo.vexa.uz\">lingo.vexa.uz</a> (LingoUz)",
    "",
    "📝 <b>Batafsil ma’lumot:</b>",
    "• <b>Til tugmalari standartlashtirildi:</b> Botdagi qavsli uzun yozuvlar o‘rniga qat'iy «🇺🇿 O‘zbekcha» va «🌐 Change language» deb belgilandi (<code>src/telegram.php</code>).",
    "• <b>Dasturchilar superguruhi ulandi:</b> Guruh ID: <code>-1003979299740</code> tizimga ulanib, platformadagi har qanday deploy va muhim o‘zgarishlarni guruhga xabar qiluvchi <code>bin/notify-deploy.php</code> moduli yaratildi.",
    "• <b>Admin sozlamalari:</b> Boshqaruv panelida Telegram guruh holati va token tekshiruvi integratsiyasi kuchaytirildi.",
]);

$buttons = [
    'inline_keyboard' => [
        [
            ['text' => '🌐 Platformaga O‘tish', 'url' => 'https://lingo.vexa.uz'],
            ['text' => '🤖 Rasmiy Bot', 'url' => 'https://t.me/' . telegram_bot_username()],
        ]
    ]
];

$res = telegram_send_photo($chatId, $photoPath, $caption, $buttons, 'HTML', 'uz', $topicId);

echo "Natija: " . (!empty($res['ok']) ? "Muvaffaqiyatli yuborildi (message_id: " . ($res['result']['message_id'] ?? 'n/a') . ")" : "Xatolik: " . json_encode($res)) . "\n";
