<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$chatId = telegram_dev_chat_id();
$topicId = telegram_dev_topic_id('releases'); // 18
$photoPath = __DIR__ . '/../public/og/historic-change-17.png';

$caption = implode("\n", [
    "🚀 <b>17-BOSQICH: TO‘LIQ LARAVEL 13 FRAMEWORKIGA O‘TISH & ELOQUENT MODELLAR</b>",
    "",
    "📅 <b>Sana:</b> 2026-11-21",
    "⏰ <b>Vaqt:</b> 18:07:27",
    "🌐 <b>Loyiha:</b> <a href=\"https://lingo.vexa.uz\">lingo.vexa.uz</a> (LingoUz)",
    "",
    "📝 <b>Batafsil ma’lumot:</b>",
    "• <b>Laravel 13 yadrosi:</b> Loyiha arxitekturasi yengil skriptdan to‘laqonli enterprise darajadagi eng so‘nggi Laravel 13 frameworkiga o‘tkazildi (<code>config/</code>, <code>routes/web.php</code>, <code>bootstrap/providers.php</code>).",
    "• <b>Eloquent ORM & Query Builder:</b> Ma’lumotlar bazasi so‘rovlari, munosabatlar (relationships) va xavfsizlik filtrlari Laravel Eloquent orqali boshqariladigan bo‘ldi.",
    "• <b>Avtomatlashtirilgan kesh va seanslar:</b> Redis va faylli drayverlar, Artisan konsol buyruqlari quvvati to‘liq ulandi.",
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
