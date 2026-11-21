<section class="hero-section registon-hero">
    <div class="registon-pattern-overlay"></div>
    <div class="container hero-inner">
        <div class="hero-content">
            <div class="hero-badge">
                <span class="badge-icon-wrap"><?= icon('registon', 'icon-registon-svg', 18) ?></span>
                <span><?= t('Bolalar, kattalar va sayyohlar — o‘zbek tilini o‘rganmoqchi bo‘lgan hamma uchun') ?></span>
            </div>
            <h1 class="hero-title">
                <?= t('O‘zbek tili — <span class="text-gradient-registon">sehrli sarguzashtga</span> aylanadi!') ?>
            </h1>
            <p class="hero-subtitle">
                <?= t('Samarqand va Buxoro ruhida yaratilgan zamonaviy onlayn maktab. Xorijda ulg‘ayayotgan farzandingiz, O‘zbekistonga sayohatga otlangan mehmon yoki tilni noldan boshlagan katta yoshli — har kim 3–5 daqiqalik darslar, ovozli mashqlar va jonli muloqot bilan o‘zbekcha gapira boshlaydi.') ?>
            </p>

            <div class="hero-cta-group">
                <a href="#ariza" class="btn btn-primary btn-lg pulse-hover">
                    <?= icon('sparkles', '', 20) ?> <?= t('Pilot dasturiga yozilish') ?>
                </a>
                <a href="/login" class="btn btn-secondary btn-lg">
                    <?= icon('play', '', 18) ?> <?= t('Darslarni boshlash') ?>
                </a>
            </div>

            <div class="hero-stats">
                <div class="stat-item">
                    <span class="stat-number"><?= t('3–5 daqiqa') ?></span>
                    <span class="stat-label"><?= t('Mikro-dars formati') ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?= t('80% qoidasi') ?></span>
                    <span class="stat-label"><?= t('Interaktiv test nazorati') ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">Zoom Live</span>
                    <span class="stat-label"><?= t('Ustoz bilan jonli suhbat') ?></span>
                </div>
            </div>
        </div>

        <div class="hero-visual">
            <div class="hero-card-stack">
                <div class="mascot-hero-wrap">
                    <?= mascot('welcome', 120, 'hero-owl') ?>
                    <div class="mascot-speech-bubble">
                        <?= t('«Assalomu alaykum! Men <strong>Lingo</strong>man — sizning o‘zbek tili do‘stingiz. Birga o‘rganamiz!»') ?>
                    </div>
                </div>

                <div class="hero-interactive-card registon-arch-card">
                    <div class="arch-top-ornament">
                        <?= icon('registon', 'arch-icon', 28) ?>
                    </div>
                    <div class="card-top-bar">
                        <span class="card-chip"><?= t('NAMUNAVIY SO‘Z') ?></span>
                        <span class="card-level"><?= t('A0 / Boshlang‘ich') ?></span>
                    </div>
                    <h3 class="card-uz-word" id="heroWord">Salom!</h3>
                    <p class="card-pronounce"><?= t('Talaffuz:') ?> <em>[Sah-lom]</em></p>
                    <p class="card-en-meaning">English: <strong>Hello / Hi</strong></p>
                    <div class="card-audio-action">
                        <button class="btn-voice" onclick="speakUzbek('Salom, xush kelibsiz!')" title="<?= t('Ovozni eshitish') ?>">
                            <?= icon('volume-2', '', 16) ?> <?= t('Ovozli eshitish') ?>
                        </button>
                    </div>
                    <div class="card-example">
                        «Salom, mening do‘stim!»
                    </div>
                </div>

                <div class="floating-badge badge-trophy">
                    <?= icon('trophy', '', 18) ?> <span><?= t('80%+ ball — keyingi dars ochildi!') ?></span>
                </div>
                <div class="floating-badge badge-zoom">
                    <?= icon('video', '', 18) ?> <span><?= t('Jonli Zoom • Toshkent, Yevropa, AQSh vaqtida') ?></span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Muammo va Yechim -->
