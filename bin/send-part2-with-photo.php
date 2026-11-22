<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$chatId = telegram_dev_chat_id();
$topicId = telegram_dev_topic_id('releases'); // 18
$photoPath = __DIR__ . '/../public/og/part2-changelog.png';

$captionLines = [
    "📅 <b>2-QISM: Bot Menyusi, Dasturchilar Guruhi, Topiclar & Yangi Header (10:47 – 11:54)</b>",
    "",
    "⏰ <b>10:47</b> · <code>4b4445e</code> — <b>PWA v8 Kesh Tozalash & Qahramon Nomi</b>",
    "• Service Worker v8 ishga tushirilib, telefonlardagi ilova piktogrammasi yangilandi, timsol nomi qat'iy «Lingo» deb belgilandi.",
    "",
    "⏰ <b>10:50</b> · <code>4c9a928</code> — <b>Gamifikatsiya Ovozlarini Kengaytirish</b>",
    "• Test natijalari (80%+ fanfara), streak olovi, diplom sahifasi, talaffuz to‘lqini va qahramon sakrashiga ovozlar ulandi.",
    "",
    "⏰ <b>11:11</b> · <code>cea759f</code> — <b>Bot Nomi, Rasmiy Avatar & Doimiy Menyu</b>",
    "• Bot nomi «LingoUz | O‘zbek Tili Akademiyasi» deb ro‘yxatga olindi, rasmiy avatar yuklandi va 8 ta doimiy emojili menyu tugmasi sozlandi.",
    "",
    "⏰ <b>11:39</b> · <code>a921bbe</code> — <b>Dasturchilar Guruhi Integratsiyasi</b>",
    "• Bot til tugmalari tozalandi (🇺🇿 O‘zbekcha, 🌐 Change language), Dasturchilar guruhi ulandi va reliz yuboruvchi bin/notify-deploy.php yaratildi.",
    "",
    "⏰ <b>11:41</b> · <code>6232903</code> — <b>Hodisalar Monitoringi (Alerts)</b>",
    "• Yangi o‘quvchilar, arizalar, 100% test natijalari va streak rekordlari uchun guruhga avto-bildirishnomalar ulandi.",
    "",
    "⏰ <b>11:43</b> · <code>93196d0</code> — <b>FastCGI Log Xatosi Bartaraf Etildi</b>",
    "• Bot xatoliklari storage/logs/telegram.log ga yo‘naltirilib, Nginx stderr xatoliklari to‘liq yo‘q qilindi (0 errors).",
    "",
    "⏰ <b>11:47</b> · <code>08f54b4</code> — <b>Guruhni Forum Topic'larga Ajratish</b>",
    "• Guruh 6 ta alohida Forum Topic ga ajratildi va har bir xabarnoma o‘z mavzusiga yo‘naltirildi.",
    "",
    "⏰ <b>11:48</b> · <code>3626a03</code> — <b>Topic Nomlarini Qisqa Inglizcha Qilish</b>",
    "• Mavzular xalqaro standartda qisqa qilindi: 🚀 Releases, 👥 Students, 📝 Leads, 🏆 Milestones, 📊 Reports, ⚠️ Alerts.",
    "",
    "⏰ <b>11:49</b> · <code>de87c61</code> — <b>Guruh Xabarlarini Filtrlash</b>",
    "• Guruhdagi umumiy yozishmalar filtrlanib, bot guruhda jim turishi va faqat shaxsiy chatda javob berishi sozlandi.",
    "",
    "⏰ <b>11:54</b> · <code>3aca3dc</code> — <b>Header Dizayni va Zamonaviy Responsivlik</b>",
    "• Menyudan takror «Bosh sahifa» olib tashlandi, 5 ta asosiy bo‘limli zamonaviy kapsula menyu va ixcham asboblar paneli yaratildi, Service Worker v9 ga yangilandi.",
];

$fullCaption = implode("\n", $captionLines);

$buttons = [
    'inline_keyboard' => [
        [
            ['text' => '🌐 lingo.vexa.uz', 'url' => 'https://lingo.vexa.uz'],
            ['text' => '🤖 @vexa_lingo_bot', 'url' => 'https://t.me/' . telegram_bot_username()],
        ],
        [
            ['text' => '📋 Barcha Commitlar', 'url' => 'https://github.com/Iqbolshoh/lingo.vexa.uz/commits/main'],
        ],
    ],
];

// Telegram caption character limit is 1024 characters.
// Check caption length:
if (mb_strlen($fullCaption, 'UTF-8') > 1000) {
    echo "Caption uzunligi: " . mb_strlen($fullCaption, 'UTF-8') . " belgi. Rasm bilan qisqa annotatsiya, matn alohida ketma-ket yuboriladi.\n";
    
    $shortCaption = "📅 <b>2-QISM: Bot Menyusi, Dasturchilar Guruhi, Topiclar & Yangi Header (10:47 – 11:54)</b>\n\nQuyidagi rasmda 10 ta muhim yangilanish va aniq vaqtlari keltirilgan. To‘liq matnli izoh pastda 👇";
    $resPhoto = telegram_send_photo($chatId, $photoPath, $shortCaption, null, 'HTML', 'uz', $topicId);
    echo "Rasm yuborish: " . (!empty($resPhoto['ok']) ? "OK" : "FAIL") . "\n";
    
    usleep(200000);
    $resMsg = telegram_send_message($chatId, $fullCaption, $buttons, 'HTML', 'uz', $topicId);
    echo "Matnli izoh yuborish: " . (!empty($resMsg['ok']) ? "OK" : "FAIL") . "\n";
} else {
    $resPhoto = telegram_send_photo($chatId, $photoPath, $fullCaption, $buttons, 'HTML', 'uz', $topicId);
    echo "Rasm va matn birga yuborildi: " . (!empty($resPhoto['ok']) ? "OK" : "FAIL") . "\n";
}
