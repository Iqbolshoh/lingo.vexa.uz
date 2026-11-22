<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$chatId = telegram_dev_chat_id();
$topicId = telegram_dev_topic_id('releases'); // 18
$photoPath = __DIR__ . '/../public/og/real-change-2.png';

$caption = implode("\n", [
    "📌 <b>2-O‘ZGARISH (Bugungi 21 ta o‘zgarishdan 2-si)</b>",
    "",
    "⏰ <b>Vaqt:</b> 2026-11-22 09:46:22",
    "🚀 <b>Yo‘nalish:</b> <b>PHP Seanslari Xavfsizligi & Tizimga Kirish Barqarorligi</b>",
    "",
    "📝 <b>Batafsil izoh:</b>",
    "• <b>Seanslar izolyatsiyasi:</b> Foydalanuvchilar sessiyalari serverning umumiy <code>/tmp</code> katalogidan mustaqil <code>storage/framework/sessions</code> papkasiga o‘tkazildi.",
    "• <b>Xavfsizlik & Ruxsatlar:</b> Boshqa loyihalar yoki umumiy server jarayonlari bilan sessiya to‘qnashuvlari butunlay bartaraf etildi.",
    "• <b>Uzluksiz avtorizatsiya:</b> O‘quvchilar va adminlarning tizimdan o‘z-o‘zidan chiqib ketishi (session timeout/permission denied) oldi olindi.",
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
