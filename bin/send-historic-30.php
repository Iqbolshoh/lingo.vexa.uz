<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$chatId = telegram_dev_chat_id();
$topicId = telegram_dev_topic_id('releases'); // 18
$photoPath = __DIR__ . '/../public/og/historic-change-30.png';

$caption = implode("\n", [
    "🚀 <b>30-BOSQICH: HEADER DIZAYNI VA ZAMONAVIY RESPONSIVLIK (PWA V9)</b>",
    "",
    "📅 <b>Sana:</b> 2026-11-22",
    "⏰ <b>Vaqt:</b> 11:54:32",
    "🌐 <b>Loyiha:</b> <a href=\"https://lingo.vexa.uz\">lingo.vexa.uz</a> (LingoUz)",
    "",
    "📝 <b>Batafsil ma’lumot:</b>",
    "• <b>Kompyuterda 1 qatorda tiqilish muammosi hal qilindi:</b> 11 ta element bir joyga to‘planib qolmasligi uchun menyu mantiqiy bloklarga ajratildi:",
    "  1. Asosiy kapsula navigatsiya: <i>Darslar, Flashcards, Ertaklar, Reyting, Qo‘llanma</i>",
    "  2. Asboblar guruhi: <i>Ovoz (🔊) + Til (UZ/EN) + Ilova o‘rnatish</i>",
    "  3. Avtorizatsiya guruhi: <i>Kirish + Ro‘yxatdan o‘tish (yashil gradient tugma)</i>",
    "• <b>Keraksiz takroriy havola olib tashlandi:</b> Logo yonida turgan ortiqcha «Bosh sahifa» matnli havolasi olib tashlanib, navigatsiya toza va yengil qilindi.",
    "• <b>PWA Service Worker v9:</b> Barcha foydalanuvchilar brauzerida yangi dizayn zudlik bilan aks etishi uchun kesh versiyasi <code>lingouz-pwa-v9</code> ga yangilandi.",
]);

$buttons = [
    'inline_keyboard' => [
        [
            ['text' => '🌐 Saytda Ko‘rish', 'url' => 'https://lingo.vexa.uz'],
        ]
    ]
];

$res = telegram_send_photo($chatId, $photoPath, $caption, $buttons, 'HTML', 'uz', $topicId);

echo "Natija: " . (!empty($res['ok']) ? "Muvaffaqiyatli yuborildi (message_id: " . ($res['result']['message_id'] ?? 'n/a') . ")" : "Xatolik: " . json_encode($res)) . "\n";
