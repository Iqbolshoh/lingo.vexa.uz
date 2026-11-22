<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$chatId = telegram_dev_chat_id();
$topicId = telegram_dev_topic_id('releases'); // 18
$photoPath = __DIR__ . '/../public/og/historic-change-11.png';

$caption = implode("\n", [
    "🚀 <b>11-BOSQICH: ANIMATSIYALI BOYO‘G‘LI TIMSOLI & DARAJANI ANIQLASH (ONBOARDING)</b>",
    "",
    "📅 <b>Sana:</b> 2026-11-21",
    "⏰ <b>Vaqt:</b> 11:11:34",
    "🌐 <b>Loyiha:</b> <a href=\"https://lingo.vexa.uz\">lingo.vexa.uz</a> (LingoUz)",
    "",
    "📝 <b>Batafsil ma’lumot:</b>",
    "• <b>Boyo‘g‘li (Owl) timsoli:</b> 10 xil hissiy holatga (quvonch, o‘ylanish, tabrik, adashish va h.k.) ega interaktiv animatsiyali boyqush timsoli yaratildi (<code>src/mascot.php</code>).",
    "• <b>Interaktiv Onboarding tizimi (/onboarding):</b> Ro‘yxatdan o‘tgandan so‘ng o‘quvchining maqsadi, qiziqishi, kunlik vaqti va til darajasini so‘rovnoma orqali aniqlash oqimi yo‘lga qo‘yildi.",
    "• <b>Darajani aniqlash testi (Placement test):</b> O‘quvchi o‘z darajasiga mos darslardan boshlashi uchun intellektual test tizimi ulandi.",
    "• <b>Admin chuqur tahliliy statistikasi:</b> O‘quvchilar faolligi, soatlik grafiklar, mashqlar aniqligi va savollarga javoblar voronkasi ishga tushirildi.",
]);

$buttons = [
    'inline_keyboard' => [
        [
            ['text' => '🌐 Saytda ko‘rish', 'url' => 'https://lingo.vexa.uz/onboarding'],
        ]
    ]
];

$res = telegram_send_photo($chatId, $photoPath, $caption, $buttons, 'HTML', 'uz', $topicId);

echo "Natija: " . (!empty($res['ok']) ? "Muvaffaqiyatli yuborildi (message_id: " . ($res['result']['message_id'] ?? 'n/a') . ")" : "Xatolik: " . json_encode($res)) . "\n";
