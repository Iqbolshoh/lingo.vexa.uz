<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$chatId = telegram_dev_chat_id();
$topicId = telegram_dev_topic_id('releases'); // 18
$photoPath = __DIR__ . '/../public/og/historic-change-9.png';

$caption = implode("\n", [
    "🚀 <b>9-BOSQICH: LAPIS HERO, GIRIH NAQSHLARI VA SHISHA EFFEKTI (GLASS HEADER)</b>",
    "",
    "📅 <b>Sana:</b> 2026-11-21",
    "⏰ <b>Vaqt:</b> 10:15:13",
    "🌐 <b>Loyiha:</b> <a href=\"https://lingo.vexa.uz\">lingo.vexa.uz</a> (LingoUz)",
    "",
    "📝 <b>Batafsil ma’lumot:</b>",
    "• <b>Lapis Lazuli (Lojuvard) Hero foni:</b> Bosh sahifa qahramon qismi qadimiy sharqona lojuvard to‘q ko‘k foni va nozik «Girih» geometrik naqshlari bilan boyitildi.",
    "• <b>Shisha effekti (Glassmorphic Header):</b> Sarlavha paneli zamonaviy yarim-shaffof shisha (backdrop blur) uslubiga o‘tkazildi.",
    "• <b>Aktivlar avtomatik keshi (filemtime):</b> CSS va JS fayllariga versiya sifatida fayl o‘zgarish vaqti ulandi (foydalanuvchilarda kesh qotib qolmasligi uchun).",
    "• <b>Qahramon harakati:</b> Lingo timsoli sahnadagi o‘quv maydonida mustahkam o‘rnashadigan qilib sozlandi.",
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
