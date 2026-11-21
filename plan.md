# LingoUz by Gulomov’s Academy — plan.md

AQShda yashovchi 7–11 yoshli o‘zbek bolalari uchun onlayn o‘zbek tili maktabi (startap).
URL: https://lingo.vexa.uz · Yo‘l: `/var/www/startups/lingo` · Oxirgi to‘liq tekshiruv: **2026-11-21**

---

## 0. Umumiy holat — qay darajada tayyor

**Umumiy tayyorlik: ~67% (ishlaydigan MVP / pilot bosqichi).**
Sayt to‘liq dinamik: barcha ma’lumot (foydalanuvchilar, darslar, lug‘at, testlar, natijalar, nishonlar, arizalar, jonli darslar) **SQLite** bazasida saqlanadi va har so‘rovda bazadan o‘qiladi. Hozircha SQLite yetarli (owner qarori 2026-11-21: MySQL/Laravel'ga ko‘chirish shart emas).

| # | Qism | Tayyorlik | Holat |
|---|------|-----------|-------|
| 1 | Landing (bosh sahifa) | 95% | Ishlaydi, yangi lapis dizayn (2026-11-21) |
| 2 | Pilotga ariza (enrollment) | 75% | Ishlaydi, validatsiya zaif |
| 3 | Ro‘yxatdan o‘tish / kirish / chiqish / profil | 75% | Ishlaydi, brute-force himoyasi yo‘q |
| 4 | O‘quvchi paneli (darslar xaritasi, progress) | 85% | Ishlaydi |
| 5 | Dars pleyeri (sahna, flip-kartalar, o‘yin, ovoz) | 85% | Ishlaydi, haqiqiy video yo‘q |
| 6 | Test va 80% qulf qoidasi | 60% | Sahifa qulfi bor, lekin POST orqali chetlab o‘tiladi |
| 7 | Nishonlar (badges) va tangalar | 70% | Nishonlar bazada, tangalar faqat brauzerda |
| 8 | Diplom (sertifikat) | 40% | Hamma uchun ochiq, darslarni tugatish tekshirilmaydi |
| 9 | Jonli Zoom xonasi | 45% | Vaqtlar qo‘lda yozilgan, bazadagi sana ishlatilmaydi |
| 10 | Admin panel | 50% | Faqat ko‘rish + ariza holati + qo‘lda dars ochish; kontentni tahrirlash yo‘q |
| 11 | Ota-onalar qo‘llanmasi, Maxfiylik (COPPA) | 100% | Statik matn |
| 12 | Xavfsizlik va infratuzilma | 60% | CSRF, xavfsiz sessiya bor; throttling, xato sahifasi, kesh muammolari bor |
| 13 | To‘lov, email xabarnomalar, ota-ona kabineti | 0% | Hali boshlanmagan |

---

## 1. Texnik arxitektura (haqiqiy holat)

- **Stack:** oddiy PHP 8.5 (framework yo‘q) + SQLite (PDO, WAL rejimi). Nomi “Laravel” deb yozilgan, lekin **bu Laravel emas** — `artisan` ham o‘zimizning kichik CLI skripti.
- **Server:** nginx → `php-fpm-students` (alohida pool, `students` foydalanuvchisi, open_basedir, 2 GB RAM / 1.5 CPU cgroup limiti). nginx konfig: `/etc/nginx/sites-available/student-lingo.conf`, root = `public/`.
- **Front controller:** `public/index.php` — oddiy route jadvali (`'GET /path' => handler`), `/up` health-check (bazani tekshiradi).
- **Fayllar:**
  - `src/bootstrap.php` — DB ulanish, avto-migratsiya, sessiya, CSRF, flash, auth yordamchilari, `render()`.
  - `src/schema.php` — barcha jadvallar (SCHEMA_VERSION = 1).
  - `src/content.php` — boshlang‘ich kontent: 1 kurs, 5 dars, 37 so‘z, 25 savol, 5 nishon, 2 demo akkaunt, 1 jonli dars.
  - `src/handlers.php` — barcha sahifa va amallar (≈745 qator).
  - `src/icons.php` — SVG ikonkalar to‘plami (`icon('name')`), emoji o‘rniga.
  - `src/views/*.php` — 14 ta shablon + `layout.php`.
  - `public/assets/style.css` (≈1900 qator, Registon dizayni), `app.js` (ovoz, flip, o‘yin, konfetti, mobil menyu), maskot SVG'lar.
- **CLI:** `php artisan migrate | db:seed | status`, `php bin/seed-demo.php`.
- **Git:** lokal repo, `main` branch, remote yo‘q.

## 2. Ma’lumotlar bazasi (SQLite, `database/database.sqlite`)

| Jadval | Vazifasi |
|--------|----------|
| `users` | O‘quvchi va adminlar (role, ism, email, parol hash, yosh, daraja, vaqt zonasi) |
| `enrollments` | Ota-onalar pilot arizalari + holat (pending / contacted / approved / rejected) |
| `courses` | Kurs (hozir 1 ta: A0–A1, 7–11 yosh) |
| `lessons` | Darslar (tartib, nom uz/en, maqsad, davomiylik, video, o‘tish bali) |
| `vocabulary` | Har dars lug‘ati (so‘z, tarjima, talaffuz, misol) |
| `quizzes`, `questions` | Dars testi va savollar (variantlar JSON, to‘g‘ri javob indeksi, izoh) |
| `quiz_attempts` | Har bir test urinishi (ball, foiz, o‘tdi/o‘tmadi) |
| `lesson_progress` | O‘quvchi × dars holati (locked / available / completed, eng yaxshi ball, admin override) |
| `badges`, `user_badges` | Nishonlar va olingan nishonlar |
| `live_sessions` | Zoom jonli mashg‘ulotlar |
| `startup_meta` | Sxema versiyasi |

Hozirgi jonli ma’lumot (2026-11-21): 3 foydalanuvchi, 1 ariza, 5 dars, 37 so‘z, 25 savol, 2 test urinishi.

## 3. Sahifalar va funksiyalar (rol bo‘yicha)

### 3.1 Mehmon (tizimga kirmagan)
| Route | Funksiya |
|-------|----------|
| `GET /` | Landing: hero + maskot, muammo/yechim, 4 bosqichli metodika, darslar ro‘yxati (bazadan), talaffuz sinovi (1-dars lug‘ati, Web Speech API), FAQ, pilotga ariza formasi |
| `POST /enroll` | Ariza saqlash; ixtiyoriy “akkaunt ham yaratish” — o‘quvchi yaratib, avtomatik kiritadi |
| `GET/POST /register` | O‘quvchi ro‘yxati (ism, ota-ona emaili, parol, yosh, daraja, vaqt zonasi) → avto-login |
| `GET/POST /login` | Email yoki qisqa login (`demo` → `demo@lingo.uz`), kirgan bo‘lsa — akkaunt almashtirish kartasi |
| `GET/POST /logout` | Chiqish + sessiya regeneratsiyasi |
| `GET /parent-tips` | Ota-onalarga 5 ta tavsiya |
| `GET /privacy`, `/terms` | COPPA maxfiylik siyosati |
| `GET /up` | Monitoring uchun health-check |

### 3.2 O‘quvchi
| Route | Funksiya |
|-------|----------|
| `GET /dashboard` | Salomlashish, progress foizi, darslar xaritasi (tugagan / ochiq / qulf), nishonlar, keyingi jonli dars, profil qisqacha |
| `GET /lessons/{id}` | Dars: 1) sahna pleyeri (lug‘atdan iboralar, ovoz), 2) 3D flip lug‘at kartalari, 3) so‘z moslashtirish o‘yini + tangalar, 4) yakuniy test |
| `POST /lessons/{id}/quiz` | Testni baholash, urinishni saqlash, ≥80% bo‘lsa keyingi darsni ochish va nishon berish |
| `GET /lessons/{id}/result` | Natija: foiz, konfetti/ovoz, har savol bo‘yicha tahlil va izoh |
| `GET /live` | Zoom jonli xona, xavfsizlik qoidalari, “Jonli Muloqotchi” nishoni |
| `GET /certificate` | “O‘zbek Tili Bilimdoni” diplomi, chop etish / PDF |
| `GET/POST /profile` | Ism, yosh, daraja, vaqt zonasi, parolni o‘zgartirish; test statistikasi |

