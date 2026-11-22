# LingoUz — plan.md

> **LingoUz** — O‘zbek tilini o‘rganmoqchi bo‘lgan hamma uchun zamonaviy, interaktiv onlayn ta’lim platformasi: xorijdagi vatandoshlar, bolalar, kattalar, sayyohlar va o‘zbek madaniyatiga qiziquvchilar uchun.  
> **Jonli URL:** https://lingo.vexa.uz · **Lokal yo‘l:** `/var/www/startups/lingo` · **Oxirgi yangilanish:** 2026-11-21

---

## 📌 QAT’IY QOIDA: Yangi O‘zbek Lotin Alifbosi Standarti

> [!IMPORTANT]
> Loyihada foydalanuvchiga ko‘rinadigan har qanday o‘zbekcha matn (UI, darslar, lug‘at, savollar, bildirishnomalar, tugmalar, placeholder'lar) kiritilayotganda yoki tahrirlanayotganda **QAT’IY** yangi o‘zbek lotin alifbosi standartiga amal qilinishi shart!

### Harf almashtirish qoidalari:
- `O‘ / o‘` → `Ö / ö`
- `Sh / sh` → `Ş / ş`
- `Ch / ch` → `Ç / ç`
- `G‘ / g‘` → `Ğ / ğ`

### Asosiy talablar:
1. **To‘liq qamrov:** Saytdagi barcha foydalanuvchiga ko‘rinadigan matnlar yangi standartda bo‘lishi shart.
2. **Barcha UI elementlari:** Navbar, menyu, tugmalar (buttons), sarlavhalar (headings), paragraflar, `placeholder`lar, formalar, modallar, `notification` / `flash` xabarlari, footer va yordamchi matnlar tekshirilgan va yangi alifboda bo‘lishi kerak.
3. **Faqat o‘zbekcha matnlar:** O‘zbek tilidagi matnlar yangi alifboga o‘tkaziladi; inglizcha, ruscha va boshqa tillardagi matnlar o‘zgartirilmaydi.
4. **Texnik daxlsizlik:** Kod, variable, function, class, API, URL, route, database table/column, fayl nomlari va developer-facing identifikatorlar o‘zgartirilmaydi (ular doimiy ingliz tilida bo‘ladi).
5. **To‘g‘ri kontekst va registr:** `O‘`, `o‘`, `Sh`, `sh`, `Ch`, `ch`, `G‘`, `g‘` kombinatsiyalari to‘g‘ri kontekstda almashtiriladi, bosh va kichik harf registri to‘liq saqlanadi.
6. **Imlo va ma'no:** Matnning ma'nosi, imlosi va grammatik strukturasi o‘zgarmaydi, faqat alifbo transformatsiyasi amalga oshiriladi.
7. **Eski alifboga yo‘l yo‘q:** Birorta o‘zbekcha foydalanuvchi matni eski alifboda (`o'`, `g'`, `sh`, `ch`) qolmasligi shart.

---

## 0. Hozirgi holat — MVP tayyorligi (~90%)

Loyihada "Pilotga ariza" tizimi to‘liq chiqarilib, to‘g‘ridan-to‘g‘ri ommaviy ro‘yxatdan o‘tish (`/register`) va to‘liq o‘quv jarayoni yo‘lga qo‘yildi. Ma’lumotlar bazasi sifatida tezkor va mustaqil **SQLite (WAL)** ishlatiladi.

| # | Qism / Modul | Tayyorlik | Hozirgi holat va imkoniyatlar |
|---|--------------|-----------|-------------------------------|
| 1 | **Landing page (Bosh sahifa)** | 98% | Registon/Lapis dizayni, maskot animatsiyalari, interaktiv namunalar, 3 ustunli toza footer, yangi alifbo |
| 2 | **Ro‘yxatdan o‘tish & Kirish** | 95% | `/register` (to‘g‘ridan-to‘g‘ri o‘quvchi profili ochish), `/login`, 15 daqiqada 5 xato urinish bo‘yicha login throttling, CSRF himoyasi |
| 3 | **Tanishuv (Onboarding)** | 95% | 4 qadamli maqsad va qiziqishlar so‘rovi + 10 ta savolli boshlang‘ich daraja testi (A0/A1/A2 aniqlash va mos darslarni avto-ochish) |
| 4 | **O‘quvchi kabineti (Dashboard)** | 92% | Shaxsiy tavsiya, kunlik maqsad (XP), darslar xaritasi, progress foizi, nishonlar, tangalar, keyingi jonli muloqot vaqti |
| 5 | **Interaktiv Dars Pleyeri** | 95% | 1) Slayd/video sahna, 2) Talaffuzli audio lug‘at, 3) Matn o‘qish (gapma-gap audio va tarjima), 4) «Bilasizmi?» madaniyat kartasi, 5) Juftlik o‘yini (+tangalar), 6) Tezkor mashq, 7) Yakuniy test |
| 6 | **Ko‘p kurslilik & Kurslar katalogi** | 95% | 3 ta to‘liq kurs (17 ta dars, 120+ lug‘at, 85+ savol): «Ilk qadamlar» (A0), «Sayohatchilar uchun» (Survival Uzbek), «Madaniyat ko‘prigi» |
| 7 | **Baholash, Qulf & Natijalar** | 90% | ≥80% o‘tish balli qoidasi, server darajasida test qulfi tekshiruvi, xatoliklar tahlili, konfetti/rag‘batlantirish |
| 8 | **Audio & Talaffuz (TTS)** | 90% | Web Speech API turkiy fonetika fallback (`tr-TR` yuqori sifatli ovoz) va moslashtirilgan o‘qish algoritmi |
| 9 | **Ko‘p tillilik (UZ / EN)** | 95% | Header va mobil menyuda til almashtirgich (`?lang=uz|en`), 468+ kalit to‘liq tarjima qilingan |
| 10 | **Diplom (Sertifikat)** | 90% | Kurs to‘liq tugatilganda dinamik beriladigan rasmiy sertifikat (`/certificate?course=ID`), chop etish/PDF rejimi |
| 11 | **Admin boshqaruv markazi** | 92% | Kurslar, darslar, lug‘at, savollar, jonli darslar to‘liq CRUD; o‘quvchilar ro‘yxati, baholar jurnali (heatmap), statistik ko‘rsatkichlar (DAU/WAU/MAU, voronka) |
| 12 | **Jonli Zoom xonasi** | 85% | Jonli muloqot jadvali (Toshkent, London, AQSh vaqtlari bilan), havola va mentor ma’lumotlari |

