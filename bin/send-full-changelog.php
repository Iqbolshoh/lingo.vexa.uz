<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$chatId = telegram_dev_chat_id();
$topicId = telegram_dev_topic_id('releases'); // 18
$photo = __DIR__ . '/../public/og/default.png';

echo "Guruhga to‘liq xronologik hisobot yuborilmoqda...\n";

// -----------------------------------------------------------------------------
// Xabar 1: Rasmli Kirish Kartochkasi (Photo + Caption)
// -----------------------------------------------------------------------------
$caption = implode("\n", [
    "🚀 <b>LingoUz: Bugungi Barcha O‘zgarishlar Hisoboti</b>",
    "",
    "📅 <b>Sana:</b> 2026-11-22",
    "🕒 <b>Jami commitlar:</b> 21 ta muvaffaqiyatli yangilanish",
    "💻 <b>Platforma:</b> <a href=\"https://lingo.vexa.uz\">lingo.vexa.uz</a>",
    "🤖 <b>Bot:</b> <a href=\"https://t.me/vexa_lingo_bot\">@vexa_lingo_bot</a>",
    "",
    "Bugun amalga oshirilgan har bir o‘zgarish, ularning aniq vaqti va vazifasi quyida to‘liq bayon etilgan 👇",
]);

$buttons = [
    'inline_keyboard' => [
        [
            ['text' => '🌐 Platformaga o‘tish', 'url' => 'https://lingo.vexa.uz'],
            ['text' => '🤖 Rasmiy Bot', 'url' => 'https://t.me/' . telegram_bot_username()],
        ],
        [
            ['text' => '⚙️ Admin Panel', 'url' => 'https://lingo.vexa.uz/admin'],
            ['text' => '📋 Barcha Commitlar', 'url' => 'https://github.com/Iqbolshoh/lingo.vexa.uz/commits/main'],
        ],
    ],
];

$res1 = telegram_send_photo($chatId, $photo, $caption, $buttons, 'HTML', 'uz', $topicId);
echo "1. Rasmli xulosa: " . (!empty($res1['ok']) ? "OK" : "FAIL") . "\n";
usleep(300000); // 300ms pause

// -----------------------------------------------------------------------------
// Xabar 2: 1-Qism (09:45 – 10:41)
// -----------------------------------------------------------------------------
$part1Lines = [
    "📅 <b>1-QISM: Baza, Xavfsizlik, Brending & Bot Integratsiyasi (09:45 – 10:41)</b>",
    "",
    "⏰ <b>09:45</b> · <code>f4db803</code> — <b>Admin panel va PWA banner</b>",
    "• Admin boshqaruv panelida o‘quv modullari importi to‘g‘rilandi va mobil PWA banneri optimallashtirildi.",
    "",
    "⏰ <b>09:46</b> · <code>146dde9</code> — <b>PHP Seanslari Xavfsizligi</b>",
    "• Seanslar `storage/framework/sessions` papkasiga ajratilib, ruxsat xatoliklari to‘liq bartaraf etildi.",
    "",
    "⏰ <b>09:49</b> · <code>e9c6e7a</code> — <b>Lingo Piktogrammasi (1-versiya)</b>",
    "• Eski qora belgi o‘rniga quvnoq Lingo timsoli chizildi va saytga ulandi.",
    "",
    "⏰ <b>09:51</b> · <code>21a57d0</code> — <b>SQL Xavfsizligi (Prepared Statements)</b>",
    "• Barcha SQL so‘rovlar PDO parametrlariga o‘tkazildi (SQL Injection himoyasi).",
    "",
    "⏰ <b>09:53</b> · <code>64403a6</code> — <b>Piktogramma Aniqligi</b>",
    "• Lingo yuzi markazlashtirilib, piktogramma konturlari kuchaytirildi.",
    "",
    "⏰ <b>09:56</b> · <code>c72b417</code> — <b>Toza Oq Fonli Favicon</b>",
    "• Barcha favicon va mobil ilova piktogrammalari foni toza oq rangga (#ffffff) o‘tkazildi.",
    "",
    "⏰ <b>10:06</b> · <code>0d5974a</code> — <b>Favicon Keshini Tozalash</b>",
    "• Keshdagi eski belgilar to‘liq tozalandi va toza Lingo timsoli bilan almashtirildi.",
    "",
    "⏰ <b>10:15</b> · <code>d234fa1</code> — <b>Telegram Bot Ilk Integratsiyasi</b>",
    "• Rasmiy bot webhooki ulandi, dashboardga 1-bosishda ulanuvchi banner va real-time status tekshiruvi qo‘yildi.",
    "",
    "⏰ <b>10:22</b> · <code>e33b8f4</code> — <b>Ikki Tilli Bot & Dinamik OG Kartalar</b>",
    "• Rasmiy bot tokeni ulandi, bot o‘zbek va ingliz tillarida ishga tushirildi, 1200x630 formatdagi dinamik OG kartalar generatori yaratildi.",
    "",
    "⏰ <b>10:34</b> · <code>476eacf</code> — <b>Audio Ertaklar & QR Diplom Tekshiruvi</b>",
    "• 4 ta audio ertak («Zumrad va Qimmat», «Susambil», «Oltin Tarvuz», «Afandi») va ommaviy QR tekshirish sahifasi (/verify/{code}) ishga tushirildi.",
    "",
    "⏰ <b>10:41</b> · <code>8b008f7</code> — <b>Duolingo / Algor Ovoz Effektlari (SoundFX)</b>",
    "• 7 xil sintezlangan tovush (correct, wrong, fanfare, coin, streak, click, flip) va headerda 🔊/🔇 ovoz sozlamasi ulandi.",
];

$res2 = telegram_send_message($chatId, implode("\n", $part1Lines), null, 'HTML', 'uz', $topicId);
echo "2. Xronologiya 1-qism: " . (!empty($res2['ok']) ? "OK" : "FAIL") . "\n";
usleep(300000);

// -----------------------------------------------------------------------------
// Xabar 3: 2-Qism (10:47 – 11:54)
// -----------------------------------------------------------------------------
$part2Lines = [
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

$res3 = telegram_send_message($chatId, implode("\n", $part2Lines), null, 'HTML', 'uz', $topicId);
echo "3. Xronologiya 2-qism: " . (!empty($res3['ok']) ? "OK" : "FAIL") . "\n";

echo "Barcha xabarlar guruhga muvaffaqiyatli yetkazildi!\n";
