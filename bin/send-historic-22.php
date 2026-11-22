<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$chatId = telegram_dev_chat_id();
$topicId = telegram_dev_topic_id('releases'); // 18
$photoPath = __DIR__ . '/../public/og/historic-change-22.png';

$caption = implode("\n", [
    "🚀 <b>22-BOSQICH: DUOLINGO & ALGOR OVOZ EFFEKTLARI (SOUNDFX) VA OVOZNI YOQISH/O‘CHIRISH TUGMASI 🔊</b>",
    "",
    "📅 <b>Sana:</b> 2026-11-22",
    "⏰ <b>Vaqt:</b> 10:41:39",
    "🌐 <b>Loyiha:</b> <a href=\"https://lingo.vexa.uz\">lingo.vexa.uz</a> (LingoUz)",
    "",
    "📝 <b>Batafsil ma’lumot:</b>",
    "• <b>Web Audio API sintezlangan tovushlari:</b> Duolingo va Algor.uz uslubidagi 7 xil quvnoq tovush effekti (to‘g‘ri javob — ding, xato — buzz, fanfara, tangalar — coin, streak olovi, tugma bosilishi, kartani aylantirish) integratsiya qilindi (<code>public/assets/app.js</code>).",
    "• <b>Sarlavhada Ovoz sozlamasi (🔊 / 🔇):</b> Foydalanuvchi istagan paytda tovushlarni 1 ta bosishda o‘chirib yoki yoqib qo‘yishi mumkin bo‘ldi (sozlama xotirada saqlanadi).",
    "• <b>Interaktiv animatsiyalar:</b> Savol to‘g‘ri topilganda yashil zarralar nurlanishi (confetti / ripple) va qahramonning sakrash harakatlari ulandi.",
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