---

## 1. MVP da yana nimalar bor? (Mavjud asosiy imkoniyatlar)

1. **3 xil kurs va turli auditoriyalar:**
   - **Boshlang‘ich o‘zbek tili (A0):** Harflar, salomlashish, tanishuv, raqamlar, oila a’zolari.
   - **Sayohatchilar uchun o‘zbek tili (Survival):** Bozorlik qilish, narx so‘rash, taksi chaqirish, choyxona va taomlar, favqulodda yordam (112).
   - **Ikki madaniyat ko‘prigi:** O‘zbekiston va G‘arb/Amerika madaniyati solishtirmalari (Osh an’analari, mehmondo‘stlik, Samarqand/Buxoro/Xiva, milliy kiyimlar).
2. **Interaktiv mashq turlari:**
   - `choice`: Ko‘p variantli test.
   - `listen`: Audio eshitib to‘g‘ri javobni tanlash.
   - `build`: So‘z bloklaridan to‘g‘ri gap tuzish.
   - `fill`: So‘zni yozib kiritish (ekrandagi yangi lotin klaviatura yordamida: `ö`, `ğ`, `ş`, `ç`).
   - `match`: So‘z va tarjimani juftlash mini-o‘yini.
3. **Gamifikatsiya va motivatsiya:**
   - XP ballari, darajalar, 9 ta maxsus nishon (badge), tangalar (coins), har dars oxiridagi rag‘batlantirish animatsiyalari.
