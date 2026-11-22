<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$chatId = telegram_dev_chat_id();
$topicId = telegram_dev_topic_id('releases'); // 18
$photoPath = __DIR__ . '/../public/og/historic-change-32.png';

$caption = implode("\n", [
    "✨ <b>32-BOSQICH: TO‘LIQ DIZAYN POLISH VA UI/UX YAXSHILANISHLARI (PWA V10)</b>",
    "",
    "📅 <b>Sana:</b> 2026-11-22",
    "⏰ <b>Vaqt:</b> 13:10:19",
    "🔗 <b>Commit:</b> <code>b40e686</code>",
    "🌐 <b>Loyiha:</b> <a href=\"https://lingo.vexa.uz\">lingo.vexa.uz</a> (LingoUz)",
    "",
    "📝 <b>Batafsil ma’lumot:</b>",
    "• <b>Ovozli ertaklar (Stories & Reader):</b> Inline stillar to‘liq tozalanib, zamonaviy CSS Grid va Card tizimiga o‘tkazildi. Qiroat vaqtida audio tugmaga to‘lqinli animatsiya (pulse ring) ulandi.",
    "• <b>Reyting jadvali (Leaderboard):</b> Foydalanuvchining o‘z qatorini ajratuvchi <code>.my-rank-row</code> moviy foni va ko‘k indikatori ishga tushirildi. Shohsupa nishonlari, streak olovi va dars pillari maxsus stillandi.",
    "• <b>Rasmiy sertifikat tekshiruvi:</b> Tekshiruv sahifasi to‘liq responsiv kartochka formatiga keltirilib, rasmiy muhr va ma’lumotlar paneli mobil ekranlarga moslashtirildi.",
    "• <b>Mikro-interaksiyalar va PWA v10:</b> Tugma va kartalarga yengil bosilish (active state) effekti qo‘shildi. Barcha uslublar darhol yangilanishi uchun Service Worker <code>lingouz-pwa-v10</code> ga oshirildi.",
]);

$buttons = [
    'inline_keyboard' => [
        [
            ['text' => '🌐 Saytda Ko‘rish', 'url' => 'https://lingo.vexa.uz/stories'],
        ]
    ]
];

$res = telegram_send_photo($chatId, $photoPath, $caption, $buttons, 'HTML', 'uz', $topicId);

echo "Natija: " . (!empty($res['ok']) ? "Muvaffaqiyatli yuborildi (message_id: " . ($res['result']['message_id'] ?? 'n/a') . ")" : "Xatolik: " . json_encode($res)) . "\n";
