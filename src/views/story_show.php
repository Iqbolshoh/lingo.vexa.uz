<div class="pg">
    <div class="container pg-narrow">
        <!-- Top Nav -->
        <div class="story-header-nav">
            <a href="/stories" class="btn btn-outline btn-sm">
                <?= icon('arrow-left', '', 14) ?> <?= t('Barcha ertaklarga qaytish') ?>
            </a>
            <div style="display: flex; gap: 8px;">
                <button type="button" class="btn btn-glass btn-sm" id="btnToggleLang" onclick="toggleTranslation()">
                    🌐 <span id="langToggleText"><?= t('Inglizcha tarjimani ko‘rsatish') ?></span>
                </button>
            </div>
        </div>

        <!-- Story Header Banner -->
        <div class="story-reading-banner" style="background: <?= $story['gradient'] ?>;">
            <div class="story-badge-bar">
                <div style="display: flex; gap: 8px; align-items: center;">
                    <span class="story-level-pill">
                        <?= e(is_en() ? $story['level_en'] : $story['level']) ?>
                    </span>
                    <span class="story-read-time">⏱ <?= e(is_en() ? $story['read_time_en'] : $story['read_time']) ?></span>
                </div>
                <span class="tag tag-gold">⭐ +20 XP</span>
            </div>
            <h1 class="story-title" style="font-size: 30px; margin-top: 10px;">
                <?= e(is_en() ? $story['title_en'] : $story['title_uz']) ?>
            </h1>
            <p class="story-subtitle" style="font-size: 15px; margin-top: 6px;">
                <?= e(is_en() ? $story['subtitle_en'] : $story['subtitle_uz']) ?>
            </p>
        </div>

        <!-- Story Content Paragraphs -->
        <div class="card story-content-card">
            <div class="story-paragraphs-list">
                <?php foreach ($story['paragraphs'] as $idx => $p): ?>
                    <div class="story-paragraph-item">
                        <button type="button" class="btn-story-audio" onclick="speakText(<?= htmlspecialchars(json_encode($p['uz']), ENT_QUOTES, 'UTF-8') ?>, this)" title="<?= t('Ovozli eshitish') ?>">
                            <?= icon('volume', '', 20) ?>
                        </button>
                        <div style="flex: 1;">
                            <div class="story-uz-text">
                                <?= e($p['uz']) ?>
                            </div>
                            <div class="story-en-text">
                                <?= e($p['en']) ?>
                            </div>
                            <?php if (!empty($p['vocab'])): ?>
                                <div class="story-vocab-strip">
                                    <?php foreach ($p['vocab'] as $vUz => $vEn): ?>
                                        <span class="vocab-pill">
                                            <b><?= e($vUz) ?></b>: <?= e($vEn) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Story Comprehension Mini-Quiz -->
        <div class="card story-quiz-card" id="storyQuizBox">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                <span class="tag tag-emerald">🎯 <?= t('Ertakni tushunish testi') ?></span>
                <span style="font-size: 13px; color: var(--ink-soft); font-weight: 700;">+20 XP</span>
            </div>
            <h3 style="font-size: 20px; font-weight: 800; color: var(--ink); margin: 0 0 16px;">
                <?= e(is_en() ? $story['quiz']['question_en'] : $story['quiz']['question']) ?>
            </h3>

            <div class="quiz-options-list">
                <?php 
                $opts = is_en() ? $story['quiz']['options_en'] : $story['quiz']['options'];
                $correct = (int) $story['quiz']['correct'];
                ?>
                <?php foreach ($opts as $oidx => $opt): ?>
                    <button type="button" class="quiz-opt-btn" onclick="checkStoryAnswer(<?= $oidx ?>, <?= $correct ?>, <?= htmlspecialchars(json_encode($story['quiz']['explanation']), ENT_QUOTES, 'UTF-8') ?>)">
                        <?= e($opt) ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <div id="quizResultMsg" class="quiz-result-msg" style="display: none; padding: 14px 18px; border-radius: 12px; font-size: 14.5px; font-weight: 600; margin-top: 10px;"></div>
        </div>
    </div>
</div>

<script>
var showEn = false;
function toggleTranslation() {
    showEn = !showEn;
    var ens = document.querySelectorAll('.story-en-text');
    ens.forEach(function(el) {
        el.style.display = showEn ? 'block' : 'none';
    });
    var btnText = document.getElementById('langToggleText');
    if (btnText) {
        btnText.innerText = showEn ? '<?= t("Inglizcha tarjimani yashirish") ?>' : '<?= t("Inglizcha tarjimani ko‘rsatish") ?>';
    }
}

function speakText(text, btn) {
    if (!('speechSynthesis' in window)) {
        alert('Brauzeringiz ovozli talaffuzni qo‘llab-quvvatlamaydi.');
        return;
    }
    window.speechSynthesis.cancel();
    var u = new SpeechSynthesisUtterance(text);
    u.lang = 'uz-UZ';
    u.rate = 0.9;
    
    if (btn) {
        btn.style.background = '#0284c7';
        btn.style.color = '#ffffff';
        u.onend = function() {
            btn.style.background = '#e0f2fe';
            btn.style.color = '#0284c7';
        };
        u.onerror = function() {
            btn.style.background = '#e0f2fe';
            btn.style.color = '#0284c7';
        };
    }
    window.speechSynthesis.speak(u);
}

var quizCompleted = false;
function checkStoryAnswer(chosen, correct, explanation) {
    if (quizCompleted) return;
    var btns = document.querySelectorAll('.quiz-opt-btn');
    btns.forEach(function(b, idx) {
        b.disabled = true;
        b.style.cursor = 'default';
        if (idx === correct) {
            b.style.borderColor = '#10b981';
            b.style.background = '#dcfce7';
            b.style.color = '#065f46';
        } else if (idx === chosen) {
            b.style.borderColor = '#ef4444';
            b.style.background = '#fee2e2';
            b.style.color = '#991b1b';
        }
    });

    var resBox = document.getElementById('quizResultMsg');
    resBox.style.display = 'block';

    if (chosen === correct) {
        quizCompleted = true;
        resBox.style.background = '#dcfce7';
        resBox.style.color = '#065f46';
        resBox.innerHTML = '🎉 <b>To‘g‘ri javob! +20 XP qo‘shildi!</b><br><small style="opacity:.9">' + explanation + '</small>';

        if (typeof window.SoundFX !== 'undefined') window.SoundFX.correct();
        if (typeof window.showFloatingXP === 'function' && btns[chosen]) {
            window.showFloatingXP(btns[chosen], '+20 XP');
        }
        if (typeof window.triggerConfetti === 'function') {
            window.triggerConfetti();
        }

        // Award XP via API
        fetch('/stories/<?= e($story['id']) ?>/complete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }).catch(function() {});
    } else {
        if (typeof window.SoundFX !== 'undefined') window.SoundFX.wrong();
        if (typeof window.shakeElement === 'function' && btns[chosen]) {
            window.shakeElement(btns[chosen]);
        }
        resBox.style.background = '#fee2e2';
        resBox.style.color = '#991b1b';
        resBox.innerHTML = '❌ <b>Xato javob.</b> To‘g‘ri javob yashil rang bilan belgilandi.<br><small style="opacity:.9">' + explanation + '</small>';
    }
}
</script>
