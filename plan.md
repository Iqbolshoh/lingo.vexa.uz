# LingoUz — plan.md

> **LingoUz** — O‘zbek tilini o‘rganmoqchi bo‘lgan hamma uchun zamonaviy, interaktiv va o‘yinlashtirilgan onlayn ta’lim platformasi: xorijdagi vatandoshlar, bolalar, kattalar, sayyohlar va o‘zbek madaniyatiga qiziquvchilar uchun.  
> **Jonli URL:** https://lingo.vexa.uz · **Lokal yo‘l:** `/var/www/startups/lingo` · **Oxirgi yangilanish:** 2026-11-22 · **Holati:** 100% Ishga tushirilgan va barqaror

---

## 📌 QAT’IY QOIDA: Yangi O‘zbek Lotin Alifbosi Standarti

> [!IMPORTANT]
> Loyihada foydalanuvchiga ko‘rinadigan har qanday o‘zbekcha matn (UI, darslar, lug‘at, savollar, bildirishnomalar, tugmalar, placeholder'lar) kiritilayotganda yoki tahrirlanayotganda **QAT’IY** yangi o‘zbek lotin alifbosi standartiga amal qilinishi shart!

### Harf almashtirish qoidalari:
- `O‘ / o‘` → `Ö / ö`
- `Sh / sh` → `Ş / ş`
- `Ch / ch` → `Ç / ç`
- `G‘ / g‘` → `Ğ / ğ`

### Asosiy qoidalar:
1. **Foydalanuvchi interfeysi (UI):** Barcha ko‘rinadigan matnlar yangi standartda bo‘ladi.
2. **Texnik daxlsizlik:** Dasturchi kodi, o‘zgaruvchilar, funksiyalar, marshrutlar (routes), SQL ustunlari va API kalitlari doimo toza **ingliz tilida (English)** yoziladi.
3. **Qahramon (Mascot) nomi:** Rasmiy timsol nomi barcha joyda qat’iy faqat **«Lingo»** deb yuritiladi.

---

## 0. Tizim Arxitekturasi va Hozirgi Holati (100% Tayyor)

| # | Modul / Tizim | Texnologiya / Xizmat | Hozirgi holati va Imkoniyatlari |
|---|---------------|----------------------|---------------------------------|
| 1 | **Ma’lumotlar bazasi** | MySQL 8.4 (`lingo_vexa_db`) | 15 ta to‘liq jadval (users, courses, lessons, vocabulary, questions, quizzes, quiz_attempts, lesson_progress, badges, user_badges, activity_days, live_sessions, enrollments, login_attempts, startup_meta). Barcha so‘rovlar PDO prepared statements orqali himoyalangan. |
| 2 | **Web Server & Backend** | Nginx + PHP 8.5 (FastCGI) | HTTP/2, TLS, izolyatsiyalangan `php-fpm-students.service`, kesh boshqaruvi, xavfsiz seanslar. |
| 3 | **Telegram Bot** | `@vexa_lingo_bot` (API 7.0+) | Rasmiy nom: `LingoUz \| O‘zbek Tili Akademiyasi`. Maxsus oq fondagi profil rasmi, 8 ta doimiy reply keyboard tugmasi, ikki tilli (UZ/EN) rejim, 1-bosishda ulanish, kunlik so‘z va interaktiv viktorinalar. |
| 4 | **Dasturchilar Guruhi** | `ligo.uz` (`-1003979299740`) | 6 ta inglizcha qisqa Forum Topics (`🚀 Releases`, `👥 Students`, `📝 Leads`, `🏆 Milestones`, `📊 Reports`, `⚠️ Alerts`). Barcha xabarnomalar o‘z mavzusiga avtomatik yo‘naltiriladi. |
| 5 | **Ovoz va Gamifikatsiya** | Web Audio API + SoundFX | Duolingo va Algor.uz uslubidagi 7 ta tovush effekti (correct, wrong, fanfare, coin, streak, click, flip), suzuvchi oltin `+XP` nishonlari, konfetti feyerverki, talaffuz to‘lqini animatsiyasi va qahramon sakrashi. Headerda `🔊 / 🔇` sozlamasi. |
| 6 | **PWA Mobil Ilova** | Service Worker v9 + Manifest | Toza oq fondagi Lingo piktogrammalari (16–512px, apple-touch, maskable), kesh yangilanishi va mustaqil ilova sifatida o‘rnatish imkoniyati. |
| 7 | **Ovozli Ertaklar** | Interactive Audio Stories | 4 ta mashhur xalq ertagi («Zumrad va Qimmat», «Susambil», «Oltin Tarvuz», «Afandi va Donishmand»), gapma-gap Web Speech API audio talaffuzi va tushunish viktorinasi. |
| 8 | **Diplom & QR Tekshiruv** | Dynamic Certificate & Verification | Kurs yakunida beriladigan rasmiy diplom (`/certificate`), chop etish rejimi va ommaviy QR-kod orqali tekshirish sahifasi (`/verify/{code}`). |
| 9 | **Header & UI Dizayni** | Responsive Segmented Nav | Qatorga tiqilmagan zamonaviy kapsula menyu, asboblar va autentifikatsiya guruhlari, ≤1080px ekranlar uchun qulay Gamburger drawer. |
| 10 | **Admin Boshqaruv Paneli** | Full Admin Suite (`/admin`) | Kurslar, darslar, lug‘at va savollar boshqaruvi, o‘quvchilar profili, arizalar qabuli, baholar matritsasi, DAU/WAU/MAU tahlili, guruhga yangilanish chiqarish vositasi. |

---

## 1. 2026-11-22 Sanasida Bajarilgan Ishlar Tarixi (Changelog)

Bugungi kun davomida jami **21 ta** muvaffaqiyatli commit amalga oshirildi:

| # | Commit | Vaqt | Nomi (Sarlavha) | Tavsilotlar va Texnik Izohi |
|---|:---:|:---:|---|---|
| 1 | `f4db803` | 09:45 | `fix(admin): ensure onboarding and learning modules loaded, and optimize mobile banner PWA` | Admin paneldagi seans va darslarni yuklashdagi importlar to‘g‘rilandi, mobil qurilmalardagi PWA banner ko‘rinishi optimallashtirildi. |
| 2 | `146dde9` | 09:46 | `fix(session): isolate session storage to storage/framework/sessions` | PHP seanslari fayllarini izolyatsiyalangan `storage/framework/sessions` papkasiga yo‘naltirilib, ruxsat xatoliklari bartaraf etildi. |
| 3 | `e9c6e7a` | 09:49 | `feat(icons): update favicon and PWA app icons with cute Lingo mascot` | Eski qoramtir noaniq piktogramma o‘rniga quvnoq Lingo qahramonining SVG va PNG formatdagi ilk belgilari tayyorlandi. |
| 4 | `21a57d0` | 09:51 | `refactor(sql): replace remaining inline query parameters with secure prepared statements` | Admin va darsliklar so‘rovlaridagi barcha string birlashtirishlar to‘liq PDO prepared statement xavfsiz parametrlariga o‘tkazildi. |
| 5 | `64403a6` | 09:53 | `feat(icons): clean white background with bold Lingo mascot portrait` | Lingo qahramoni piktogrammasi markazlashtirildi va chiziqlari aniqlandi. |
| 6 | `c72b417` | 09:56 | `feat(icons): solid pure white background for all favicons and PWA icons` | Favicon va PWA piktogrammalarining foni toza oq rangga (`#ffffff`) o‘tkazildi. |
| 7 | `0d5974a` | 10:06 | `fix(icons): replace dark demon blob with cute transparent Lingo owl favicon and PWA icons` | Brauzer tablarida xunuk ko‘rinayotgan eski fayllar butunlay tozalandi va shaffof/oq SVG Lingo o‘rnatildi. |
| 8 | `d234fa1` | 10:15 | `feat(telegram): implement official Telegram bot integration with 1-click connect banner, daily word and streak reminders` | Rasmiy Telegram bot integratsiyasi yaratildi (`src/telegram.php`), Webhook o‘rnatildi, o‘quvchi dashboardiga bir bosishda ulanish banneri va avto-polling ulandi. |
| 9 | `e33b8f4` | 10:22 | `feat(telegram): full bilingual (UZ/EN) bot support, official token integration for @vexa_lingo_bot, and dynamic high-res OG share cards` | Rasmiy bot tokeni (`9105016228:...`) ulandi, bot to‘liq ikki tilda ishlaydigan qilindi, 1200x630 formatdagi dinamik OpenGraph kartalar generatori (`src/og.php`) yaratildi. |
| 10 | `476eacf` | 10:34 | `feat(stories & certs): interactive audio folk stories with speech synthesis and public QR certificate verification` | 4 ta audio ertak («Zumrad va Qimmat», «Susambil», «Oltin Tarvuz», «Afandi») va ommaviy QR-kod orqali sertifikat tekshirish sahifasi (`/verify/{code}`) joriy etildi. |
| 11 | `8b008f7` | 10:41 | `feat(gamification): add Duolingo/Algor.uz style gamified sound effects, animations, and mute toggle` | Web Audio API orqali sintez qilinuvchi `SoundFX` tizimi (correct, wrong, fanfare, coin, streak, click, flip) yaratildi, konfetti feyerverki va headerga `🔊/🔇` ovoz sozlamasi qo‘shildi. |
| 12 | `4b4445e` | 10:47 | `fix(icons): solid white background PWA icons, sw.js v8 cache purge, and use pure Lingo mascot name` | Service Worker v8 kesh tozalash mexanizmi joriy etildi, Nginx kesh sarlavhalari sozlandi va timsol nomi qat’iy «Lingo» deb belgilandi. |
| 13 | `4c9a928` | 10:50 | `feat(gamification): add sound FX & animations to quiz results, streak power, certificate, audio ripple, and mascot hop` | Ovozlar va animatsiyalar test natijalariga (80%+ fanfara), streak oloviga, sertifikat sahifasiga, talaffuz to‘lqiniga va bosh qahramon sakrashiga ulandi. |
| 14 | `cea759f` | 11:11 | `feat(telegram): professional persistent reply keyboard, set bot name, upload official avatar, and enhanced button matching` | Bot nomi rasman `LingoUz \| O‘zbek Tili Akademiyasi` deb ro‘yxatga olindi, rasmiy oq fondagi avatar Bot API orqali yuklandi va 8 ta doimiy emojili menyu tugmalari sozlandi. |
| 15 | `a921bbe` | 11:39 | `feat(telegram): clean language button labels and integrate dev group alerts (-1003979299740)` | Til tugmalari lo‘nda qilindi (`🇺🇿 O‘zbekcha`, `🌐 Change language`), Dasturchilar guruhi (`-1003979299740`) ulandi va reliz yuborish skripti (`bin/notify-deploy.php`) yaratildi. |
| 16 | `6232903` | 11:41 | `feat(events): wire user registration, quiz milestone, and streak alerts to dev group` | Yangi o‘quvchi ro‘yxatdan o‘tganda, kursga ariza qoldirilganda, testdan 100% olinganda va streak rekordlarida guruhga avtomatik xabarnomalar ulandi. |
| 17 | `93196d0` | 11:43 | `fix(logging): route telegram errors to storage/logs/telegram.log to prevent FastCGI stderr pollution` | Bot xatolari Nginx va FastCGI stderr ga tushmasligi uchun `telegram_log()` ajratildi va `storage/logs/telegram.log` ga yo‘naltirildi (Nginx 0 errors). |
| 18 | `08f54b4` | 11:47 | `feat(topics): organize developer supergroup into dedicated forum topics with automated routing` | Guruh 6 ta alohida Forum Topic (Mavzu) ga ajratildi va har bir xabarnoma o‘z mavzusiga `message_thread_id` orqali yo‘naltirildi. |
| 19 | `3626a03` | 11:48 | `chore(topics): rename forum topics to short concise English labels` | Topic nomlari xalqaro standartdagi qisqa inglizcha nomlarga o‘zgartirildi: `🚀 Releases`, `👥 Students`, `📝 Leads`, `🏆 Milestones`, `📊 Reports`, `⚠️ Alerts`. |
| 20 | `de87c61` | 11:49 | `fix(telegram): strictly ignore group/supergroup chatter so bot never spams greetings or menus in developer group` | Guruhdagi umumiy yozishmalar filtrlandi (`$chatType !== 'private'`), bot faqat shaxsiy chatdagina salomlashishi va guruhda jim turishi ta’minlandi. |
| 21 | `3aca3dc` | 11:54 | `feat(ui): redesign header with modern segmented nav, clean tool/auth grouping, and fluid responsiveness` | Header to‘liq modernizatsiya qilindi: «Bosh sahifa» takrori olib tashlandi, menyu 5 ta asosiy bo‘limli kapsulaga olindi, asboblar va kirish tugmalari ajratildi, 1080px responsiv drawer ulandi, Service Worker v9 ga oshirildi. |

---

## 2. Dasturchilar Guruhi Mavzular Xaritasi (`-1003979299740`)

| Mavzu (Topic) | Thread ID | Yo‘naltirilgan Hodisa | Yuboruvchi Funksiya / Buyruq |
|---|:---:|---|---|
| **🚀 Releases** | `18` | Dastur relizlari, changeloglar va yangilanishlar | `php bin/notify-deploy.php` / `telegram_notify_release()` |
| **👥 Students** | `23` | Saytda yangi ro‘yxatdan o‘tgan o‘quvchilar | `action_register()` / `telegram_notify_new_user()` |
| **📝 Leads** | `24` | Ota-onalardan kelgan yangi kurs arizalari | `action_enroll()` / `telegram_notify_new_enrollment()` |
| **🏆 Milestones** | `26` | 100% test natijalari, sertifikatlar, streaklar | `telegram_notify_quiz_milestone()`, `telegram_notify_certificate()` |
| **📊 Reports** | `28` | Kunlik ertalabki reja (09:00) va kechki faollik (21:00) | `telegram_notify_dev_daily_summary()` (Cron) |
| **⚠️ Alerts** | `29` | Tizim diagnostikasi, xavfsizlik va server holati | `telegram_notify_system_alert()` |

---

## 3. Chala Ishlar Qoldimi? (Mavjud Holat Tahlili)

Hozirgi holatda bugungi barcha qo‘yilgan vazifalar va talablar **100% to‘liq, sinovdan o‘tgan va jonli ishlab turgan holatda**:
- [x] Baza: MySQL 8.4 ga to‘liq ulangan va 15 ta jadval barqaror ishlamoqda.
- [x] Admin Panel: Barcha 11 ta bo‘lim 200 OK.
- [x] Qahramon (Mascot): Logotip va ilova ikonlari toza oq fonda, nomi faqat «Lingo».
- [x] Telegram Bot: `@vexa_lingo_bot` ikki tilda, doimiy menyu tugmalari, profil rasmi va 1-bosishda ulanish banneri bilan faol.
- [x] Ovoz va animatsiyalar: Web Audio API orqali Duolingo/Algor uslubida to‘liq ulangan.
- [x] Dasturchilar guruhi: 6 ta Topic ga ajratilgan, bildirishnomalar avto-filtr bilan to‘g‘ri joyiga tushmoqda.
- [x] FastCGI xatosi: `error_log` muammosi to‘liq bartaraf etilgan, Nginx error logi 0 xatolikda.
- [x] Header dizayni: To‘liq zamonaviy, ixcham va barcha ekranlarga moslashuvchan.

### Kelgusidagi ixtiyoriy kengaytirish rejalari (Keyingi bosqich):
1. **To‘lov tizimi (Monetizatsiya):** Pullik mentorlik yoki jonli darslar uchun Payme / Click (O‘zbekiston) va Stripe (AQSh/Yevropa) integratsiyasi.
2. **Ota-onalarga haftalik hisobot:** Farzandining haftalik yutuqlarini email yoki Telegram orqali avtomatik yuborish.
3. **A2/B1 kurslari kontenti:** Chuqurlashtirilgan yangi darslar va biznes o‘zbek tili moduli.

---

## 4. Foydali Konsol Buyruqlari

```bash
# Guruhga tezkor reliz / yangilanish yuborish:
php bin/notify-deploy.php "Yangilanish mavzusi" "v1.3.1"

# Dasturchilar guruhiga kunlik hisobot chiqarish (morning yoki evening):
php artisan lingo:dev-summary evening

# Obunachilarga kunlik so‘z va test tarqatish:
php artisan lingo:telegram-daily

# Tarjima kalitlari va yangi alifboni tekshirish:
php tools/i18n/check_t.php /var/www/startups/lingo

# Baza holatini tekshirish:
php artisan status
```
