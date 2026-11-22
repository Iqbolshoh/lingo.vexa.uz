<?php
$wordListJson = json_encode(array_values(array_map(function ($w) {
    return [
        'id' => (int) $w['id'],
        'uz' => $w['word_uz'],
        'en' => $w['word_en'],
        'pron' => $w['pronunciation'] ?? '',
        'example' => $w['example'] ?? '',
        'course' => $w['course_title'] ?? '',
        'lesson' => $w['lesson_title'] ?? '',
    ];
}, $words)), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
?>
<div class="pg flashcards-pg">
    <div class="container">
        <section class="pg-hero theme-teal">
            <div class="hero-split">
                <div>
                    <div class="crumbs">
                        <a href="/"><?= t('Bosh sahifa') ?></a> <span>/</span>
                        <?php if ($currentUser): ?><a href="/dashboard"><?= t('Mening sahifam') ?></a> <span>/</span><?php endif; ?>
                        <span><?= t('Aqlli Flashcards') ?></span>
                    </div>
                    <h1><?= icon('layers', '', 32) ?> <?= t('Aqlli Flashcards Trenajyori') ?></h1>
                    <p><?= t('Bo‘sh vaqtingizda so‘zlarni 3D kartochkalar yordamida tezkor takrorlang, talaffuzingizni tekshiring va har bir o‘zlashtirilgan so‘z uchun +2 XP oling!') ?></p>
                    <div class="xp-strip" style="margin-top:14px">
                        <span class="xp-chip" style="background:#ccfbf1;color:#115e59;border-color:#5eead4">
                            <?= icon('book-open', '', 18) ?>
                            <strong id="cardTotalChip"><?= count($words) ?></strong> <?= t('ta so‘z') ?>
                        </span>
                        <span class="xp-chip" style="background:#dcfce7;color:#166534;border-color:#86efac">
                            <?= icon('check-circle', '', 18) ?>
                            <span id="cardMasteredCount">0</span> <?= t('o‘zlashtirildi') ?>
                        </span>
                        <span class="xp-chip" style="background:#fef3c7;color:#92400e;border-color:#fcd34d">
                            <?= icon('star', '', 18) ?>
                            <span id="cardXpEarned">0</span> XP <?= t('yig‘ildi') ?>
                        </span>
                    </div>
                </div>
                <div class="hero-owl-ring">
                    <?= mascot('learning', 115) ?>
                </div>
            </div>
        </section>

        <!-- Course Filter Pills -->
        <div class="flashcards-filter-bar">
            <span class="filter-label"><?= icon('filter', '', 16) ?> <?= t('Mavzuni tanlang:') ?></span>
            <div class="filter-pills">
                <a href="/flashcards" class="pill-btn <?= $selectedCourse === 0 ? 'is-active' : '' ?>">
                    <?= t('Barcha so‘zlar') ?> <small>(<?= count($words) ?>)</small>
                </a>
                <?php foreach ($courses as $c): ?>
                    <a href="/flashcards?course=<?= (int) $c['id'] ?>" class="pill-btn <?= $selectedCourse === (int) $c['id'] ? 'is-active' : '' ?>">
                        <?= e(loc($c, 'title')) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if (empty($words)): ?>
            <div class="card empty-flashcards">
                <?= mascot('sleeping', 100) ?>
                <h3><?= t('Bu kursda hozircha so‘zlar mavjud emas') ?></h3>
                <p><?= t('Boshqa mavzuni tanlang yoki yangi darslarni boshlang!') ?></p>
                <a href="/flashcards" class="btn btn-primary"><?= t('Barcha so‘zlarni ko‘rish') ?></a>
            </div>
        <?php else: ?>
            <!-- Interactive Workout Section -->
            <div class="flashcards-workout" id="flashcardsApp">
                <!-- Progress Header -->
                <div class="flashcards-progress-bar-wrap">
                    <div class="progress-info">
                        <span class="progress-step-text" id="progressStepText">1 / <?= count($words) ?></span>
                        <span class="progress-status-badge" id="progressPercentage">0%</span>
                    </div>
                    <div class="fc-progress-track">
                        <div class="fc-progress-fill" id="progressFillBar" style="width: 0%"></div>
                    </div>
                </div>

                <!-- 3D Flip Card Container -->
                <div class="fc-card-stage">
                    <div class="fc-flip-card" id="flipCard" tabindex="0" role="button" aria-label="<?= t('Kartani aylantirish') ?>">
                        <!-- FRONT SIDE -->
                        <div class="fc-card-face fc-card-front">
                            <div class="fc-face-header">
                                <span class="fc-theme-badge" id="cardCourseTag">1-kurs</span>
                                <span class="fc-side-label"><?= icon('rotate-cw', '', 14) ?> <?= t('O‘zbekcha') ?></span>
                            </div>
                            <div class="fc-face-body">
                                <div class="fc-word-uz" id="frontWordUz">Assalomu alaykum</div>
                                <div class="fc-pronunciation" id="frontPron">[a-ssah-lohm-oo ah-lay-koom]</div>

                                <!-- Audio & Mic Action Buttons -->
                                <div class="fc-audio-actions">
                                    <button type="button" class="fc-audio-btn" id="fcListenBtn" title="<?= t('Talaffuzni tinglash') ?>">
                                        <?= icon('volume-2', '', 20) ?>
                                        <span><?= t('Tinglash') ?></span>
                                    </button>
                                    <button type="button" class="fc-mic-btn" id="fcMicBtn" title="<?= t('Mikrofon orqali talaffuzingizni tekshiring') ?>">
                                        <?= icon('mic', '', 20) ?>
                                        <span><?= t('Talaffuzni tekshirish') ?></span>
                                    </button>
                                </div>
                                <div class="fc-mic-feedback" id="fcMicFeedback" style="display:none"></div>
                            </div>
                            <div class="fc-face-footer">
                                <span class="fc-hint-text">
                                    <kbd>Space</kbd> <?= t('yoki kartani bosing — tarjimani ko‘rish') ?>
                                </span>
                            </div>
                        </div>

                        <!-- BACK SIDE -->
                        <div class="fc-card-face fc-card-back">
                            <div class="fc-face-header">
                                <span class="fc-theme-badge back-tag"><?= icon('check', '', 14) ?> <?= t('Tarjimasi va Namunasi') ?></span>
                                <span class="fc-side-label"><?= icon('rotate-cw', '', 14) ?> <?= t('Inglizcha') ?></span>
                            </div>
                            <div class="fc-face-body">
                                <div class="fc-word-en" id="backWordEn">Hello / Peace be upon you</div>
                                <div class="fc-example-box" id="backExampleBox">
                                    <span class="example-label"><?= t('Misol:') ?></span>
                                    <p class="example-text" id="backExampleText">«Assalomu alaykum, ustoz! Bugungi dars boshlandimi?»</p>
                                </div>
                                <div class="fc-lesson-source" id="backLessonSource">
                                    <?= icon('book-open', '', 14) ?> <span id="backLessonTitle">1-dars: Salomlashish</span>
                                </div>
                            </div>
                            <div class="fc-face-footer">
                                <span class="fc-hint-text">
                                    <kbd>Space</kbd> <?= t('kartani orqaga qaytarish') ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Answer Action Controls -->
                <div class="fc-controls-bar">
                    <button type="button" class="fc-btn fc-btn-review" id="btnReview" title="<?= t('Chap strelka') ?> [←]">
                        <span class="fc-btn-icon"><?= icon('rotate-cw', '', 20) ?></span>
                        <span class="fc-btn-label">
                            <strong><?= t('Yana takrorlash') ?></strong>
                            <small><?= t('Tez orada qaytariladi') ?> [←]</small>
                        </span>
                    </button>

                    <button type="button" class="fc-btn fc-btn-flip" id="btnFlipCenter" title="<?= t('Kartani aylantirish') ?> [Space]">
                        <span class="fc-btn-icon"><?= icon('rotate-cw', '', 20) ?></span>
                        <span class="fc-btn-label">
                            <strong><?= t('Aylantirish') ?></strong>
                            <small>[Space]</small>
                        </span>
                    </button>

                    <button type="button" class="fc-btn fc-btn-mastered" id="btnMastered" title="<?= t('O‘ng strelka') ?> [→]">
                        <span class="fc-btn-icon"><?= icon('check-circle', '', 20) ?></span>
                        <span class="fc-btn-label">
                            <strong><?= t('Bilib oldim!') ?></strong>
                            <small>+2 XP [→]</small>
                        </span>
                    </button>
                </div>

                <!-- Keyboard Navigation Legend -->
                <div class="fc-hotkeys-legend">
                    <span><kbd>←</kbd> <?= t('Yana takrorlash') ?></span>
                    <span><kbd>Space</kbd> <?= t('Aylantirish') ?></span>
                    <span><kbd>→</kbd> <?= t('Bilib oldim (+2 XP)') ?></span>
                </div>
            </div>

            <!-- Completion Screen (hidden initially) -->
            <div class="flashcards-finished-card" id="flashcardsFinished" style="display: none;">
                <?= mascot('celebrate', 130) ?>
                <h2><?= t('Barakalla! Mashg‘ulot yakunlandi!') ?></h2>
                <p><?= t('Siz ushbu bosqichdagi barcha so‘zlarni muvaffaqiyatli takrorlab chiqdingiz.') ?></p>

                <div class="finished-stats-row">
                    <div class="fin-stat-box">
                        <span class="fin-stat-num" id="finMasteredNum">0</span>
                        <span class="fin-stat-lbl"><?= t('O‘zlashtirilgan so‘zlar') ?></span>
                    </div>
                    <div class="fin-stat-box fin-highlight">
                        <span class="fin-stat-num" id="finXpNum">+0</span>
                        <span class="fin-stat-lbl"><?= t('Qo‘shilgan XP ball') ?></span>
                    </div>
                    <div class="fin-stat-box">
                        <span class="fin-stat-num" id="finReviewNum">0</span>
                        <span class="fin-stat-lbl"><?= t('Qayta takrorlanadi') ?></span>
                    </div>
                </div>

                <div class="finished-actions">
                    <button type="button" class="btn btn-primary btn-lg" id="btnRestartWorkout">
                        <?= icon('rotate-cw', '', 18) ?> <?= t('Qayta mashq qilish') ?>
                    </button>
                    <a href="/leaderboard" class="btn btn-gold btn-lg">
                        <?= icon('trophy', '', 18) ?> <?= t('Reytingdagi o‘rningiz') ?>
                    </a>
                    <a href="/dashboard" class="btn btn-ghost btn-lg">
                        <?= icon('layers', '', 18) ?> <?= t('Mening sahifam') ?>
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
(function() {
    const rawWords = <?= $wordListJson ?>;
    if (!rawWords || rawWords.length === 0) return;

    let deck = [...rawWords];
    let currentIndex = 0;
    let isFlipped = false;
    let masteredCount = 0;
    let reviewCount = 0;
    let earnedXp = 0;

    const flipCard = document.getElementById('flipCard');
    const frontWordUz = document.getElementById('frontWordUz');
    const frontPron = document.getElementById('frontPron');
    const cardCourseTag = document.getElementById('cardCourseTag');
    const backWordEn = document.getElementById('backWordEn');
    const backExampleText = document.getElementById('backExampleText');
    const backLessonTitle = document.getElementById('backLessonTitle');
    const progressStepText = document.getElementById('progressStepText');
    const progressPercentage = document.getElementById('progressPercentage');
    const progressFillBar = document.getElementById('progressFillBar');

    const cardMasteredCount = document.getElementById('cardMasteredCount');
    const cardXpEarned = document.getElementById('cardXpEarned');

    const btnReview = document.getElementById('btnReview');
    const btnFlipCenter = document.getElementById('btnFlipCenter');
    const btnMastered = document.getElementById('btnMastered');
    const fcListenBtn = document.getElementById('fcListenBtn');
    const fcMicBtn = document.getElementById('fcMicBtn');
    const fcMicFeedback = document.getElementById('fcMicFeedback');

    const flashcardsApp = document.getElementById('flashcardsApp');
    const flashcardsFinished = document.getElementById('flashcardsFinished');
    const finMasteredNum = document.getElementById('finMasteredNum');
    const finXpNum = document.getElementById('finXpNum');
    const finReviewNum = document.getElementById('finReviewNum');
    const btnRestartWorkout = document.getElementById('btnRestartWorkout');

    function renderCard() {
        if (currentIndex >= deck.length) {
            finishDeck();
            return;
        }

        const item = deck[currentIndex];
        isFlipped = false;
        flipCard.classList.remove('is-flipped');

        frontWordUz.textContent = item.uz;
        frontPron.textContent = item.pron ? '[' + item.pron + ']' : '';
        cardCourseTag.textContent = item.course || 'LingoUz';

        backWordEn.textContent = item.en;
        backExampleText.textContent = item.example ? '«' + item.example + '»' : '«' + item.uz + ' — ' + item.en + '»';
        backLessonTitle.textContent = item.lesson || '';

        // Reset mic feedback
        fcMicFeedback.style.display = 'none';
        fcMicFeedback.className = 'fc-mic-feedback';
        fcMicFeedback.textContent = '';

        // Progress
        const pct = Math.round((currentIndex / deck.length) * 100);
        progressStepText.textContent = (currentIndex + 1) + ' / ' + deck.length;
        progressPercentage.textContent = pct + '%';
        progressFillBar.style.width = pct + '%';
    }

    function toggleFlip() {
        isFlipped = !isFlipped;
        flipCard.classList.toggle('is-flipped', isFlipped);
        if (typeof window.SoundFX !== 'undefined') window.SoundFX.flip();
    }

    function speakCurrentWord() {
        if (currentIndex >= deck.length) return;
        const word = deck[currentIndex].uz;
        if (window.speakUzbek) {
            window.speakUzbek(word);
        } else if ('speechSynthesis' in window) {
            const u = new SpeechSynthesisUtterance(word);
            u.lang = 'uz-UZ';
            window.speechSynthesis.speak(u);
        }
    }

    // Mic Speech-to-Text pronunciation check
    let recognition = null;
    const SpeechRec = window.SpeechRecognition || window.webkitSpeechRecognition;

    function startMicCheck() {
        if (!SpeechRec) {
            fcMicFeedback.style.display = 'block';
            fcMicFeedback.className = 'fc-mic-feedback is-error';
            fcMicFeedback.textContent = 'Brauzeringiz ovoz yozishni qo‘llab-quvvatlamaydi (Chrome tavsiya etiladi).';
            return;
        }

        if (recognition) {
            try { recognition.abort(); } catch(e) {}
        }

        recognition = new SpeechRec();
        recognition.lang = 'uz-UZ';
        recognition.interimResults = false;
        recognition.maxAlternatives = 3;

        fcMicFeedback.style.display = 'block';
        fcMicFeedback.className = 'fc-mic-feedback is-listening';
        fcMicFeedback.textContent = '🎙 Tinglayapman... So‘zni aniq talaffuz qiling!';
        fcMicBtn.classList.add('is-active');

        recognition.onresult = (evt) => {
            fcMicBtn.classList.remove('is-active');
            const target = deck[currentIndex].uz.toLowerCase().replace(/[^a-z0-9öşçğўқғҳ]/gi, '').trim();
            let heard = '';
            let matched = false;

            for (let i = 0; i < evt.results[0].length; i++) {
                const alt = evt.results[0][i].transcript.toLowerCase().trim();
                heard = alt;
                const cleanAlt = alt.replace(/[^a-z0-9öşçğўқғҳ]/gi, '');
                if (cleanAlt === target || cleanAlt.includes(target) || target.includes(cleanAlt)) {
                    matched = true;
                    break;
                }
            }

            if (matched) {
                fcMicFeedback.className = 'fc-mic-feedback is-success';
                fcMicFeedback.textContent = '🎯 Ajoyib! Talaffuz to‘g‘ri: «' + heard + '»';
                // Automatically count as mastered after short pause
                setTimeout(() => {
                    handleMastered();
                }, 1200);
            } else {
                fcMicFeedback.className = 'fc-mic-feedback is-warning';
                fcMicFeedback.textContent = 'Tinglandi: «' + heard + '». Qayta urinib ko‘ring!';
            }
        };

        recognition.onerror = (err) => {
            fcMicBtn.classList.remove('is-active');
            fcMicFeedback.style.display = 'block';
            fcMicFeedback.className = 'fc-mic-feedback is-error';
            fcMicFeedback.textContent = 'Ovoz aniqlanmadi yoki mikrofon ruxsati berilmadi.';
        };

        recognition.onend = () => {
            fcMicBtn.classList.remove('is-active');
        };

        try {
            recognition.start();
        } catch (e) {
            fcMicBtn.classList.remove('is-active');
        }
    }

    function handleMastered() {
        masteredCount++;
        earnedXp += 2;
        cardMasteredCount.textContent = masteredCount;
        cardXpEarned.textContent = earnedXp;

        if (typeof window.SoundFX !== 'undefined') window.SoundFX.correct();
        if (typeof window.showFloatingXP === 'function') window.showFloatingXP(btnMastered, '+2 XP');

        flipCard.classList.add('anim-mastered');
        setTimeout(() => {
            flipCard.classList.remove('anim-mastered');
            currentIndex++;
            renderCard();
        }, 220);
    }

    function handleReview() {
        reviewCount++;
        // Push this word towards the end of deck so it repeats
        const item = deck[currentIndex];
        deck.push(item);

        if (typeof window.SoundFX !== 'undefined') window.SoundFX.click();

        flipCard.classList.add('anim-review');
        setTimeout(() => {
            flipCard.classList.remove('anim-review');
            currentIndex++;
            renderCard();
        }, 220);
    }

    function finishDeck() {
        flashcardsApp.style.display = 'none';
        flashcardsFinished.style.display = 'block';

        finMasteredNum.textContent = masteredCount;
        finXpNum.textContent = '+' + earnedXp + ' XP';
        finReviewNum.textContent = reviewCount;

        if (typeof window.SoundFX !== 'undefined') window.SoundFX.fanfare();
        if (typeof window.triggerConfetti === 'function') window.triggerConfetti();

        // Save progress & award XP to user account
        if (earnedXp > 0) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            fetch('/flashcards/done', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': csrfToken
                },
                body: JSON.stringify({ known_count: masteredCount })
            }).then(r => r.json()).then(data => {
                if (data.ok && window.confetti) {
                    window.confetti({ particleCount: 80, spread: 70, origin: { y: 0.6 } });
                }
            }).catch(e => console.warn('Flashcards save warning:', e));
        }
    }

    function restart() {
        deck = [...rawWords].sort(() => Math.random() - 0.5);
        currentIndex = 0;
        masteredCount = 0;
        reviewCount = 0;
        earnedXp = 0;
        cardMasteredCount.textContent = '0';
        cardXpEarned.textContent = '0';
        flashcardsFinished.style.display = 'none';
        flashcardsApp.style.display = 'block';
        renderCard();
    }

    // Event listeners
    flipCard.addEventListener('click', (e) => {
        // Prevent flip if clicked on audio or mic button
        if (e.target.closest('.fc-audio-btn') || e.target.closest('.fc-mic-btn')) {
            return;
        }
        toggleFlip();
    });

    btnFlipCenter.addEventListener('click', toggleFlip);
    btnReview.addEventListener('click', handleReview);
    btnMastered.addEventListener('click', handleMastered);
    fcListenBtn.addEventListener('click', speakCurrentWord);
    fcMicBtn.addEventListener('click', startMicCheck);
    btnRestartWorkout.addEventListener('click', restart);

    // Keyboard shortcuts
    window.addEventListener('keydown', (e) => {
        if (flashcardsApp.style.display === 'none') return;
        if (['input', 'textarea'].includes(document.activeElement?.tagName?.toLowerCase())) return;

        if (e.code === 'Space') {
            e.preventDefault();
            toggleFlip();
        } else if (e.code === 'ArrowLeft') {
            e.preventDefault();
            handleReview();
        } else if (e.code === 'ArrowRight') {
            e.preventDefault();
            handleMastered();
        }
    });

    // Initial render
    renderCard();
})();
</script>
