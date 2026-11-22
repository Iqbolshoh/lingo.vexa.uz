<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$chatId = telegram_dev_chat_id();
$topicId = telegram_dev_topic_id('releases'); // 18
$photoPath = __DIR__ . '/../public/og/historic-change-14.png';

$caption = implode("\n", [
    "🚀 <b>14-BOSQICH: BARCHA 17 TA DARSLAR UCHUN TA’LIMIY VIDEO VA SUBTITRLAR</b>",
    "",
    "📅 <b>Sana:</b> 2026-11-21",
    "⏰ <b>Vaqt:</b> 16:59:19",
    "🌐 <b>Loyiha:</b> <a href=\"https://lingo.vexa.uz\">lingo.vexa.uz</a> (LingoUz)",
    "",
    "📝 <b>Batafsil ma’lumot:</b>",
    "• <b>Ta’limiy video darsliklar:</b> Barcha 17 ta darsga professional multimedia video pleyer, animatsiyali tushuntirishlar va vizual dars lavhalari integratsiya qilindi (<code>src/views/lesson.php</code>).",
    "• <b>Ikki tilli interaktiv subtitrlar:</b> O‘quvchi videoni ko‘rayotganda o‘zbekcha va inglizcha subtitrlarni sinxron kuzatib, so‘zlarni darhol bosib ma’nosini o‘rgana oladi.",
    "• <b>Video pleyer boshqaruvi:</b> Tezlikni sozlash (0.75x, 1x), subtitrlarni yoqish/o‘chirish va to‘liq ekran rejimi ulandi.",
]);

$buttons = [
    'inline_keyboard' => [
        [
            ['text' => '🌐 Darslar katalogi', 'url' => 'https://lingo.vexa.uz/courses'],
        ]
    ]
];

$res = telegram_send_photo($chatId, $photoPath, $caption, $buttons, 'HTML', 'uz', $topicId);

echo "Natija: " . (!empty($res['ok']) ? "Muvaffaqiyatli yuborildi (message_id: " . ($res['result']['message_id'] ?? 'n/a') . ")" : "Xatolik: " . json_encode($res)) . "\n";
