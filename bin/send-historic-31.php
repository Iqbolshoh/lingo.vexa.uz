<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$chatId = telegram_dev_chat_id();
$topicId = telegram_dev_topic_id('releases'); // 18
$photoPath = __DIR__ . '/../public/og/historic-change-31.png';

$caption = implode("\n", [
    "🏁 <b>31-BOSQICH (YAKUNIY BOSQICH): DASTURCHILAR GURUHI RASMIY BRENDINGI VA TO‘LIQ SIKL YAKUNI</b>",
    "",
    "📅 <b>Sana:</b> 2026-11-22",
    "⏰ <b>Vaqt:</b> 12:18:00",
    "🌐 <b>Loyiha:</b> <a href=\"https://lingo.vexa.uz\">lingo.vexa.uz</a> (LingoUz)",
    "",
    "📝 <b>Batafsil ma’lumot:</b>",
    "• <b>Rasmiy guruh nomi (Title):</b> Guruh nomi rasmiy «LingoUz | Dev & System Ops» deb belgilandi.",
    "• <b>Rasmiy guruh avatari:</b> Guruhga platformaning yuqori sifatli toza oq fonli «Lingo» timsoli avatari yuklandi.",
    "• <b>Guruh tavsifi (Description):</b> Dasturchilar va monitoring maqsadi aniq qayd etildi.",
    "• <b>31 ta bosqich yakunlandi:</b> 2026-11-18 dagi ilk commitdan boshlab hozirgi soniyagacha bo‘lgan har bir o‘zgarish asl skrinshotlari va aniq vaqti bilan xronologik tarzda muvaffaqiyatli saqlandi va yetkazildi!",
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
