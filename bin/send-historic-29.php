<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$chatId = telegram_dev_chat_id();
$topicId = telegram_dev_topic_id('releases'); // 18
$photoPath = __DIR__ . '/../public/og/historic-change-29.png';

$caption = implode("\n", [
    "🚀 <b>29-BOSQICH: GURUH CHATINI FILTRLASH & BOT SPAMINI TO‘XTATISH</b>",
    "",
    "📅 <b>Sana:</b> 2026-11-22",
    "⏰ <b>Vaqt:</b> 11:49:25",
    "🌐 <b>Loyiha:</b> <a href=\"https://lingo.vexa.uz\">lingo.vexa.uz</a> (LingoUz)",
    "",
    "📝 <b>Batafsil ma’lumot:</b>",
    "• <b>Guruhdagi bot spami to‘xtatildi:</b> Guruhda odamlar yozishganda yoki boshqa botlar xabar yuborganda @vexa_lingo_bot o‘z-o‘zidan salomlashuv va menyu yuborib bezovta qilishi to‘liq bartaraf etildi (<code>src/telegram.php</code>).",
    "• <b>Xavfsizlik qoidasi qo‘yildi:</b>",
    "  <code>if (\$chatType !== 'private') return;</code>",
    "  <code>if (!empty(\$from['is_bot'])) return;</code>",
    "• <b>Aniq taqsimot:</b> Bot guruhda faqat rasmiy bildirishnomalarni o‘z topiciga chiqaradi va jim turadi, shaxsiy chatda esa foydalanuvchilar bilan to‘liq interaktiv muloqot qiladi.",
]);

$buttons = [
    'inline_keyboard' => [
        [
            ['text' => '🤖 Shaxsiy Chatda Sinash', 'url' => 'https://t.me/' . telegram_bot_username()],
            ['text' => '🌐 Platformaga O‘tish', 'url' => 'https://lingo.vexa.uz'],
        ]
    ]
];

$res = telegram_send_photo($chatId, $photoPath, $caption, $buttons, 'HTML', 'uz', $topicId);

echo "Natija: " . (!empty($res['ok']) ? "Muvaffaqiyatli yuborildi (message_id: " . ($res['result']['message_id'] ?? 'n/a') . ")" : "Xatolik: " . json_encode($res)) . "\n";
