# 🇺🇿 LingoUz — O‘zbek Tilini O‘rganish Platformasi

> **Sayt:** [https://lingo.vexa.uz](https://lingo.vexa.uz)  
> Zamonaviy, interaktiv va professional onlayn o‘zbek tili ta’lim platformasi (bolalar, kattalar, vatandoshlar va xorijlik sayyohlar uchun).

---

## 🚀 Asosiy Imkoniyatlar (Features)

- **🏛 Registon Heritage Dizayn:** O‘zbek milliy me’morchiligi (Registon moviy gumbazlari, girih naqshlari va do‘ppi san’ati) bilan uyg‘unlashgan zamonaviy Glassmorphism UI.
- **🦉 Lingo — Interaktiv Maslahatchi Boyo‘g‘li:** Sahifalarda o‘quvchini kutib oluvchi, rag‘batlantiruvchi va jonli gapiruvchi rasmiy maskot.
- **🎙 Speech-to-Text Talaffuz Baholovchi:** Web Speech AI orqali o‘quvchining mikrofonga aytgan o‘zbekcha so‘zini tinglaydi va talaffuzini real vaqtda baholaydi.
- **🎬 Jonli Video Darslar va Interaktiv Subtitrlar:** Barcha 17 ta dars uchun haqiqiy o‘quv video darsliklari, har bir qatorda alohida ovoz eshitish va mikrofonda o‘zini sinash imkoniyati.
- **🗂 Aqlli 3D Flashcards Trenajyori:** Bo‘sh vaqtda so‘zlarni 3D kartochkalar yordamida tezkor takrorlash (`Space`, `←`, `→` boshqaruvi) va to‘g‘ri topilgan har bir so‘z uchun real XP ballari.
- **🏆 O‘quvchilar Reytingi va Haftalik Liga (Leaderboard):** Top-3 Shohsupa (🥇 Oltin, 🥈 Kumush, 🥉 Bronza) va to‘liq o‘quvchilar yetakchilik jadvali.
- **📱 PWA (Progressive Web App):** Brauzer panellarisiz smartfon (Android/iOS) va kompyuter ekraniga mustaqil mobil ilova sifatida o‘rnatib olish imkoniyati.
- **🔡 Yangi O‘zbek Lotin Alifbosi:** Rasmiy islohot standarti bo‘yicha to‘liq moslashtirilgan (`Ö/ö`, `Ş/ş`, `Ç/ç`, `Ğ/ğ`).
- **🌐 Ikki Tilli Interfeys (UZ / EN):** O‘zbekcha va inglizcha to‘liq lokalizatsiya (`check_t.php` orqali 550+ kalit tekshirilgan).

---

## 🛠 Arxitektura va Texnologiyalar

- **Framework:** **Laravel 13 (v13.43.0)** — Eng so‘nggi, zamonaviy Laravel versiyasi.
- **PHP:** PHP 8.5+ (Composer 2.9+).
- **ORM & Database:** Eloquent ORM + SQLite 3 (WAL yuqori tezlikdagi tranzaksiyalar rejimi).
- **Frontend:** Modern CSS3 Variables, Glassmorphism, Vanilla ES6+ JavaScript, Web Speech AI, PWA Service Workers.
- **CLI vositasi:** Rasmiy Laravel `artisan` (`php artisan route:list`, `php artisan tinker`).

---

## 📂 Loyiha Strukturasi

```text
├── app/                 # Laravel 13 App (Http Controllers, Eloquent Models, Providers)
├── bootstrap/           # Laravel 13 Bootstrap (app.php, providers.php)
├── config/              # Laravel konfiguratsiyalari (app, auth, database, session, vb.)
├── database/            # SQLite ma'lumotlar bazasi va migratsiyalar
├── public/              # Web root (index.php, CSS, JS, SVG maskotlar, PWA manifest, sw.js)
├── routes/              # Laravel yo‘nalishlari (web.php, console.php)
├── src/                 # Ta’lim mantiqi, i18n, Yangi Lotin alifbosi va shablonlar:
│   ├── handlers.php     # Controller / Web route handlers
│   ├── bootstrap.php    # Ilova yordamchilari, CSRF va router
│   ├── i18n.php         # Yangi Lotin alifbosi konvertori (Ö/ö, Ş/ş, Ç/ç, Ğ/ğ)
│   ├── schema.php       # Baza sxemasi va ma'lumotlar
│   ├── admin.php        # Administrator boshqaruv markazi
│   └── views/           # UI shablonlari (Dashboard, Flashcards, Leaderboard, vb.)
├── storage/             # Kesh, sessiyalar va loglar
├── tools/               # i18n tarjima tekshiruv vositalari
├── artisan              # Rasmiy Laravel 13 CLI
└── composer.json        # Paketlar va bog'liqliklar
```

---

## 💻 Mahalliy Muhitda Ishga Tushirish

```bash
# 1. Repozitoriyani klonlash
git clone git@github.com:Iqbolshoh/lingo.vexa.uz.git
cd lingo.vexa.uz

# 2. Demo ma'lumotlarni bazaga yuklash
php bin/seed-demo.php

# 3. Mahalliy serverni ishga tushirish
php -S localhost:8000 -t public
```

---

## 📜 Mualliflik Huquqi
© 2026 **LingoUz**. Barcha huquqlar himoyalangan.