<!-- Kimlar uchun -->
<section class="section" id="kimlar-uchun">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-tag"><?= t('KIMLAR UCHUN?') ?></span>
            <h2 class="section-title"><?= t('O‘zbek tilini o‘rganmoqchi bo‘lgan hamma uchun') ?></h2>
            <p class="section-desc"><?= t('Uzbek for everyone — kids, adults and travellers. Har bir so‘z ovoz va inglizcha tarjima bilan.') ?></p>
        </div>
        <div class="feature-trio">
            <?php foreach ([
                ['users', 'tone-lapis', t('Xorijdagi oilalar'), t('AQSh, Yevropa, Koreya yoki Rossiyada ulg‘ayayotgan farzandlar ona tilini o‘yin orqali o‘rganadi va buvisi bilan o‘zbekcha gaplashadi.')],
                ['map-pin', 'tone-emerald', t('Sayyohlar'), t('Samarqand, Buxoro, Xivaga sayohatdan oldin: bozorda savdolashish, taksi, choyxona va mehmonxona iboralari.')],
                ['book-open', 'tone-gold', t('Kattalar'), t('Ishi, oilasi yoki qiziqishi uchun o‘zbek tilini noldan boshlayotganlar — kuniga 5 daqiqa yetarli.')],
                ['heart', 'tone-ruby', t('Madaniyat ixlosmandlari'), t('Osh, non, Navro‘z, mehmondo‘stlik va qadimiy shaharlar — til bilan birga madaniyatni ham his qilasiz.')],
            ] as [$ico, $tone, $h, $p]): ?>
                <div class="feature-box <?= $tone ?>">
                    <span class="stat-ico"><?= icon($ico, '', 22) ?></span>
                    <h3><?= $h ?></h3>
                    <p><?= $p ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section-problem-solution" id="muammo">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-tag"><?= t('MUAMMO VA YECHIM') ?></span>
            <h2 class="section-title"><?= t('Nega o‘zbek tilini o‘rganish qiyin tuyuladi?') ?></h2>
            <p class="section-desc"><?= t('Bolalar, kattalar va sayyohlar uchun bir xil qulay, qisqa va qiziqarli ta’lim modeli') ?></p>
        </div>

        <div class="problem-solution-grid">
            <div class="card-problem">
                <div class="card-icon red-icon">
                    <?= icon('shield-check', '', 32) ?>
                </div>
                <h3><?= t('Eski an’anaviy usul') ?></h3>
                <ul class="comparison-list">
                    <li><?= t('40–60 daqiqalik zerikarli grammatika qoidalari') ?></li>
                    <li><?= t('O‘zbek tili uchun sifatli onlayn materiallar juda kam') ?></li>
                    <li><?= t('Darslikdagi so‘zlar bozorda, taksida, mehmonda ishlatilmaydi') ?></li>
                    <li><?= t('O‘rganuvchi o‘z rivojlanishini ko‘ra olmaydi') ?></li>
                </ul>
            </div>

            <div class="card-solution">
                <div class="card-icon green-icon">
                    <?= icon('check-circle', '', 32) ?>
                </div>
                <h3><?= t('LingoUz zamonaviy yechimi') ?></h3>
                <ul class="comparison-list">
                    <li><?= t('<strong>3–5 daqiqalik mikro-darslar:</strong> animatsiya va rangli tasvirlar') ?></li>
                    <li><?= t('<strong>O‘yinlashtirilgan mashqlar:</strong> so‘zlarni moslashtirish va flesh-kartalar') ?></li>
                    <li><?= t('<strong>80% Content Locking:</strong> mavzuni o‘zlashtirmay keyingi darsga o‘tmaydi') ?></li>
                    <li><?= t('<strong>Hayotiy mavzular:</strong> oila, bozor, choyxona, sayohat, bayramlar va madaniyat') ?></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Metodika: 4 qadamli o'quv sikli -->
