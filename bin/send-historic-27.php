<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$chatId = telegram_dev_chat_id();
$topicId = telegram_dev_topic_id('releases'); // 18
$photoPath = __DIR__ . '/../public/og/historic-change-27.png';

$caption = implode("\n", [
    "🚀 <b>27-BOSQICH: FASTCGI VA NGX_STDERR LOG XATOSI BARTARAF ETILDI</b>",
    "",
    "📅 <b>Sana:</b> 2026-11-22",
    "⏰ <b>Vaqt:</b> 11:43:10",
    "🌐 <b>Loyiha:</b> <a href=\"https://lingo.vexa.uz\">lingo.vexa.uz</a> (LingoUz)",
    "",
    "📝 <b>Batafsil ma’lumot:</b>",
    "• <b>FastCGI oqimi tozalandi:</b> PHP’dagi <code>error_log()</code> chaqiriqlari sabab Nginx <code>student_lingo_error.log</code> ga tushayotgan soxta PHP xabarlari to‘liq yo‘q qilindi (<code>src/telegram.php</code>).",
    "• <b>Alohida xavfsiz log fayli:</b> Botning barcha diagnostika yozuvlari faqat <code>storage/logs/telegram.log</code> ga yo‘naltirildi.",
    "• <b>Nginx buferlari kengaytirildi:</b> <code>fastcgi_buffers 16 128k</code> qilinib, upstream sarlavhalari tiqilib qolishining oldi olindi (Nginx error log: 0 errors).",
]);

$buttons = [
    'inline_keyboard' => [
        [
            ['text' => '🌐 Platformaga O‘tish', 'url' => 'https://lingo.vexa.uz'],
        ]
    ]
];

$res = telegram_send_photo($chatId, $photoPath, $caption, $buttons, 'HTML', 'uz', $topicId);

echo "Natija: " . (!empty($res['ok']) ? "Muvaffaqiyatli yuborildi (message_id: " . ($res['result']['message_id'] ?? 'n/a') . ")" : "Xatolik: " . json_encode($res)) . "\n";