4. **Xavfsizlik va bolalar himoyasi:**
   - COPPA talablariga moslik: ortiqcha shaxsiy ma’lumot so‘ralmaydi (taxallus yetarli). CSRF himoyasi, brute-force throttling.

---

## 2. Ushbu MVP da yana nimalar qilamiz? (Keyingi ustuvor qadamlar)

MVP ni mukammal startap holatiga yetkazish uchun quyidagi qadamlar rejalashtirilgan:

### 2.1. Ovozni aniqlash — Speech-to-Text (Talaffuzni tekshirish) [BAJARILDI ✅ 2026-11-21]
- [x] Web Speech API (`SpeechRecognition` / `webkitSpeechRecognition`, `uz-UZ` tilida) orqali o‘quvchining mikrofondan aytgan so‘zini taniydigan Speech Engine yaratildi.
- [x] O‘zbek kirill/lotin fonetik normalizatori va Levenshtein masofasi orqali talaffuz aniqligi foizda hisoblanadi (0–100%).
- [x] Dars ichidagi **Tezkor mashq** (`#mashq`) bo‘limiga yangi `speak` («Talaffuz qiling») vazifa turi qo‘shildi: katta pulsatsiyali mikrofon tugmasi, ovozni eshitib to‘g‘riligini tekshirish, ball va yutuq ovozi/konfetti berish.
- [x] **Ovozli lug‘at** (`#lugat`) dagi har bir so‘z kartochkasiga mikrofon tugmasi qo‘shildi (o‘quvchi o‘z talaffuzini mustaqil tekshirishi mumkin).
- [x] Bosh sahifa (Landing) dagi namunaviy kartaga «Özingiz ayting» mikrofon testi ulandi.

### 2.2. Haqiqiy video darslar integratsiyasi [BAJARILDI ✅ 2026-11-21]
- [x] Barcha 17 ta dars uchun haqiqiy o‘quv video darsliklar va madaniy hujjatli videoroliklar integratsiya qilindi (Ibrat Farzandlari, O‘zbekiston milliy madaniyati va sayohat videolari).
- [x] Har bir dars ostiga ikki tilli interaktiv subtitrlar tizimi (`video_subtitles`) qo‘shildi: vaqt ko‘rsatkichi (`0:05`), yangi lotin alifbosidagi o‘zbekcha matn, inglizcha tarjima, har bir qatorda alohida ovoz eshitish va o‘z talaffuzini mikrofonda tekshirish tugmalari.
- [x] Subtitrlarni filtrlash rejimi ulandi: `[Ikkala til]`, `[O‘zbekcha]`, `[English]`.

### 2.3. O‘quvchilar Reytingi va Ligasini (Leaderboard) joriy etish [BAJARILDI ✅ 2026-11-21]
- [x] Maxsus `/leaderboard` sahifasi yaratildi: Top-3 Shohsupa (🥇 1st Oltin, 🥈 2nd Kumush, 🥉 3rd Bronza) va to‘liq o‘quvchilar reyting jadvali.
- [x] O‘quvchi boshqaruv panelida (`/dashboard`) Top-5 haftalik liga vidjeti va foydalanuvchining o‘z o‘rni aks ettirildi.
- [x] Reyting darslar, to‘plangan XP, ketma-ketlik (streak) va test natijalariga asoslangan holda avtomatik hisoblanadi.

### 2.4. Progressive Web App (PWA) — Mobil Ilovaga aylantirish [BAJARILDI ✅ 2026-11-21]
- [x] `manifest.webmanifest` to‘liq standart bo‘yicha sozlandi (nomi, do‘ppi va registon siluetli logotipi, ranglar gammasi `#0284c7`, tezkor havolalar).
- [x] 192x192, 512x512, apple-touch-icon va maskable formatdagi ilova belgilari yaratildi.
- [x] `sw.js` (Service Worker) ulanib, asosiy statik fayllar keshlandi va oflayn rejim qo‘llab-quvvatlandi.
- [x] Brauzer panellarisiz mustaqil mobil ilova sifatida (Add to Home Screen) bir tugma bilan o‘rnatish imkoniyati qo‘shildi.

