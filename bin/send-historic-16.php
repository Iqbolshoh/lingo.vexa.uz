<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$chatId = telegram_dev_chat_id();
$topicId = telegram_dev_topic_id('releases'); // 18
$photoPath = __DIR__ . '/../public/og/historic-change-16.png';

$caption = implode("\n", [
    "🚀 <b>16-BOSQICH: AKTIV SAHIFA INDIKATORI (ACTIVE HIGHLIGHT) & SMART FLASHCARDS</b>",
    "",
    "📅 <b>Sana:</b> 2026-11-21",
    "⏰ <b>Vaqt:</b> 17:57:57",
    "🌐 <b>Loyiha:</b> <a href=\"https://lingo.vexa.uz\">lingo.vexa.uz</a> (LingoUz)",
    "",
    "📝 <b>Batafsil ma’lumot:</b>",
    "• <b>Faol sahifa chiroyli ajratib ko‘rsatilishi:</b> Foydalanuvchi qaysi bo‘limda turganini aniq bilishi uchun sarlavha va mobil menyuda turgan havola (masalan: «Flashcards») yashil-feruza fon va nozik nur bilan avtomatik belgilandi (<code>src/bootstrap.php</code>, <code>src/views/layout.php</code>).",
    "• <b>Smart Flashcards interfeysi:</b> Har bir so‘z kartochkasi uchun o‘zbekcha matn, inglizcha tarjimasi, talaffuz qilish tugmasi va o‘zlashtirish darajasi ko‘rsatkichlari mukammal ishga tushirildi.",
    "• <b>Navigatsiya qulayligi:</b> Darslar, kartochkalar va reyting o‘rtasida o‘tish yanada tushunarli va sezgir bo‘ldi.",
]);

$buttons = [
    'inline_keyboard' => [
        [
            ['text' => '🌐 Flashcards bo‘limi', 'url' => 'https://lingo.vexa.uz/flashcards'],
        ]
    ]
];

$res = telegram_send_photo($chatId, $photoPath, $caption, $buttons, 'HTML', 'uz', $topicId);

echo "Natija: " . (!empty($res['ok']) ? "Muvaffaqiyatli yuborildi (message_id: " . ($res['result']['message_id'] ?? 'n/a') . ")" : "Xatolik: " . json_encode($res)) . "\n";
