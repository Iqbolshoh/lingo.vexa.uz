<div class="legal-page">
    <div class="container">
        <div class="legal-card">
            <span class="legal-tag">SAMARALI O‘RGANISH QO‘LLANMASI</span>
            <h1>O‘zbek tilini tez va zavq bilan o‘rganish</h1>
            <p class="legal-meta">Gulomov’s Academy metodikasi asosida tayyorlandi</p>

            <div class="legal-content">
                <h2 style="font-family:var(--font-heading);color:var(--ink);margin:6px 0 14px">Kattalar va sayyohlar uchun 5 maslahat</h2>
                <?php foreach ([
                    ['Kuniga 5 daqiqa, lekin har kuni', 'Bir soatlik dars haftada bir marta emas — har kuni bitta qisqa dars. Ketma-ket kunlar «olovi» sizni ushlab turadi.'],
                    ['Baland ovozda takrorlang', 'O‘qish bo‘limidagi har gapni bosing, tinglang va ovoz chiqarib qaytaring. Talaffuz faqat og‘izda shakllanadi.'],
                    ['Sayohatdan oldin «Sayohatchilar uchun» kursi', 'Bozor, taksi, choyxona va mehmonxona iboralarini 6 ta darsda o‘rganib oling — O‘zbekistonda darhol ishlatasiz.'],
                    ['Xatodan qo‘rqmang', 'Tezkor mashq baholanmaydi — bemalol xato qiling. O‘zbeklar chet elliklarning bir og‘iz o‘zbekchasidan ham juda xursand bo‘ladi!'],
                    ['Madaniyat bilan birga', '«Bilasizmi?» kartalari va «Ikki madaniyat ko‘prigi» kursi so‘zlarning ortidagi hayotni ko‘rsatadi — shunda ular esda qoladi.'],
                ] as $i => [$h, $p]): ?>
                    <div class="tip-box" style="background:#eff6ff; border-left:4px solid #0369a1; padding:16px 20px; border-radius:8px; margin-bottom:14px;">
                        <h3><?= $i + 1 ?>. <?= $h ?></h3>
                        <p><?= $p ?></p>
                    </div>
                <?php endforeach; ?>

                <h2 style="font-family:var(--font-heading);color:var(--ink);margin:26px 0 14px">Xorijdagi ota-onalar uchun 5 oltin qoida</h2>
                <div class="tip-box" style="background:#f0fdf4; border-left:4px solid #059669; padding:16px 20px; border-radius:8px; margin-bottom:20px;">
                    <h3>1. «Bitta ota-ona — bitta til» tamoyili (One Parent, One Language)</h3>
                    <p>Uyda doimiy ravishda o‘zbek tilida gapiring. Bola inglizcha javob qaytarsa ham, siz o‘zbekcha so‘rashda va qo‘llab-quvvatlashda davom eting. Uning xatosini kulgiga olmang, to‘g‘ri variantini mehr bilan takrorlang.</p>
                </div>

                <div class="tip-box" style="background:#fef3c7; border-left:4px solid #f59e0b; padding:16px 20px; border-radius:8px; margin-bottom:20px;">
                    <h3>2. Kuniga 10–15 daqiqa muntazamlik — haftada bir marta 2 soatdan yaxshiroq</h3>
                    <p>Bolaning miyasi qisqa va takrorlanuvchi tajribalarni oson o‘zlashtiradi. LingoUz mikro-darslarini har kuni 5 daqiqadan ko‘rib chiqish uzoq muddatda ajoyib natija beradi.</p>
                </div>

                <div class="tip-box" style="background:#eff6ff; border-left:4px solid #2563eb; padding:16px 20px; border-radius:8px; margin-bottom:20px;">
                    <h3>3. O‘zbekona ertaklar va multfilmlar</h3>
                    <p>Uyqudan oldin o‘zbek xalq ertaklarini birgalikda o‘qing yoki LingoUz audio lug‘atidagi yangi so‘zlarni kundalik buyumlarga bog‘lang (masalan, muzlatgichdagi olma, anor, sabzini birga sanash).</p>
                </div>

                <div class="tip-box" style="background:#faf5ff; border-left:4px solid #7c3aed; padding:16px 20px; border-radius:8px; margin-bottom:20px;">
                    <h3>4. Bobo va buvilar bilan muntazam video-qo‘ng‘iroqlar</h3>
                    <p>O‘zbekistondagi yaqinlar bilan haftalik suhbatlar bolada tilga bo‘lgan tabiiy ehtiyojni va faxr tuyg‘usini uyg‘otadi. LingoUzda o‘rgangan yangi so‘zlarini buvasiga aytib berish bola uchun eng katta faxr!</p>
                </div>

                <div class="tip-box" style="background:#fff1f2; border-left:4px solid #f43f5e; padding:16px 20px; border-radius:8px; margin-bottom:20px;">
                    <h3>5. LingoUz Zoom Live Speaking Room’da faol ishtirok</h3>
                    <p>Boshqa shahar va davlatlardagi tengdoshlari ham o‘zbekcha gapirayotganini ko‘rish bolaga kuchli motivatsiya beradi: «Men yolg‘iz emasman, bu til juda qiziq ekan!»</p>
                </div>
            </div>

            <div style="margin-top: 32px; display:flex; gap:12px; flex-wrap:wrap;">
                <a href="/dashboard" class="btn btn-primary"><?= icon('arrow-left', '', 16) ?> O‘quvchi paneliga qaytish</a>
                <a href="/" class="btn btn-outline"><?= icon('registon', '', 16) ?> Bosh sahifa</a>
            </div>
        </div>
    </div>
</div>