<section class="section section-methodology" id="metodika">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-tag"><?= t('TA’LIM METODIKASI') ?></span>
            <h2 class="section-title"><?= t('Darslar qanday tizimda o‘tiladi?') ?></h2>
            <p class="section-desc"><?= t('Har bir dars — qisqa, qiziqarli va o‘lchanadigan bosqichlar zanjiri. O‘ynab o‘rganasiz, natijangizni har kuni ko‘rasiz.') ?></p>
        </div>

        <div class="feature-trio">
            <?php foreach ([
                ['video', 'tone-lapis', t('Mikro-sahna'), t('3–5 daqiqalik qisqa sahna: Lingo iboralarni aytadi, siz takrorlaysiz.')],
                ['volume-2', 'tone-gold', t('Ovozli lug‘at'), t('Ag‘dariluvchi kartalar, talaffuz va misol gaplar — har biri ovoz bilan.')],
                ['message-square', 'tone-emerald', t('O‘qish + «Bilasizmi?»'), t('Qisqa hikoya gapma-gap o‘qiladi, yonida o‘zbek madaniyati haqida qiziq fakt.')],
                ['coins', 'tone-ruby', t('O‘yin va tangalar'), t('Juftlik o‘yini, tangalar, XP, ketma-ket kunlar olovi va nishonlar.')],
                ['layers', 'tone-lapis', t('Englify uslubidagi mashq'), t('Tinglab top, so‘z bloklaridan gap tuz, o‘zbekcha yoz — darhol javob bilan.')],
                ['trophy', 'tone-gold', t('80% test va diplom'), t('Testdan 80%+ — keyingi dars ochiladi. Kurs oxirida diplom chop etiladi.')],
                ['video', 'tone-emerald', t('Haftalik Zoom'), t('Ustoz bilan jonli suhbat; vaqt Toshkent, Yevropa va AQSh bo‘yicha avtomatik ko‘rsatiladi.')],
                ['shield-check', 'tone-ruby', t('Shaxsiy kuzatuv'), t('Har bir dars natijasi va o‘zlashtirish foizi sizga ham, ustozga ham, ota-onaga ham ko‘rinadi.')],
            ] as [$ico, $tone, $h, $p]): ?>
                <div class="feature-box <?= $tone ?>">
                    <span class="stat-ico"><?= icon($ico, '', 22) ?></span>
                    <h3><?= $h ?></h3>
                    <p><?= $p ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Kurslar -->
<section class="section section-lessons" id="darslar">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-tag"><?= t('KURSLAR') ?></span>
            <h2 class="section-title"><?= t('Nimalarni o‘rganasiz?') ?></h2>
            <p class="section-desc"><?= t('Hozircha :lessons ta dars, :words ta so‘z va :exercises ta mashq.', ['lessons' => $stats['lessons'], 'words' => $stats['words'], 'exercises' => $stats['exercises']]) ?> <?= t('Ustozlar har hafta yangi darslar qo‘shib boradi.') ?></p>
        </div>

        <div class="course-grid">
            <?php foreach ($courses as $c): ?>
                <div class="course-card theme-<?= e($c['theme']) ?>">
                    <div class="course-cover">
                        <div class="cover-tags"><span class="tag"><?= e($c['level']) ?></span><span class="tag"><?= count($c['lessons']) ?> <?= t('dars') ?></span></div>
                        <div class="cover-ico"><?= icon($c['icon'], '', 26) ?></div>
                        <h3><?= e(loc($c, 'title')) ?></h3>
                        <?php if (!empty($c['subtitle'])): ?><p><?= e(loc($c, 'subtitle')) ?></p><?php endif; ?>
                    </div>
                    <div class="course-body">
                        <ul class="lock-list" style="margin-top:0">
                            <?php foreach (array_slice($c['lessons'], 0, 6) as $l): ?>
                                <li><?= icon($l['icon'], '', 16) ?> <span style="flex:1"><?= e(loc($l, 'title')) ?></span> <span class="small muted"><?= (int) $l['duration_min'] ?> <?= t('daq') ?></span></li>
                            <?php endforeach; ?>
                        </ul>
                        <a href="/register" class="btn btn-primary btn-block"><?= icon('play', '', 16) ?> <?= t('Bepul boshlash') ?></a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if ($culture): ?>
