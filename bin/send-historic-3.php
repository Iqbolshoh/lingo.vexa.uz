<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$chatId = telegram_dev_chat_id();
$topicId = telegram_dev_topic_id('releases'); // 18
$photoPath = __DIR__ . '/../public/og/historic-change-3.png';

$caption = implode("\n", [
    "🚀 <b>3-BOSQICH: REGISTON & MAJOLIKA DIZAYN REVOLATSIYASI, SVG I把它ONLAR & PROFIL</b>",
    "",
    "📅 <b>Sana:</b> 2026-11-18",
    "⏰ <b>Vaqt:</b> 16:17:32",
    "🌐 <b>Loyiha:</b> <a href=\"https://lingo.vexa.uz\">lingo.vexa.uz</a> (LingoUz)",
    "",
    "📸 <i>Yuqoridagi rasm — 2026-11-18 da Registon feruza-ko‘k majolika uslubiga o‘tgan paytdagi platformaning 100% asl tarixiy skrinshoti!</i>",
    "",
    "📝 <b>Batafsil ma’lumot:</b>",
    "• <b>Registon & Majolika Dizayn Overhaul:</b> Barcha oddiy yashil ranglar o‘rniga Samarqand Registoni feruza-lojuvard (turquoise/cyan) ranglar gammasi va milliy geometrik naqshlar tatbiq etildi.",
    "• <b>Emojilardan toza SVG ikonkalariga:</b> Saytdagi barcha kulgich va emojilar o‘rniga professional SVG vektor ikonkalar tizimi (<code>src/icons.php</code>) yaratildi.",
    "• <b>Foydalanuvchi profili:</b> O‘quvchilar o‘z shaxsiy ma’lumotlarini, yoshini va til darajasini tahrirlash imkoniyati bo‘lgan <code>/profile</code> sahifasi ishga tushirildi.",
    "• <b>Haqiqiy POST autentifikatsiyasi:</b> Xavfsiz login/register oqimi va CSRF himoyasi to‘liq o‘rnatildi.",
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
