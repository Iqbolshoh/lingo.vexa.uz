<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$chatId = telegram_dev_chat_id();
$topicId = telegram_dev_topic_id('releases'); // 18
$photoPath = __DIR__ . '/../public/og/real-change-1.png';

$caption = implode("\n", [
    "📌 <b>1-O‘ZGARISH (Bugungi 21 ta o‘zgarishdan 1-si)</b>",
    "",
    "⏰ <b>Vaqt:</b> 2026-11-22 09:45:12",
    "🚀 <b>Yo‘nalish:</b> <b>Admin panel, Ta’lim modullari & Mobil PWA banneri</b>",
    "",
    "📝 <b>Batafsil izoh:</b>",
    "• <b>Admin boshqaruvi:</b> O‘quv modullarini to‘g‘ridan-to‘g‘ri import qilish va sozlash imkoniyati to‘liq ishga tushirildi (<code>src/bootstrap.php</code>, <code>src/learning.php</code>).",
    "• <b>Mobil PWA banneri:</b> Smartfonlarda saytga kirganda «Ilovani o‘rnatish» smart-banneri ekranni to‘sib qo‘ymasligi uchun ixcham suzuvchi holatga keltirildi.",
    "• <b>Ko‘p tillilik:</b> Ingliz tili (EN) lokalizatsiyasi yangilanib, onboarding matnlari to‘liq tarjima qilindi.",
    "• <b>Ma’lumotlar bazasi:</b> Yangi o‘quvchilar statistikasi va progressini saqlash jadvallari optimallashtirildi.",
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
