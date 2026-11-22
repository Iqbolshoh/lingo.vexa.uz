<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$chatId = telegram_dev_chat_id();
$topicId = telegram_dev_topic_id('releases'); // 18
$photoPath = __DIR__ . '/../public/og/historic-change-19.png';

$caption = implode("\n", [
    "🚀 <b>19-BOSQICH: QAHRAMON LINGO TIMSOLI, OQ FONLI FAVICON VA PWA PIKTOGRAMMALARI</b>",
    "",
    "📅 <b>Sana:</b> 2026-11-22",
    "⏰ <b>Vaqt:</b> 10:06:42",
    "🌐 <b>Loyiha:</b> <a href=\"https://lingo.vexa.uz\">lingo.vexa.uz</a> (LingoUz)",
    "",
    "📝 <b>Batafsil ma’lumot:</b>",
    "• <b>Toza va yorqin Lingo timsoli:</b> Eski xira qora qoralama belgi butunlay olib tashlanib, shaffof fonli, katta ko‘zli, quvnoq va do‘stona boyqush «Lingo» piktogrammasi chizildi (<code>public/favicon.svg</code>, <code>public/assets/icon-512.png</code>).",
    "• <b>Toza oq fonli mobil ilova piktogrammasi:</b> Android va iOS smartfonlari uchun PWA ilova piktogrammalari zamonaviy toza oq fonda (#ffffff) tayyorlandi.",
    "• <b>Brauzer keshini majburiy tozalash:</b> Foydalanuvchilar brauzerida eski keshdagi noaniq suratlar qolib ketmasligi uchun <code>?v=...</code> kesh tozalash mexanizmi ulandi.",
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