<!-- Madaniyat burchagi -->
<section class="section" id="madaniyat">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-tag"><?= t('MADANIYAT BURCHAGI') ?></span>
            <h2 class="section-title"><?= t('Ildizlarini faxr bilan o‘rganadi') ?></h2>
            <p class="section-desc"><?= t('Osh nega YUNESKO merosi? Nonni nega teskari qo‘ymaymiz? Navro‘z va Thanksgiving qanchalik o‘xshash? Ikki madaniyat — bitta ko‘prik.') ?></p>
        </div>
        <div class="culture-strip">
            <?php foreach ($culture as $l): ?>
                <div class="culture-tile">
                    <div class="ct-ico"><?= icon($l['icon'], '', 24) ?></div>
                    <h3><?= e(is_en() ? $l['title_en'] : $l['title']) ?></h3>
                    <div class="ct-en"><?= e(is_en() ? $l['title'] : $l['title_en']) ?></div>
                    <p><?= e(loc($l, 'culture_title') ? loc($l, 'culture_title') . ' — ' : '') ?><?= e(mb_strimwidth(loc($l, 'culture_note'), 0, 120, '…')) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Interaktiv namunaviy lug'at -->
<section class="section section-demo-interactive">
    <div class="container">
        <div class="demo-box registon-mosaic-box">
            <div class="demo-header">
                <span class="badge-pill"><?= icon('volume-2', '', 14) ?> <?= t('TALAFUZ SINOVI') ?></span>
                <h2><?= t('So‘zlarni ovozli eshitib ko‘ring!') ?></h2>
                <p><?= t('Quyidagi kartochkalarni bosing va o‘zbekcha talaffuzni tinglang:') ?></p>
            </div>

            <div class="vocab-interactive-grid">
                <?php foreach ($sampleVocab as $v): ?>
                    <div class="vocab-demo-card" data-speak="<?= e($v['word_uz']) ?>" role="button" tabindex="0">
                        <div class="vocab-word"><?= e($v['word_uz']) ?></div>
                        <div class="vocab-phonetic">[<?= e($v['pronunciation']) ?>]</div>
                        <div class="vocab-en"><?= e($v['word_en']) ?></div>
                        <button class="btn-play-voice" type="button"><?= icon('volume-2', '', 14) ?> <?= t('Tinglash') ?></button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- 9 Haftalik Pilot Rejasi -->
<section class="section section-pilot-info">
    <div class="container">
        <div class="pilot-summary-card">
            <div class="pilot-header">
                <div>
                    <span class="section-tag"><?= t('PILOT LOYIHA') ?></span>
                    <h2 class="section-title"><?= t('9 Haftalik Sinov Bosqichi (0 so‘m)') ?></h2>
                </div>
                <div class="pilot-badge-target">
                    <?= icon('users', '', 16) ?> <?= t('Butun dunyodan o‘quvchilar') ?>
                </div>
            </div>

            <div class="pilot-table-wrapper">
                <table class="pilot-table">
                    <thead>
                        <tr>
                            <th><?= t('Element') ?></th>
                            <th><?= t('Tavsif va Shartlar') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= t('<strong>Maqsadli guruh</strong>') ?></td>
                            <td><?= t('Xorijdagi o‘zbek oilalari farzandlari, O‘zbekistonga keladigan sayyohlar, o‘zbek tilini o‘rganayotgan kattalar') ?></td>
                        </tr>
                        <tr>
                            <td><?= t('<strong>O‘quv kontenti</strong>') ?></td>
                            <td><?= t('3 ta kurs: «Ilk qadamlar», «Ikki madaniyat ko‘prigi», «Sayohatchilar uchun» — ovozli lug‘at, o‘qish, mashqlar, 80% testlar va Zoom suhbat') ?></td>
                        </tr>
                        <tr>
                            <td><?= t('<strong>Pilot qiymati</strong>') ?></td>
                            <td><?= t('<strong>0 so‘m (Mutlaqo bepul)</strong> — MVP sinovi va o‘quvchilar fikrini o‘rganish uchun') ?></td>
                        </tr>
                        <tr>
                            <td><?= t('<strong>Xavfsizlik (COPPA)</strong>') ?></td>
                            <td><?= t('Ortiqcha shaxsiy ma’lumotlar yig‘ilmaydi; 13 yoshgacha bolalar faqat ota-ona roziligi bilan qatnashadi') ?></td>
                        </tr>
                        <tr>
                            <td><?= t('<strong>Vaqt zonalari</strong>') ?></td>
                            <td><?= t('Toshkent, Yevropa va AQSh vaqtlari — jadval har bir o‘quvchiga o‘z vaqtida ko‘rsatiladi') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Ota-onalar uchun -->
