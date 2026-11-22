<div class="pg">
    <div class="container" style="max-width: 820px; margin: 0 auto;">
        <!-- Top Nav -->
        <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
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
        <div class="card" style="background: <?= $story['gradient'] ?>; color: #ffffff; padding: 32px 28px; border-radius: 20px; margin-bottom: 28px; box-shadow: var(--shadow-lg);">
            <div style="display: flex; gap: 10px; align-items: center; margin-bottom: 10px;">
                <span style="background: rgba(255,255,255,0.22); padding: 3px 10px; border-radius: 999px; font-size: 12px; font-weight: 800;">
                    <?= e(is_en() ? $story['level_en'] : $story['level']) ?>
                </span>
                <span style="font-size: 12.5px; opacity: 0.9;">⏱ <?= e(is_en() ? $story['read_time_en'] : $story['read_time']) ?></span>
                <span style="font-size: 12.5px; opacity: 0.9;">⭐ +20 XP</span>
            </div>
            <h1 style="font-family: var(--font-heading); font-size: 34px; font-weight: 800; color: #ffffff; margin: 0 0 8px;">
                <?= e(is_en() ? $story['title_en'] : $story['title_uz']) ?>
            </h1>
            <p style="font-size: 15px; opacity: 0.95; margin: 0; line-height: 1.5;">
                <?= e(is_en() ? $story['subtitle_en'] : $story['subtitle_uz']) ?>
            </p>
        </div>

        <!-- Story Content Paragraphs -->
        <div class="card story-content-card" style="padding: 32px 28px; border-radius: 18px; margin-bottom: 32px;">
            <div class="story-paragraphs" style="display: flex; flex-direction: column; gap: 24px;">
                <?php foreach ($story['paragraphs'] as $idx => $p): ?>
                    <div class="story-item" style="display: flex; gap: 16px; align-items: flex-start; padding-bottom: 20px; border-bottom: 1px solid #f1f5f9;">
                        <button type="button" class="btn-story-audio" onclick="speakText(<?= htmlspecialchars(json_encode($p['uz']), ENT_QUOTES, 'UTF-8') ?>, this)" title="<?= t('Ovozli eshitish') ?>" style="width: 44px; height: 44px; border-radius: 50%; background: #e0f2fe; border: 1px solid #bae6fd; color: #0284c7; display: flex; align-items: center; justify-content: center; flex-shrink: 0; cursor: pointer; transition: all 0.15s ease;">
                            <?= icon('volume', '', 20) ?>
                        </button>
                        <div style="flex: 1;">
                            <div class="story-uz-text" style="font-size: 18px; line-height: 1.6; color: #0f172a; font-weight: 500;">
                                <?= e($p['uz']) ?>
                            </div>
                            <div class="story-en-text" style="font-size: 14.5px; line-height: 1.5; color: #64748b; margin-top: 6px; display: none;">
                                <?= e($p['en']) ?>
                            </div>
                            <?php if (!empty($p['vocab'])): ?>
                                <div style="display: flex; gap: 6px; flex-wrap: wrap; margin-top: 8px;">
                                    <?php foreach ($p['vocab'] as $vUz => $vEn): ?>
                                        <span class="vocab-pill" style="font-size: 12px; background: #fef3c7; color: #92400e; padding: 2px 8px; border-radius: 6px; border: 1px solid #fde68a;">
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
        <div class="card quiz-card" id="storyQuizBox" style="padding: 32px 28px; border-radius: 18px; border-top: 4px solid #059669; margin-bottom: 40px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                <span class="tag tag-emerald">🎯 <?= t('Ertakni tushunish testi') ?></span>
                <span style="font-size: 13px; color: #64748b; font-weight: 700;">+20 XP</span>
            </div>
            <h3 style="font-size: 20px; font-weight: 800; color: #0f172a; margin: 0 0 16px;">
                <?= e(is_en() ? $story['quiz']['question_en'] : $story['quiz']['question']) ?>
            </h3>

            <div class="quiz-options-list" style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px;">
                <?php 
                $opts = is_en() ? $story['quiz']['options_en'] : $story['quiz']['options'];
                $correct = (int) $story['quiz']['correct'];
                ?>
                <?php foreach ($opts as $oidx => $opt): ?>
                    <button type="button" class="quiz-opt-btn" onclick="checkStoryAnswer(<?= $oidx ?>, <?= $correct ?>, <?= htmlspecialchars(json_encode($story['quiz']['explanation']), ENT_QUOTES, 'UTF-8') ?>)" style="padding: 14px 18px; text-align: left; background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 15px; font-weight: 600; color: #1e293b; cursor: pointer; transition: all 0.15s ease;">
                        <?= e($opt) ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <div id="quizResultMsg" style="display: none; padding: 14px 18px; border-radius: 12px; font-size: 14.5px; font-weight: 600; margin-top: 10px;"></div>
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

        // Award XP via API
        fetch('/stories/<?= e($story['id']) ?>/complete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }).catch(function() {});
    } else {
        resBox.style.background = '#fee2e2';
        resBox.style.color = '#991b1b';
        resBox.innerHTML = '❌ <b>Xato javob.</b> To‘g‘ri javob yashil rang bilan belgilandi.<br><small style="opacity:.9">' + explanation + '</small>';
    }
}
</script>