### 3.3 Administrator
| Route | Funksiya |
|-------|----------|
| `GET /admin` | Ko‘rsatkichlar, o‘quvchilar ro‘yxati + tugatilgan darslar, arizalar, oxirgi 20 test urinishi |
| `POST /admin/enrollments/status` | Ariza holatini o‘zgartirish |
| `POST /admin/unlock` | O‘quvchiga darsni qo‘lda ochish (manual override) |
| `GET /admin/export` | Arizalarni CSV (Excel uchun UTF-8 BOM) yuklab olish |

### 3.4 Biznes qoidalari
- **80% qoidasi:** keyingi dars faqat oldingi dars testidan ≥80% olinganda ochiladi; admin istalgan darsni ochib bera oladi; admin hamma darsni ko‘radi.
- **Nishonlar:** `first_step` (1-dars), `smart_kid` (har qanday ≥80%), `halfway_hero` (3 dars), `lingo_master` (5 dars), `speaker` (/live sahifasiga kirish).
- **Demo akkauntlar:** o‘quvchi `demo` / `demo123`, admin `admin` / `admin123`.

## 4. 2026-11-21 tekshiruvi — topilgan muammolar

Sinov jonli saytning nusxasida o‘tkazildi (jonli bazaga test yozuvi tushmadi). Barcha sahifalar 200/302 qaytaradi, PHP xatolari yo‘q.