<section class="section section-faq" id="faq">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-tag"><?= t('SAVOL-JAVOBLAR') ?></span>
            <h2 class="section-title">Ko‘p beriladigan savollar</h2>
        </div>

        <div class="faq-list">
            <details class="faq-item">
                <summary class="faq-question">
                    <span><?= t('Men o‘zbek tilini umuman bilmayman. Kurs mos keladimi?') ?></span>
                    <span class="faq-toggle">+</span>
                </summary>
                <div class="faq-answer">
                    <?= t('<p>Ha, albatta! Kurslar A0 darajasidan — noldan boshlanadi. Har bir so‘z ovoz, talaffuz va inglizcha tarjima bilan beriladi. Bolalar ham, kattalar ham, O‘zbekistonga sayohatga kelayotgan xorijliklar ham bemalol o‘rganadi.</p>') ?>
                </div>
            </details>

            <details class="faq-item">
                <summary class="faq-question">
                    <span><?= t('80% content locking qoidasi nima degani?') ?></span>
                    <span class="faq-toggle">+</span>
                </summary>
                <div class="faq-answer">
                    <?= t('<p>Har bir dars oxirida 5–7 ta qiziqarli savol bo‘ladi: tinglab topish, gap tuzish, so‘z yozish va variant tanlash. Kamida 80% to‘planmaguncha keyingi dars ochilmaydi. Agar 80% dan kam to‘plasa, tizim: «Yana bir bor urinib ko‘r!» deb darsni qayta o‘rganishni taklif qiladi.</p>') ?>
                </div>
            </details>

            <details class="faq-item">
                <summary class="faq-question">
                    <span><?= t('Darslar uchun qanday qurilma kerak?') ?></span>
                    <span class="faq-toggle">+</span>
                </summary>
                <div class="faq-answer">
                    <?= t('<p>Istalgan planshet (iPad), noutbuk yoki smartfon yetarli. Hech qanday murakkab dastur o‘rnatish shart emas, barchasi brauzer orqali ishlaydi.</p>') ?>
                </div>
            </details>

            <details class="faq-item">
                <summary class="faq-question">
                    <span><?= t('Jonli Zoom darslari qachon bo‘ladi?') ?></span>
                    <span class="faq-toggle">+</span>
                </summary>
                <div class="faq-answer">
                    <?= t('<p>Ustoz jadvalni oldindan e’lon qiladi va vaqt Toshkent, Yevropa va AQSh bo‘yicha ko‘rsatiladi. Darsda o‘tilgan mavzular bo‘yicha qisqa o‘yinlar va suhbatlar bo‘ladi.</p>') ?>
                </div>
            </details>

            <details class="faq-item">
                <summary class="faq-question">
                    <span><?= t('Bolaning shaxsiy ma’lumotlari qanchalik xavfsiz?') ?></span>
                    <span class="faq-toggle">+</span>
                </summary>
                <div class="faq-answer">
                    <?= t('<p>Biz AQShning COPPA (Children\'s Online Privacy Protection Act) qoidalariga rioya qilamiz. Bolaning familiyasi, uy manzili yoki telefon raqami talab qilinmaydi. Profil uchun taxallus (nickname) yetarli.</p>') ?>
                </div>
            </details>
        </div>
    </div>
</section>

