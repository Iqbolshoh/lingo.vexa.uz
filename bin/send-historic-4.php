<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$chatId = telegram_dev_chat_id();
$topicId = telegram_dev_topic_id('releases'); // 18
$photoPath = __DIR__ . '/../public/og/historic-change-4.png';

$caption = implode("\n", [
    "🚀 <b>4-BOSQICH: RASMIY REGISTON FAVICONLARI & TIMSOL NOMI «LINGO» REBRENDINGI</b>",
    "",
    "📅 <b>Sana:</b> 2026-11-18",
    "⏰ <b>Vaqt:</b> 16:19:44",
    "🌐 <b>Loyiha:</b> <a href=\"https://lingo.vexa.uz\">lingo.vexa.uz</a> (LingoUz)",
    "",
    "📸 <i>Yuqoridagi rasm — 2026-11-18 da timsol rasman «Lingo» deb e’lon qilingan va brauzer faviconlari integratsiya qilingan paytdagi platformaning 100% asl tarixiy skrinshoti!</i>",
    "",
    "📝 <b>Batafsil ma’lumot:</b>",
    "• <b>Qahramon nomi «Lingo»ga o‘zgartirildi:</b> Eski «Zukko» nomi o‘rniga xalqaro va zamonaviy jaranglovchi «Lingo» nomi rasmiy brend timsoli sifatida belgilandi (<code>public/assets/mascot-lingo.svg</code>).",
    "• <b>Registon mavzuli Faviconlar:</b> Brauzer sarlavhasi (tab) uchun maxsus Registon koshinkorligi uslubidagi <code>favicon.ico</code> va <code>favicon.svg</code> yaratilib, layoutga ulandi.",
    "• <b>Aktivlar tozalanishi:</b> Keraksiz statik index.html va eski aktivlar tozalab, PHP front-controller bilan to‘liq muvofiqlashtirildi.",
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
