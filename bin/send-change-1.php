<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$chatId = telegram_dev_chat_id();
$topicId = telegram_dev_topic_id('releases'); // 18
$photoPath = __DIR__ . '/../public/og/change-1.png';

$caption = implode("\n", [
    "📌 <b>1-O‘ZGARISH (Bugungi barcha 21 ta o‘zgarishdan 1-si)</b>",
    "",
    "⏰ <b>Vaqt:</b> 2026-11-22 09:45:12",
    "🔖 <b>Commit:</b> <code>f4db803</code>",
    "🚀 <b>Yo‘nalish:</b> <b>Admin panel, Ta’lim modullari & Mobil PWA banneri</b>",
    "",
    "📝 <b>Batafsil izoh:</b>",
    "• <b>Admin boshqaruvi:</b> O‘quv modullarini to‘g‘ridan-to‘g‘ri import qilish va sozlash imkoniyati to‘liq ishga tushirildi (<code>src/bootstrap.php</code>, <code>src/learning.php</code>).",
    "• <b>Mobil PWA banneri:</b> Smartfonlarda saytga kirganda «Ilovani o‘rnatish» smart-banneri ekranni to‘sib qo‘ymasligi uchun ixcham pastki suzuvchi holatga keltirildi.",
    "• <b>Ko‘p tillilik:</b> Ingliz tili (EN) lokalizatsiyasi yangilanib, xalqaro o‘quvchilar uchun onboarding qulaylashtirildi.",
    "• <b>Ma’lumotlar bazasi:</b> Yangi o‘quvchilar statistikasi va progressini saqlash ustunlari sinxronlashtirildi.",
    "",
    "🔗 <a href=\"https://github.com/Iqbolshoh/lingo.vexa.uz/commit/f4db803ccef4b639f8918c783828d096aaa0090c\">Commit tafsilotini ko‘rish</a> | <a href=\"https://lingo.vexa.uz\">lingo.vexa.uz</a>",
]);

$buttons = [
    'inline_keyboard' => [
        [
            ['text' => '🌐 Saytda ko‘rish', 'url' => 'https://lingo.vexa.uz'],
            ['text' => '🔍 Commit kodi', 'url' => 'https://github.com/Iqbolshoh/lingo.vexa.uz/commit/f4db803ccef4b639f8918c783828d096aaa0090c'],
        ]
    ]
];

$res = telegram_send_photo($chatId, $photoPath, $caption, $buttons, 'HTML', 'uz', $topicId);

echo "Natija: " . (!empty($res['ok']) ? "Muvaffaqiyatli yuborildi (message_id: " . ($res['result']['message_id'] ?? 'n/a') . ")" : "Xatolik: " . json_encode($res)) . "\n";
