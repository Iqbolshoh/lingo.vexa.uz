<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$chatId = telegram_dev_chat_id();
$topicId = telegram_dev_topic_id('releases'); // 18
$photoPath = __DIR__ . '/../public/og/historic-change-12.png';

$caption = implode("\n", [
    "🚀 <b>12-BOSQICH: O‘ZBEKCHA / INGLIZCHA IKKI TILLI INTERFEYS VA TIL ALMASHTIRISH (I18N)</b>",
    "",
    "📅 <b>Sana:</b> 2026-11-21",
    "⏰ <b>Vaqt:</b> 11:25:42",
    "🌐 <b>Loyiha:</b> <a href=\"https://lingo.vexa.uz\">lingo.vexa.uz</a> (LingoUz)",
    "",
    "📝 <b>Batafsil ma’lumot:</b>",
    "• <b>To‘liq ingliz tili (EN) qo‘llab-quvvatlanishi:</b> Platformaning barcha ta’lim sahifalari, darslar, mashqlar, so‘rovnomalar va sertifikatlar to‘liq ingliz tiliga o‘girildi (<code>src/lang/en.php</code>, <code>src/i18n.php</code>).",
    "• <b>Tezkor til almashtirish tugmasi:</b> Sarlavhada UZ / EN tugmasi o‘rnatildi, foydalanuvchi tanlagan til kuki va brauzer sozlamalarida eslab qolinadigan qilindi.",
    "• <b>Xalqaro talabalar uchun qulaylik:</b> Chet elda yashovchi vatandoshlar, sayyohlar va xorijliklar uchun o‘zbek tilini ingliz tilida o‘rganish imkoni yaratildi.",
]);

$buttons = [
    'inline_keyboard' => [
        [
            ['text' => '🌐 Saytda ko‘rish (EN)', 'url' => 'https://lingo.vexa.uz/?lang=en'],
        ]
    ]
];

$res = telegram_send_photo($chatId, $photoPath, $caption, $buttons, 'HTML', 'uz', $topicId);

echo "Natija: " . (!empty($res['ok']) ? "Muvaffaqiyatli yuborildi (message_id: " . ($res['result']['message_id'] ?? 'n/a') . ")" : "Xatolik: " . json_encode($res)) . "\n";
