<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$chatId = telegram_dev_chat_id();
$topicId = telegram_dev_topic_id('releases'); // 18
$photoPath = __DIR__ . '/../public/og/historic-change-26.png';

$caption = implode("\n", [
    "🚀 <b>26-BOSQICH: JONLI VOQEALAR MONITORINGI VA GURUHGA AVTO-BILDIRISHNOMALAR</b>",
    "",
    "📅 <b>Sana:</b> 2026-11-22",
    "⏰ <b>Vaqt:</b> 11:41:18",
    "🌐 <b>Loyiha:</b> <a href=\"https://lingo.vexa.uz\">lingo.vexa.uz</a> (LingoUz)",
    "",
    "📝 <b>Batafsil ma’lumot:</b>",
    "• <b>Yangi talabalar monitoringi:</b> Har safar yangi o‘quvchi ro‘yxatdan o‘tganda (<code>action_register()</code>) uning ismi, emaili va maqsadi darhol guruhga xabar qilinadi.",
    "• <b>Yangi arizalar (Leads):</b> Ota-ona yoki sayohatchi kursga ariza qoldirganda (<code>action_enroll()</code>) telefon raqami va ma’lumotlari guruhga yetkaziladi.",
    "• <b>Muvaffaqiyatlar & Rekordlar:</b> Testdan 100% olgan talabalar, kurs bitiruvchilari (sertifikat) va 7/14/30 kunlik streak olovi yoqqan o‘quvchilar real-vaqtda guruhda e’lon qilib boriladi.",
]);

$buttons = [
    'inline_keyboard' => [
        [
            ['text' => '🌐 Platformaga O‘tish', 'url' => 'https://lingo.vexa.uz'],
            ['text' => '⚙️ Admin Panel', 'url' => 'https://lingo.vexa.uz/admin'],
        ]
    ]
];

$res = telegram_send_photo($chatId, $photoPath, $caption, $buttons, 'HTML', 'uz', $topicId);

echo "Natija: " . (!empty($res['ok']) ? "Muvaffaqiyatli yuborildi (message_id: " . ($res['result']['message_id'] ?? 'n/a') . ")" : "Xatolik: " . json_encode($res)) . "\n";