### 2.5. Aqlli Flashcards (So‘zlarni tezkor takrorlash trenajyori) [BAJARILDI ✅ 2026-11-21]
- [x] Yangi `/flashcards` sahifasi va navbarda tezkor havolalar yaratildi.
- [x] 3D kartochkani aylantirish (Space yoki bosish), o‘zbekcha so‘z, talaffuz transkripsiyasi, inglizcha tarjimasi va namunaviy gap.
- [x] Web Speech API orqali so‘z talaffuzini ovozli tinglash hamda mikrofondan o‘quvchining talaffuzini sun’iy intellekt orqali tekshirish.
- [x] «Yana takrorlash» (Left arrow `←`) va «Bilib oldim! (+2 XP)» (Right arrow `→`) trenajyor mexanikasi.
- [x] Bosqich yakunlanganda konfetti, bayramona tabrik va API orqali o‘quvchi balansiga real XP qo‘shilishi.

### 2.6. Telegram Bot bilan integratsiya (Retenshn va Eslatmalar) [BAJARILDI ✅ 2026-11-22]
- [x] **Rasmiy bot manzili:** `t.me/vexa_lingo_bot` (Token va Webhook to‘liq sozlandi va faol).
- [x] **2 ta tilda to‘liq ishlash (O‘zbekcha va Inglizcha):** Bot foydalanuvchi tilini avtomatik aniqlaydi (`telegram_lang`), inline til almashtirgich (`/lang`) orqali xohlagan paytda almashtirish mumkin.
- [x] **Algor.uz uslubidagi 1-bosishda ulanish:** O‘quvchi dashboardidagi yorqin bannerdagi «Botni ulash ↗» tugmasini bosishi bilan bot ochiladi (`/start link_<token>`).
- [x] **Real-time status tekshirish:** Dashboard brauzer oynasida polling orqali foydalanuvchi botni ulanishi bilan sahifani yangilamasdan avtomatik muvaffaqiyat kartasiga aylanadi va +20 XP hamda 5 ta tanga taqdim etiladi.
- [x] **Professional Dynamic OG Share Cards:** Har bir o‘quvchi profili (`/og/profile/{id}.png`), kunlik so‘z (`/og/word/{id}.png`) va platforma (`/og/default.png`) uchun avtomatik 1200x630 formatdagi yuqori sifatli brendli karta generatsiyasi.
- [x] **Kunlik o‘quv kartochkasi (`/word`):** Har kuni yangi o‘zbekcha so‘z, talaffuz transkripsiyasi, inglizcha ma’nosi, namunaviy gap, rasmli OG karta va Flashcards trenajyoriga havola.
- [x] **Interaktiv mini-testlar (`/quiz`):** Telegramning rasmiy `sendPoll` (Quiz rejimi) orqali to‘g‘ri javob va tushuntirish bilan ikki tilli mini-viktorina.
- [x] **Streak xavfsizligi va eslatmalar:** Kun davomida dars qilmagan o‘quvchilarga streakni saqlab qolish va darsni davom ettirish bo‘yicha eslatmalar.
- [x] **Bot tavsifi va qisqa ma’lumoti:** `setMyDescription`, `setMyShortDescription` va `setMyCommands` orqali Telegramda bot profili va buyruqlar menyusi ikki tilda to‘liq kiritildi.
- [x] **Cron avtomatizatsiyasi:** `php artisan lingo:telegram-daily` konsol buyrug‘i.
- [x] **Yangi o‘zbek lotin alifbosi:** Bot yuboradigan barcha xabarlar va savollarda qat’iy yangi alifbo (`Ö/ö`, `Ş/ş`, `Ç/ç`, `Ğ/ğ`).

