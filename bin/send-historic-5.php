<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$chatId = telegram_dev_chat_id();
$topicId = telegram_dev_topic_id('releases'); // 18
$photoPath = __DIR__ . '/../public/og/historic-change-5.png';

$caption = implode("\n", [
    "🚀 <b>5-BOSQICH: MOBIL VA PLANSHETLAR UCHUN OFF-CANVAS DRAWER MENYU</b>",
    "",
    "📅 <b>Sana:</b> 2026-11-18",
    "⏰ <b>Vaqt:</b> 16:27:55",
    "🌐 <b>Loyiha:</b> <a href=\"https://lingo.vexa.uz\">lingo.vexa.uz</a> (LingoUz)",
    "",
    "📝 <b>Batafsil ma’lumot:</b>",
    "• <b>Off-canvas Drawer menyu:</b> Kichik ekranli qurilmalarda menyu tiqilmasligi uchun silliq ochiluvchi yon panel (drawer) va hamburger tugmasi joriy qilindi.",
    "• <b>Foydalanuvchi ismini qisqartirish (Truncation):</b> Uzun ismli o‘quvchilar kirganda header buzilmasligi uchun nomlar avtomatik qisqartirildi (masalan: «Iqbolshoh...»).",
    "• <b>Navigatsiya sayqallanishi:</b> Darslar, natijalar, profil va tizimdan chiqish tugmalari mobil interfeysga moslab qayta tartiblandi.",
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
