# 🇺🇿 LingoUz by Gulomov’s Academy — Mukammallashtirish va Rivojlantirish Rejasi (plan.md)

Ushbu reja **LingoUz** platformasini AQShda yashovchi 7–11 yoshli o‘zbek bolalari uchun jahon darajasidagi (Duolingo / Khan Academy Kids uslubidagi), ammo to‘liq **o‘zbekona milliy ruhda va «chotkiy» dizaynda** rivojlantirishning bosqichma-bosqich yo‘l xaritasidir.

---

## 🎨 Asosiy G‘oya va Vizual Konseptsiya: «O‘zbekona Sarguzasht»

1. **Milliy Maskot — «Zukko» (Do‘ppili do‘st):**
   - Chust do‘ppisini kiygan yoqimli, aqlli maskot. Bolaga har bir darsda hamrohlik qiladi, yutuqlarda quvonadi, xato qilganda qo‘llab-quvvatlaydi.
2. **Milliy Vizual Kolorit:**
   - **Zumrad Yashil & Moviy Osmon:** O‘zbekiston tabiati va Registon moviy koshinlari ranglari.
   - **Tillarang Zarchopon (Oltin sariq):** Yutuqlar, tangalar va nishonlar uchun.
   - **Anor Qizili (Ruby Red):** Jonli muloqot va e’tibor qaratiladigan elementlar.
   - **Bodomgul va Atlas Naqshlari:** Orqa fonlarda nozik milliy geometrik ornamentlar.
3. **Jonli Ovoz va Tovush Effektlari (Sound Effects):**
   - Web Audio API orqali sintez qilingan bolalarga yoqadigan tovushlar:
     - 🪙 Tanga yig‘ish ovozi (*Coin pickup*)
     - ✨ Kartochka to‘g‘ri topilganda jarangdor zang (*Success chime*)
     - 🎺 Testni 80%+ bilan topshirganda tantanali g‘alaba sadosi (*Fanfare*)
   - Har bir so‘zning tiniq o‘zbekcha talaffuzi (Audio + Web Speech).

---

## 🚀 Bosqichma-Bosqich Amalga Oshirish Rejasi

### ✅ 1-BOSQICH: O‘zbekona «Chotkiy» Dizayn Tizimi va Milliy Maskot
- [x] Saytning umumiy CSS/UI dizaynini yangilash (Do‘ppi, Atlas to‘lqinlari, milliy bayroq va naqshlar uyg‘unligi).
- [x] Bosh sahifa (Hero), Navbar va Footer’ni boyitilgan milliy koloritga o‘tkazish.
- [x] «Zukko» maskotini SVG formatida sahifalarga integratsiya qilish (Salomlashayotgan, kitob o‘qiyotgan, g‘alaba qozongan holatlar).
- [x] Foydalanuvchi interfeysiga yumshoq 3D kartochkalar, jilvador gradientlar va bolalar uchun qulay yirik tugmalar qo‘shish.

### ✅ 2-BOSQICH: Interaktiv Ovozli Pleyer va Ag‘dariluvchi Fleshkartalar (Flip Cards)
- [x] Vizual lug‘at kartochkalariga **3D Flip (Ag‘darilish)** effektini qo‘shish:
  - Old tomoni: Rasm, o‘zbekcha so‘z, talaffuz tugmasi.
  - Orqa tomoni: Inglizcha tarjimasi, misol jumla va o‘zbekona mini-tushuntirish.
- [x] **Web Audio API Tovush Effektlari:** Hech qanday og‘ir fayllarsiz to‘g‘ridan-to‘g‘ri brauzerda sintez qilinadigan tovushlar (Click, Ding, Coin, Fanfare).
- [x] Ovozni o‘chirib-yoqish (Sound Effects Toggle) boshqaruvi.

### ✅ 3-BOSQICH: O‘yinlashtirish Tizimi (Gamification — Tangalar va Streak)
- [x] **«Zukko Tangalari» (Lingo Coins 🪙):** Har bir o‘rganilgan so‘z va topshirilgan test uchun tangalar berish.
- [x] **Kunlik Faollik (Streak 🔥):** O‘quvchining har kungi darsga kirishini rag‘batlantirish.
- [x] So‘zlarni moslashtirish o‘yiniga animatsion ball va tanga berish logikasini ulash.

### ✅ 4-BOSQICH: Rasmiy Sertifikat / Diplom Generatori (Certificate)
- [x] 5 ta darsni muvaffaqiyatli yakunlagan bolalar uchun rasmiy nomlangan **«O‘zbek Tili Bilimdoni Diplomi»** sahifasini yaratish.
- [x] Chop etish (Print / PDF) va ijtimoiy tarmoqda ota-onalar faxr bilan bo‘lishishi uchun tayyor dizayn.

### ✅ 5-BOSQICH: Ota-onalar uchun Rivojlanish Hisoboti (Parent Progress)
- [x] Bolaning qaysi darsda qancha so‘z yodlaganini ko‘rsatuvchi tushunarli infografika.
- [x] Ota-onalarga farzandi bilan uyda o‘zbekcha muloqot qilish bo‘yicha amaliy 5 ta tavsiya (Tips for Diaspora Parents).

### ✅ 6-BOSQICH: Testing, Xavfsizlik va Git Versiyalash
- [x] `student-app fix-perms lingo` orqali SQLite va Linux huquqlarini tekshirish.
- [x] Barcha marshrutlarni (Desktop & Mobile) test qilish.
- [x] Git’ga toza commit qilish va GitHub’ga ulashga tayyorlash.

---

## 📌 Hozirgi Holat
- **Faol bosqich:** 1- va 2-bosqichlar to‘liq tatbiq qilinmoqda.
- **Domen:** https://lingo.vexa.uz