### 2.7. Sertifikatni tekshirish tizimi (QR-kod & Rasmiy Verification) [BAJARILDI ✅ 2026-11-22]
- [x] **Rasmiy tekshirish sahifasi (`/verify/{code}`):** QR-kod yoki identifikatsiya raqami (masalan: `LU-1-0003`) orqali har qanday ish beruvchi yoki ota-ona o‘quvchining diplom haqiqiyligini tekshirishi mumkin.
- [x] **Diplom ichida jonli QR-kod:** Chop etiladigan diplom varag‘ida (`/certificate`) tekshirish sahifasiga olib boruvchi dinamik QR-kod joylashtirildi.
- [x] **Telegram bot integratsiyasi:** Botda `/cert` buyrug‘i orqali o‘quvchi o‘z sertifikatini bitta bosishda tekshirishi va do‘stlariga yuborishi mumkin.

### 2.8. Ovozli Ertaklar va Madaniy Hikoyalar (Interactive Audio Stories) [BAJARILDI ✅ 2026-11-22]
- [x] **4 ta mashhur o‘zbek xalq ertagi:** «Zumrad va Qimmat», «Susambil», «Oltin Tarvuz», «Afandi va Donishmand».
- [x] **Gapma-gap audio talaffuz:** Har bir jumlani Web Speech API orqali tabiiy o‘zbek tilida ovozli tinglash.
- [x] **Ikki tilli matn va lug‘at kartalari:** Inglizcha tarjimani bir tugma bilan yoqish/o‘chirish hamda har bir jumlada uchragan asosiy so‘zlar lug‘ati.
- [x] **Tushunish viktorinasi va XP mukofoti:** Har bir ertak yakunida tushunishni tekshiruvchi test va o‘quvchiga +20 XP bal berish.
- [x] **Navigatsiya:** Sayt menyusiga «Ertaklar» va Telegram botga `/stories` buyrug‘i ulandi.

### 2.4. Ota-onalar va murabbiylar monitoringi
- [ ] Ota-onalar uchun alohida ko‘rinish: farzandining kunlik dars vaqti, qaysi so‘zlarni qiynalib o‘rgangani, test natijalari.
- [ ] Haftalik progressni email yoki Telegram orqali avtomatik yuborish.

### 2.5. To‘lov va obuna tizimi (Monetizatsiya tayyorgarligi)
- [ ] Bepul kurslardan so‘ng chuqurlashtirilgan darajalar yoki jonli guruh darslari uchun to‘lov integratsiyasi:
  - Mahalliy foydalanuvchilar uchun: **Payme / Click**.
  - Xorijdagi foydalanuvchilar uchun: **Stripe** (xalqaro kartalar orqali).

### 2.6. Yangi kurslar va kontent bazasini kengaytirish
- [ ] **A1–A2 davomi:** Fe’l zamonlari, erkin suhbat, do‘konda va restoranda to‘liq muloqot.
- [ ] **Bolalar uchun ertaklar va hikoyalar:** O‘zbek xalq ertaklari asosidagi audio-kitoblar va mini-o‘yinlar.
- [ ] **Biznes o‘zbek tili:** Xorijiy hamkorlar va diplomatlar uchun rasmiy muomala etiketi.

---

## 3. Texnik eslatmalar va yo‘riqnoma

- **CLI buyruqlari:**
  - `php artisan status` — Tizim va baza holati.
  - `php tools/i18n/check_t.php /var/www/startups/lingo` — Tarjima kalitlari va yangi alifbo muvofiqligini tekshirish.
  - `php bin/seed-demo.php` — Boshlang‘ich kurslar va darslarni qayta tiklash.
- **Dasturchi kodi tili:** Doimo faqat **English** (o‘zgaruvchilar, funksiyalar, izohlar, commitlar).
- **Foydalanuvchi interfeysi tili:** Yangi O‘zbek Lotin Alifbosi (`Ö/ö`, `Ş/ş`, `Ç/ç`, `Ğ/ğ`) va ingliz tili (`en`).