**Jiddiy (birinchi navbatda):**
1. **80% qulfini chetlab o‘tish mumkin** — `POST /lessons/{id}/quiz` dars ochiqligini tekshirmaydi. Sinovda yangi o‘quvchi 1-darsni o‘tmasdan 2–5 darslarni “tugatdi”.
2. **Diplom hammaga ochiq** — `/certificate` darslar tugaganini tekshirmaydi, ro‘yxatdan o‘tgan zahoti diplom chiqadi.
3. **Login’da cheklov yo‘q** — 30 ta noto‘g‘ri parol ketma-ket qabul qilindi (brute-force himoyasi yo‘q).
4. **Ariza orqali noto‘g‘ri akkaunt** — `notanemail` emaili va `1` paroli bilan akkaunt yaratildi; email/parol tekshirilmaydi.

**O‘rtacha:**
5. **Jonli dars vaqtlari qo‘lda yozilgan** (11:00 / 10:00 / 09:00 / 08:00) — bazadagi `starts_at` (hozir 22-Nov 03:04, seed xatosi) bilan mos emas; o‘tib ketgan sessiyalar ham “rejalashtirilgan” bo‘lib qoladi.
6. **Profil validatsiyasi yo‘q** — yosh `999`, daraja `ZZZZ`, vaqt zonasi istalgan matn saqlanadi.
7. **“5 ta dars” qattiq yozilgan** — keyingi darsni ochish (`<= 5`), admin jadvali (“/ 5”), qo‘lda ochish ro‘yxati (2–5), ro‘yxatdan o‘tishda progress yaratish. 6-dars qo‘shilsa ishlamaydi. Qulf ham `id - 1` ga va qat’iy 80 ga bog‘langan (`passing_score` ishlatilmaydi).
8. **Sahna pleyeri maskotni emoji bilan almashtiradi** (`app.js` → `v.icon`), “emoji yo‘q” qoidasiga zid.
9. **Xato sahifasi exception matnini foydalanuvchiga ko‘rsatadi** (`public/index.php`).
10. **CSS/JS keshlanmaydi** — `?v=<?= time() ?>` har so‘rovda yangi, brauzer har safar qayta yuklaydi.

