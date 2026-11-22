<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$chatId = telegram_dev_chat_id();
$topicId = telegram_dev_topic_id('releases'); // 18
$photoPath = __DIR__ . '/../public/og/historic-change-7.png';

$caption = implode("\n", [
    "🚀 <b>7-BOSQICH: LOGIN SAHIFASINI TOZALASH & QISQA LOGIN BILAN KIRISH</b>",
    "",
    "📅 <b>Sana:</b> 2026-11-18",
    "⏰ <b>Vaqt:</b> 16:37:22",
    "🌐 <b>Loyiha:</b> <a href=\"https://lingo.vexa.uz\">lingo.vexa.uz</a> (LingoUz)",
    "",
    "📝 <b>Batafsil ma’lumot:</b>",
    "• <b>Ortiqcha tester qutichasi olib tashlandi:</b> Kirish sahifasida turgan xunuk «Test hisob ma'lumotlari» qutichasi butunlay o‘chirilib, forma toza va professional ko‘rinishga keltirildi (<code>src/views/login.php</code>).",
    "• <b>Qisqa username bilan kirish:</b> O‘quvchi va adminlar faqat uzun email emas, balki qisqa foydalanuvchi nomi (masalan: `admin`, `demo`) orqali ham to‘g‘ridan-to‘g‘ri tizimga kira oladigan qilindi.",
    "• <b>Xavfsizlik:</b> Parolni tekshirish mexanizmi va xato kiritilganda chiquvchi ogohlantirishlar takomillashtirildi.",
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
