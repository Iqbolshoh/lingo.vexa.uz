<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$chatId = telegram_dev_chat_id();
$topicId = telegram_dev_topic_id('releases'); // 18
$photoPath = __DIR__ . '/../public/og/historic-change-2.png';

$caption = implode("\n", [
    "🚀 <b>2-BOSQICH: MILLIY BRENDING, TIMSOL ZUKKO & 3D FLASHCARDS</b>",
    "",
    "📅 <b>Sana:</b> 2026-11-18",
    "⏰ <b>Vaqt:</b> 16:10:01",
    "🌐 <b>Loyiha:</b> <a href=\"https://lingo.vexa.uz\">lingo.vexa.uz</a> (LingoUz)",
    "",
    "📸 <i>Yuqoridagi rasm — 2026-11-18 da do‘ppili Zukko qahramoni va 3D kartalar qo‘shilgan paytdagi platformaning 100% asl tarixiy skrinshoti!</i>",
    "",
    "📝 <b>Batafsil ma’lumot:</b>",
    "• <b>Milliy brending & Zukko qahramoni:</b> Saytga o‘zbekona do‘ppi kiygan «Zukko» timsoli (SVG) integratsiya qilindi.",
    "• <b>3D Flip Flashcards:</b> So‘zlarni yodlash uchun aylanuvchi 3D fleshkartalar moduli ishga tushirildi.",
    "• <b>Sertifikat / Diplom generatori:</b> Kursni tugatgan o‘quvchilar uchun rasmiy diplom shakllantirish sahifasi yaratildi.",
    "• <b>Web Audio tovushlari:</b> To‘g‘ri javoblar uchun chimes va fanfara tovush effektlari ulandi.",
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
