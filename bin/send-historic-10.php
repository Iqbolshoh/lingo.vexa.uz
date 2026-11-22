<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$chatId = telegram_dev_chat_id();
$topicId = telegram_dev_topic_id('releases'); // 18
$photoPath = __DIR__ . '/../public/og/historic-change-10.png';

$caption = implode("\n", [
    "🚀 <b>10-BOSQICH: LINGOUZ V3 — KO‘P KURSLIK KATALOG, MADANIYAT & SAYOHATCHILAR KURSI, ADMIN CRUD</b>",
    "",
    "📅 <b>Sana:</b> 2026-11-21",
    "⏰ <b>Vaqt:</b> 10:59:57",
    "🌐 <b>Loyiha:</b> <a href=\"https://lingo.vexa.uz\">lingo.vexa.uz</a> (LingoUz)",
    "",
    "📝 <b>Batafsil ma’lumot:</b>",
    "• <b>Ko‘p kurslik ta’lim katalogi (/courses):</b> Platforma yagona kursdan ko‘p kurslik tizimga o‘tdi («Bolalar uchun o‘zbek tili», «Sayohatchilar uchun o‘zbek tili», «O‘zbek madaniyati va urf-odatlari»).",
    "• <b>Englify uslubidagi yangi mashqlar:</b> Savollar turi kengaytirildi — tinglab topish (audio), so‘z yig‘ish (build), bo‘shliqni to‘ldirish (fill) va testlar.",
    "• <b>To‘liq Admin CRUD boshqaruvi:</b> Kurslar, darslar, lug‘atlar va savollarni to‘g‘ridan-to‘g‘ri boshqarish, o‘quvchilar baholar jurnali (gradebook) va dunyo vaqt zonalari qo‘shildi.",
    "• <b>Gamifikatsiya bazasi:</b> XP, tangalar (coins) va kunlik faollik (streak) mexanizmlari ishga tushirildi.",
]);

$buttons = [
    'inline_keyboard' => [
        [
            ['text' => '🌐 Saytda ko‘rish', 'url' => 'https://lingo.vexa.uz/courses'],
        ]
    ]
];

$res = telegram_send_photo($chatId, $photoPath, $caption, $buttons, 'HTML', 'uz', $topicId);

echo "Natija: " . (!empty($res['ok']) ? "Muvaffaqiyatli yuborildi (message_id: " . ($res['result']['message_id'] ?? 'n/a') . ")" : "Xatolik: " . json_encode($res)) . "\n";
