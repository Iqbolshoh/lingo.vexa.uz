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

- **Backend:** Native Modern PHP 8.x (High-Performance MVC, Zero framework latency).
- **Database:** SQLite 3 + WAL rejimi (Ultra yengil, tezkor va portativ).
- **Frontend:** Modern CSS3 Variables, Glassmorphism, Vanilla ES6+ JavaScript, Web Speech API, Service Workers.
- **CLI vositasi:** `artisan` buyruqlar satri interfeysi (`php artisan status`).

---

## 📂 Loyiha Strukturasi

```text
├── bin/                 # CLI yordamchi skriptlar (seed-demo.php)
├── database/            # SQLite ma'lumotlar bazasi va schema migratsiyalari
├── public/              # Web server root (index.php, CSS, JS, SVG maskotlar, PWA manifest, sw.js)
├── src/                 # Asosiy biznes mantiq:
│   ├── handlers.php     # Controller / Web route handlers
│   ├── bootstrap.php    # Ilova ishga tushishi, session, CSRF va router
│   ├── i18n.php         # Ko'p tilli tizim va Yangi Lotin alifbosi konvertori
│   ├── schema.php       # Baza sxemasi va migratsiyalar
│   ├── admin.php        # Administrator boshqaruv markazi
│   └── views/           # PHP shablonlari (Dashboard, Flashcards, Leaderboard, Lessons, vb.)
├── tools/               # i18n tarjima tekshiruv vositalari
├── artisan              # CLI buyruqlar markazi
└── plan.md              # Loyiha rivojlanish rejasi
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
