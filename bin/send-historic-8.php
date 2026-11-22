<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$chatId = telegram_dev_chat_id();
$topicId = telegram_dev_topic_id('releases'); // 18
$photoPath = __DIR__ . '/../public/og/historic-change-8.png';

$caption = implode("\n", [
    "🚀 <b>8-BOSQICH: TIZIMDAN CHIQISH (LOGOUT) VA AKKAUNT ALMASHTIRISH OQIMI</b>",
    "",
    "📅 <b>Sana:</b> 2026-11-18",
    "⏰ <b>Vaqt:</b> 16:46:58",
    "🌐 <b>Loyiha:</b> <a href=\"https://lingo.vexa.uz\">lingo.vexa.uz</a> (LingoUz)",
    "",
    "📝 <b>Batafsil ma’lumot:</b>",
    "• <b>GET /logout so‘rovi qo‘llab-quvvatlandi:</b> Foydalanuvchilar to‘g‘ridan-to‘g‘ri havola yoki tugma orqali sessiyadan xavfsiz chiqib ketishi ta’minlandi.",
    "• <b>Ko‘rinarli chiqish tugmalari:</b> Mobil drawer menyusida va desktop sarlavhasida aniq qizil/kulrang chiqish (Logout) tugmalari o‘rnatildi.",
    "• <b>To‘g‘ridan-to‘g‘ri akkaunt almashtirish:</b> Tizimga allaqachon kirgan bo‘lsa ham, login sahifasiga o‘tib boshqa profilga (masalan: talabadan admin profiliga) o‘tish imkoni berildi.",
]);

$buttons = [
    'inline_keyboard' => [
        [
            ['text' => '🌐 Saytda ko‘rish', 'url' => 'https://lingo.vexa.uz/login'],
        ]
    ]
];

$res = telegram_send_photo($chatId, $photoPath, $caption, $buttons, 'HTML', 'uz', $topicId);

echo "Natija: " . (!empty($res['ok']) ? "Muvaffaqiyatli yuborildi (message_id: " . ($res['result']['message_id'] ?? 'n/a') . ")" : "Xatolik: " . json_encode($res)) . "\n";