**Kichik:**
11. `GET /logout` — begona sahifa rasm orqali foydalanuvchini chiqarib yuborishi mumkin.
12. Test natijasi tafsilotlari faqat sessiyada; qayta ochilganda savollar tahlili yo‘qoladi.
13. Tangalar (o‘yin) bazaga saqlanmaydi.
14. Dars videolari — vaqtinchalik YouTube havolasi; haqiqiy video yo‘q.
15. Admin kontentni (dars, so‘z, savol, jonli dars) saytdan tahrirlay olmaydi — faqat kod/seed orqali.
16. Footer “Laravel + SQLite” deydi — aslida oddiy PHP.

## 5. Keyingi qadamlar (yo‘l xaritasi)

### 10-BOSQICH: Dizaynni sayqallash [BAJARILDI ✅ 2026-11-21]
- [x] Hero: to‘q lojuvard (lapis) fon, oltin girih naqshi, oltin gradient sarlavha, pastki arka egri chizig‘i.
- [x] Hero’dagi suzuvchi belgilar kartani yopib qo‘yardi — endi karta ostida bir qatorda.
- [x] Shisha (glass) header, gradient tugmalar, fokus halqasi (klaviatura uchun), kartalar hover effekti.
- [x] Bo‘lim sarlavhalariga oltin ornament chiziqlari; footer va o‘quvchi paneli banneriga girih naqshi.
- [x] Mobil hero: statistikalar 3 ustunda, kichik ekranlarda o‘lchamlar moslandi.
- [x] Yopiq mobil menyuning soyasi ekran o‘ng chetida ko‘rinib turardi — tuzatildi.
- [x] Dars sahna pleyeri maskotni emoji bilan almashtirardi — endi maskot qoladi (muammo 8).
- [x] CSS/JS keshlash: `?v=time()` → `?v=filemtime()` (muammo 10).
- [x] Footer: “Laravel + SQLite” → “PHP + SQLite” (muammo 16).
- [x] `prefers-reduced-motion` uchun animatsiyalar o‘chiriladi.
- Barcha o‘zgarishlar `style.css` oxiridagi “Design polish v2” blokida.

### 11-BOSQICH: Xavfsizlik va qoidalar
- [ ] Quiz POST’da dars qulfini tekshirish (muammo 1).
- [ ] Diplomni faqat barcha darslar tugaganda berish (muammo 2).
- [ ] Login throttling (muammo 3), ariza/profil validatsiyasi (4, 6).
- [ ] Xato sahifasida texnik matnni yashirish, faqat logga yozish (9).
- [ ] Logout faqat POST (11).

### 12-BOSQICH: To‘liq dinamik kontent
- [ ] Dars soni va qulfni `sequence_number` + `passing_score` bo‘yicha hisoblash (7).
- [ ] Jonli dars vaqtlarini `starts_at` dan 4 ta AQSh vaqt zonasiga hisoblash, o‘tganlarini yashirish (5).
- [ ] Admin: darslar, lug‘at, savollar, jonli darslar uchun CRUD (15).
- [ ] Test tafsilotlarini `quiz_attempts` ga saqlash (12), tangalarni bazaga yozish (13).

### 13-BOSQICH: Startapni o‘stirish
- [ ] Haqiqiy dars videolari (14).
- [ ] Ota-ona kabineti (bir nechta bola, progress hisobotlari).
- [ ] Email xabarnomalar (ariza qabul qilindi, haftalik hisobot).
- [ ] To‘lov / obuna (pilotdan keyin).
- [ ] Yangi darslar (6–20) va A1–A2 kurslari.

---

## 6. O‘zgarishlar tarixi
- **2026-11-18:** MVP, Registon dizayni, SVG ikonkalar, flip-kartalar, diplom, admin, mobil drawer, login/logout tuzatishlari (1–9 bosqichlar).
- **2026-11-21:** To‘liq tekshiruv o‘tkazildi, plan.md qayta yozildi (haqiqiy holat, tayyorlik foizlari, topilgan 16 muammo). MySQL/Laravel’ga ko‘chirish bekor qilindi — SQLite qoladi. Dizayn sayqallandi (10-bosqich).
