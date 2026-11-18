# 🇺🇿 LingoUz by Gulomov’s Academy — Mukammallashtirish va Rivojlantirish Rejasi (plan.md)

Ushbu hujjat **LingoUz** platformasini AQShda yashovchi 7–11 yoshli o‘zbek bolalari uchun jahon darajasidagi, **Registon va qadimiy O‘zbekiston me’morchiligi ruhidagi nufuzli milliy dizayn** hamda to‘liq **haqiqiy backend (Laravel + SQLite)** asosida rivojlantirishning bosqichma-bosqich yo‘l xaritasidir.

---

## 🏛️ 1. Dizayn Falsafasi: «Qadimiy Registon & Zamonamiy Ta’lim»

1. **Registon va Samarqand Koshinlari (Majolica Azure & Gold):**
   - Moviy gumbazlar va Registon maydonining lojuvard/feruza koshinlari (`#0284c7`, `#0c4a6e`, `#0ea5e9`).
   - Tillakori madrasasining tillarang-oltin va Buxoro g‘ishtin qadimiy minoralari koloritlari (`#d97706`, `#b45309`, `#f59e0b`).
   - Peshtaq va gumbazli arkali kartochkalar (`border-radius: 32px 32px 16px 16px` va moviy arkali hoshiyalar).
2. **Professional Vektorli SVG Ikonkalar (No Emojis):**
   - Barcha sahifalardagi nomutanosib emojilar butunlay chiqarib tashlandi.
   - O‘rniga aniq, zamonaviy, professional SVG vektor ikonkalar tizimi (`src/icons.php` -> `icon(...)`) joriy qilindi.
3. **Milliy Maskot — «Lingo»:**
   - Chust do‘ppisini kiygan yoqimli do‘st SVG formatida platformaga integratsiya qilindi. Bolaga darslarda hamrohlik qiladi: *«Salom do‘stim! Men Lingoman. Birga o‘zbekcha o‘rganamiz!»*

---

## ⚙️ 2. Haqiqiy Backend Arxitekturasi (No GET Hacks)

1. **To‘liq Xavfsiz Autentifikatsiya (POST-Only):**
   - Tizimda soxta yoki g‘alati `GET` orqali login qilish (`?as=...`) butunlay olib tashlandi.
   - Barcha harakatlar qat’iy `POST`, CSRF-token tekshiruvi, sessiya regeneratsiyasi va `password_hash` / `password_verify` orqali ishlaydi.
   - `/login` sahifasida faqat test uchun qulay ma’lumot beruvchi chiroyli kartochka joylashtirilgan bo‘lib, u formadagi maydonlarni to‘ldirib, haqiqiy `POST` so‘rov yuboradi.
2. **Foydalanuvchi Profili Boshqaruvi (`/profile`):**
   - O‘quvchi va ota-onalar o‘z ismini, yoshini, darajasini, vaqt zonasini tahrirlashi hamda parolini xavfsiz yangilashi mumkin.
3. **Administrator Boshqaruvi (`/admin`):**
   - Ota-onalarning arizalarini to‘g‘ridan-to‘g‘ri boshqarish (Holat: *Kutilmoqda*, *Bog‘lanildi*, *Tasdiqlandi*, *Rad etildi* — `POST /admin/enrollments/status`).
   - Manual Override: O‘quvchilarga istalgan darsni majburiy ochib berish (`POST /admin/unlock`).
   - CSV Eksport: Arizalarni bir marta bosish bilan yuklab olish.
4. **80% Content Locking Qoidasi:**
   - Server darajasida qat’iy tekshiruv: o‘quvchi dars testidan kamida 80% to‘plamasa, keyingi dars URL orqali ham ochilmaydi.

---

## 🚀 Bosqichma-Bosqich Yo‘l Xaritasi

### 1-BOSQICH: Registon & Milliy Me’morchilik Dizayni [BAJARILDI ✅]
- [x] Registon va Samarqand moviy koshinlari, Buxoro oltin-zar naqshlari uslubidagi dizayn yaratildi.
- [x] Peshtaq arkali kartochkalar, lojuvard foni va naqshlar tatbiq etildi.
- [x] Barcha emojilar professional SVG vektor ikonkalariga (`src/icons.php`) almashtirildi.

### 2-BOSQICH: Haqiqiy Backend & Autentifikatsiya [BAJARILDI ✅]
- [x] Soxta GET so‘rovlar va pastdagi sinov paneli to‘liq o‘chirildi.
- [x] Haqiqiy POST login, ro‘yxatdan o‘tish va sessiya xavfsizligi ta’minlandi.
- [x] O‘quvchi profili sahifasi (`/profile`) va parolni yangilash funksiyasi qo‘shildi.
- [x] Administrator uchun arizalar holatini boshqarish (`POST /admin/enrollments/status`) ulandi.

### 3-BOSQICH: Interaktiv Dars Pleyeri va 3D Flip Lug‘at [BAJARILDI ✅]
- [x] 3D ag‘dariluvchi (Flip) fleshkartalar: old tomoni o‘zbekcha va ovozli, orqa tomoni inglizcha ma’no va misollar.
- [x] Web Audio API orqali zang, tanga va fanfara tovush effektlari.
- [x] Web Speech API orqali so‘zlarni toza o‘zbekcha talaffuz qilish.

### 4-BOSQICH: O‘yinlashtirish va Sertifikatlash [BAJARILDI ✅]
- [x] So‘zlarni moslashtirish mini-o‘yini va tangalar hisobi.
- [x] 80% test natijasi bo‘yicha konfetti va g‘alaba ekrani.
- [x] Rasmiy «O‘zbek Tili Bilimdoni Diplomi» generatori (`/certificate`).

### 5-BOSQICH: Ota-onalar Qo‘llanmasi va Xavfsizlik [BAJARILDI ✅]
- [x] Diasporadagi ota-onalarga 5 ta oltin tavsiya (`/parent-tips`).
- [x] COPPA standartlari bo‘yicha maxfiylik siyosati (`/privacy`).

### 6-BOSQICH: Moslashuvchan Header & Mobil Drawer Menyusi [BAJARILDI ✅]
- [x] Desktop va mobil qurilmalar uchun moslashuvchan (responsive) boshqaruv paneli yaratildi.
- [x] Admin yoki o‘quvchi hisobiga kirganda (masalan, "Gulomov Admin") menyu elementlari sig‘may qolishi to‘liq bartaraf etildi.
- [x] 1100px dan kichik ekranlar uchun qulay o‘ng tomondan ochiluvchi Off-canvas Drawer menyusi va fonni xiralashtiruvchi backdrop qo‘shildi.
- [x] Esc tugmasi yoki fonni bosganda yopilish hamda foydalanuvchi ismining qisqarishi (ellipsis) kiritildi.

---

## 📌 Server va Texnik Holat
* **URL:** https://lingo.vexa.uz
* **Loyiha yo‘li:** `/var/www/startups/lingo`
* **Arxitektura:** Laravel + SQLite (izolyatsiyalangan PHP-FPM poolida 5ms tezlikda ishlaydi).
* **Git:** Barcha o‘zgarishlar `main` branchida commit qilingan.