<!-- Ariza topshirish formasi -->
<section class="section section-enrollment" id="ariza">
    <div class="container">
        <div class="enrollment-card registon-portal-card">
            <div class="enrollment-header text-center">
                <span class="section-tag tag-white"><?= t('BEPUL PILOTGA QO‘SHILISH') ?></span>
                <h2 class="title-white"><?= t('LingoUz pilotiga qo‘shiling!') ?></h2>
                <p class="desc-white"><?= t('O‘zingiz yoki farzandingiz uchun ariza qoldiring va o‘zbek tilini bepul o‘rganishni boshlang.') ?></p>
            </div>

            <form action="/enroll" method="POST" class="enrollment-form">
                <?= csrf_field() ?>

                <div class="form-row">
                    <div class="form-group">
                        <label for="parent_name"><?= t('Ismingiz (ariza beruvchi) *') ?></label>
                        <input type="text" id="parent_name" name="parent_name" required placeholder="<?= t('Masalan: Dilshod Qodirov') ?>">
                    </div>

                    <div class="form-group">
                        <label for="parent_email"><?= t('Elektron pochta (Email) *') ?></label>
                        <input type="email" id="parent_email" name="parent_email" required placeholder="example@gmail.com">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="parent_phone"><?= t('Telefon / Telegram (bog‘lanish uchun)') ?></label>
                        <input type="text" id="parent_phone" name="parent_phone" placeholder="+1 (555) 000-0000">
                    </div>

                    <div class="form-group">
                        <label for="child_name"><?= t('O‘quvchi ismi (o‘zingiz yoki farzandingiz) *') ?></label>
                        <input type="text" id="child_name" name="child_name" required placeholder="<?= t('Masalan: Jasurbek yoki Emily') ?>">
                    </div>
                </div>

                <div class="form-row three-cols">
                    <div class="form-group">
                        <label for="child_age"><?= t('O‘quvchining yoshi *') ?></label>
                        <select id="child_age" name="child_age">
                            <?php foreach (AGE_LABELS as $ageKey => $ageLabel): ?><option value="<?= $ageKey ?>" <?= $ageKey === '18+' ? 'selected' : '' ?>><?= t($ageLabel) ?></option><?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="language_level"><?= t('O‘zbek tili darajasi *') ?></label>
                        <select id="language_level" name="language_level">
                            <option value="A0"><?= t('A0 — Kam tushunadi yoki gapirmaydi') ?></option>
                            <option value="A1" selected><?= t('A1 — Tushunadi, lekin erkin gapirishda qiynaladi') ?></option>
                            <option value="A2"><?= t('A2 — Gapiradi, o‘qish/yozishni o‘rganmoqchi') ?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="timezone"><?= t('Vaqt zonasi *') ?></label>
                        <select id="timezone" name="timezone">
                            <?php foreach (TIMEZONES as $tzKey => $tzLabel): ?><option value="<?= $tzKey ?>"><?= t($tzLabel) ?></option><?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="notes"><?= t('Maqsadingiz yoki istaklaringiz:') ?></label>
                    <textarea id="notes" name="notes" rows="2" placeholder="<?= t('Masalan: bahorda Samarqandga sayohat qilmoqchiman / farzandim buvisi bilan gaplashsin...') ?>"></textarea>
                </div>

                <div class="card-option-box">
                    <label class="checkbox-label">
                        <input type="checkbox" name="create_account" value="1" checked id="createAccountCheck" onchange="togglePasswordField()">
                        <span><?= t('<strong>Hoziroq bepul o‘quvchi profili yaratilsin</strong> (darhol darslarni boshlash uchun)') ?></span>
                    </label>

                    <div class="form-group" id="passwordGroup" style="margin-top: 12px;">
                        <label for="password"><?= t('Profil uchun parol yarating (kamida 6 ta belgi):') ?></label>
                        <input type="password" id="password" name="password" minlength="6" placeholder="<?= t('Parol kiriting') ?>" autocomplete="new-password">
                    </div>
                </div>

                <div class="form-group consent-box">
                    <label class="checkbox-label">
                        <input type="checkbox" name="consent" value="1" required checked>
                        <span><?= t('<a href="/privacy" target="_blank" style="text-decoration:underline; color:#fff;">Maxfiylik siyosatiga</a> roziman. Agar farzandim uchun yozayotgan bo‘lsam, men uning ota-onasi yoki qonuniy vasiysiman.') ?></span>
                    </label>
                </div>

                <button type="submit" class="btn btn-accent btn-xl btn-block">
                    <?= icon('arrow-right', '', 20) ?> <?= t('Bepul Pilotga Yozilish va Boshlash') ?>
                </button>
            </form>
        </div>
    </div>
</section>
