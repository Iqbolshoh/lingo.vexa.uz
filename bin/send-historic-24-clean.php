<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$chatId = telegram_dev_chat_id();
$topicId = telegram_dev_topic_id('releases'); // 18
$photoPath = __DIR__ . '/../public/og/historic-change-24-clean.png';

$caption = implode("\n", [
    "🚀 <b>24-BOSQICH: BOT NOMI, RASMIY AVATAR VA 8 TA DOIMIY MENYU TUGMASI (TOZA REJIM)</b>",
    "",
    "📅 <b>Sana:</b> 2026-11-22",
    "⏰ <b>Vaqt:</b> 11:11:42",
    "🌐 <b>Loyiha:</b> <a href=\"https://lingo.vexa.uz\">lingo.vexa.uz</a> (LingoUz)",
    "",
    "📝 <b>Batafsil ma’lumot:</b>",
    "• <b>Rasmiy bot identifikatsiyasi:</b> Bot nomi rasman «LingoUz | O‘zbek Tili Akademiyasi» deb ro‘yxatga olindi, rasmiy Lingo avatari yuklandi (<code>src/telegram.php</code>).",
    "• <b>8 ta Doimiy Menyu Tugmasi (Reply Keyboard):</b> Foydalanuvchi buyruqlarni yozib o‘tirmasligi uchun klaviatura ostida 8 ta emojili qulay tugma o‘rnatildi:",
    "  📖 Bugungi so‘z  |  🎯 Mini-test  |  🔥 Streak  |  📊 Profil",
    "  🏆 Reyting  |  📚 Ertaklar  |  🌐 Til  |  🚀 Saytga kirish",
    "• <b>Buyruqlarni aqlli tanish:</b> Foydalanuvchi tugmani bosganda yoki o‘xshash so‘z yozganda bot xatosiz tushunadigan moslashtiruvchi filtr ulandi.",
]);

$buttons = [
    'inline_keyboard' => [
        [
            ['text' => '🤖 Rasmiy Botni Sinash', 'url' => 'https://t.me/' . telegram_bot_username()],
            ['text' => '🌐 Platformaga O‘tish', 'url' => 'https://lingo.vexa.uz'],
        ]
    ]
];

$res = telegram_send_photo($chatId, $photoPath, $caption, $buttons, 'HTML', 'uz', $topicId);

echo "Natija: " . (!empty($res['ok']) ? "Muvaffaqiyatli yuborildi (message_id: " . ($res['result']['message_id'] ?? 'n/a') . ")" : "Xatolik: " . json_encode($res)) . "\n";
