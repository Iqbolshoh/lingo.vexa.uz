<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$chatId = telegram_dev_chat_id();
$topicId = telegram_dev_topic_id('releases'); // 18
$photoPath = __DIR__ . '/../public/og/historic-change-21.png';

$caption = implode("\n", [
    "🚀 <b>21-BOSQICH: AUDIO ERTAKLAR (/stories) VA PUBLIC QR SERTIFIKAT TEKSHIRUVI</b>",
    "",
    "📅 <b>Sana:</b> 2026-11-22",
    "⏰ <b>Vaqt:</b> 10:34:13",
    "🌐 <b>Loyiha:</b> <a href=\"https://lingo.vexa.uz\">lingo.vexa.uz</a> (LingoUz)",
    "",
    "📝 <b>Batafsil ma’lumot:</b>",
    "• <b>Interaktiv Audio Ertaklar bo‘limi (/stories):</b> O‘zbek xalq ertaklari («Zumrad va Qimmat», «Susambil», «Oltin Tarvuz», «Afandi latifalari») tabiiy ovozli hikoya va matnli animatsiyalar bilan ishga tushirildi (<code>src/stories.php</code>).",
    "• <b>Ommaviy QR Sertifikat tekshiruvi:</b> Har bir diplomga unikal QR-kod va tekshirish havolasi (<code>/verify/{code}</code>) biriktirilib, sertifikat haqiqiyligini dunyoning istalgan nuqtasidan tekshirish imkoni berildi.",
    "• <b>Ertaklar audio boshqaruvi:</b> Pauza, ijro, takrorlash va ertak so‘ngida tushunishni mustahkamlovchi savollar qo‘shildi.",
]);

$buttons = [
    'inline_keyboard' => [
        [
            ['text' => '📚 Ertaklar bo‘limi', 'url' => 'https://lingo.vexa.uz/stories'],
        ]
    ]
];

$res = telegram_send_photo($chatId, $photoPath, $caption, $buttons, 'HTML', 'uz', $topicId);

echo "Natija: " . (!empty($res['ok']) ? "Muvaffaqiyatli yuborildi (message_id: " . ($res['result']['message_id'] ?? 'n/a') . ")" : "Xatolik: " . json_encode($res)) . "\n";
