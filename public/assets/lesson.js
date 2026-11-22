/**
 * LingoUz lesson page: speak buttons, reading, step nav, practice engine
 * (instant feedback, generated from the lesson vocabulary), quiz stepper with
 * build-a-sentence tiles, and the match-game reward call.
 */
(function () {
    'use strict';

    // UI strings: Uzbek source text, English from window.I18N (set by the layout).
    const L = (s) => (window.I18N && window.I18N[s]) || s;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    // SoundFX global instance with fallback
    const fx = (typeof window.SoundFX !== 'undefined') ? window.SoundFX : (typeof SoundFX !== 'undefined' ? SoundFX : { click() {}, coin() {}, correct() {}, wrong() {}, match() {}, flip() {}, encourage() {}, fanfare() {} });

    function speak(text, slow) {
        if (typeof window.speakUzbek === 'function') {
            return window.speakUzbek(text, slow);
        }
        if (!text || !('speechSynthesis' in window)) return;
        window.speechSynthesis.cancel();
        const u = new SpeechSynthesisUtterance(text);
        u.rate = slow ? 0.6 : 0.88;
        u.pitch = 1.05;
        u.lang = 'tr-TR';
        if (window.Owl) window.Owl.talk(u);
        window.speechSynthesis.speak(u);
        return u;
    }

    // Any element with data-speak reads its text aloud.
    document.addEventListener('click', (e) => {
        const el = e.target.closest('[data-speak]');
        if (!el) return;
        e.stopPropagation();
        document.querySelectorAll('.read-line.is-speaking, .btn-audio-circle.is-speaking, .listen-big.is-speaking, .btn-sub-action.is-speaking').forEach(n => n.classList.remove('is-speaking'));
        el.classList.add('is-speaking');
        fx.click();
        const u = speak(el.dataset.speak, el.dataset.slow === '1');
        if (u) {
            const clearSpeaking = () => el.classList.remove('is-speaking');
            u.addEventListener('end', clearSpeaking);
            u.addEventListener('error', clearSpeaking);
        }
        setTimeout(() => el.classList.remove('is-speaking'), 3200);
    }, true);

    // Reading: translation toggle + read everything in order.
    const reading = document.getElementById('readingText');
    document.getElementById('toggleEn')?.addEventListener('click', (e) => {
        reading.classList.toggle('show-en');
        e.currentTarget.lastChild.textContent = ' ' + (reading.classList.contains('show-en') ? L('Tarjimani yaşiriş') : L('Tarjimani körsatiş'));
    });
    document.getElementById('readAll')?.addEventListener('click', () => {
        const lines = [...reading.querySelectorAll('.read-line')];
        let i = 0;
        const next = () => {
            lines.forEach(l => l.classList.remove('is-speaking'));
            if (i >= lines.length) return;
            const line = lines[i++];
            line.classList.add('is-speaking');
            const u = speak(line.dataset.speak);
            if (u) u.onend = () => setTimeout(next, 350); else setTimeout(next, 1800);
        };
        next();
    });

    // Video subtitles language filter
    document.querySelectorAll('.btn-sub-filter').forEach(btn => {
        btn.addEventListener('click', () => {
            const card = document.getElementById('videoSubtitlesCard');
            if (!card) return;
            document.querySelectorAll('.btn-sub-filter').forEach(b => b.classList.remove('is-active'));
            btn.classList.add('is-active');
            const mode = btn.dataset.subMode;
            card.classList.remove('hide-en', 'hide-uz');
            if (mode === 'uz') card.classList.add('hide-en');
            else if (mode === 'en') card.classList.add('hide-uz');
            if (typeof SoundFX !== 'undefined') SoundFX.click();
        });
    });

    // Step nav highlights the section in view.
    const stepLinks = [...document.querySelectorAll('.lesson-steps a')];
    if ('IntersectionObserver' in window && stepLinks.length) {
        const io = new IntersectionObserver((entries) => {
            entries.forEach(en => {
                if (!en.isIntersecting) return;
                stepLinks.forEach(a => a.classList.toggle('is-active', a.getAttribute('href') === '#' + en.target.id));
            });
        }, { rootMargin: '-40% 0px -55% 0px' });
        document.querySelectorAll('.l-section').forEach(s => io.observe(s));
    }

    // Match game reward (coins are granted once per lesson by the server).
    document.addEventListener('lingo:game-won', async () => {
        const out = document.getElementById('gameReward');
        try {
            const res = await fetch(`/lessons/${window.LESSON_ID}/game`, { method: 'POST', headers: { 'X-CSRF-Token': csrf } });
            const data = await res.json();
            if (out) out.textContent = data.coins_awarded > 0
                ? L('+:n tanga hisobingizga qöşildi! Jami: :total.').replace(':n', data.coins_awarded).replace(':total', data.coins)
                : L('Zör takrorlaş! Tangalar bu dars uçun avval berilgan.');
        } catch (_) { /* offline: the banner still shows */ }
    });

    // Typing helpers for ö / ğ / ş / ç.
    document.addEventListener('click', (e) => {
        const key = e.target.closest('[data-char]');
        if (!key) return;
        const input = key.closest('.q-step, .ex-body')?.querySelector('.fill-input');
        if (!input) return;
        const s = input.selectionStart ?? input.value.length;
        input.value = input.value.slice(0, s) + key.dataset.char + input.value.slice(input.selectionEnd ?? s);
        input.focus();
        input.setSelectionRange(s + key.dataset.char.length, s + key.dataset.char.length);
        input.dispatchEvent(new Event('input', { bubbles: true }));
    });

    const norm = (t) => String(t).toLowerCase()
        .replace(/o['‘`ʻ’]/g, 'ö')
        .replace(/g['‘`ʻ’]/g, 'ğ')
        .replace(/sh/g, 'ş')
        .replace(/ch/g, 'ç')
        .replace(/[.,!?«»"“”:;]/g, '').replace(/\s+/g, ' ').trim();
    const shuffle = (a) => { const b = [...a]; for (let i = b.length - 1; i > 0; i--) { const j = Math.floor(Math.random() * (i + 1)); [b[i], b[j]] = [b[j], b[i]]; } return b; };
    const esc = (t) => String(t).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const ICON_SPEAKER = '<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"/></svg>';
    const ICON_SPEAKER_SM = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:4px;"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"/></svg>';
    const ICON_MIC = '<svg width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" x2="12" y1="19" y2="22"/></svg>';

    /** Wires a build-a-sentence block: tiles move between the bank and the answer line. */
    function wireBuild(step, onChange) {
        const answer = step.querySelector('.build-answer');
        const hidden = step.querySelector('input[type=hidden]');
        const sync = () => {
            const words = [...answer.querySelectorAll('.tile')].map(t => t.textContent);
            if (hidden) hidden.value = words.join(' ');
            onChange && onChange(words.join(' '));
        };
        step.querySelectorAll('.build-bank .tile').forEach(tile => {
            tile.addEventListener('click', () => {
                if (tile.classList.contains('is-used')) return;
                fx.click();
                speak(tile.textContent);
                tile.classList.add('is-used');
                const chip = document.createElement('button');
                chip.type = 'button';
                chip.className = 'tile';
                chip.textContent = tile.textContent;
                chip.addEventListener('click', () => {
                    fx.click();
                    chip.remove();
                    tile.classList.remove('is-used');
                    sync();
                });
                answer.appendChild(chip);
                sync();
            });
        });
    }

    // ------------------------------------------------------------------ quiz stepper
    const quiz = document.getElementById('quizStepper');
    if (quiz) {
        const steps = [...quiz.querySelectorAll('.q-step')];
        const fill = quiz.querySelector('.ex-top .bar > i');
        const count = quiz.querySelector('.ex-count');
        const prev = quiz.querySelector('[data-act=prev]');
        const next = quiz.querySelector('[data-act=next]');
        const submit = quiz.querySelector('[data-act=submit]');
        let at = 0;

        const answered = (step) => {
            const t = step.dataset.type;
            if (t === 'build' || t === 'fill') return (step.querySelector('input[name^="q_"]')?.value || '').trim() !== '';
            return !!step.querySelector('input[type=radio]:checked');
        };
        const show = () => {
            steps.forEach((s, i) => s.classList.toggle('is-active', i === at));
            fill.style.width = Math.round(at / steps.length * 100) + '%';
            count.textContent = `${at + 1} / ${steps.length}`;
            prev.disabled = at === 0;
            const last = at === steps.length - 1;
            next.hidden = last;
            submit.hidden = !last;
            const ok = answered(steps[at]);
            next.disabled = !ok;
            submit.disabled = !ok;
        };
        steps.forEach(step => {
            if (step.dataset.type === 'build') wireBuild(step, show);
            step.addEventListener('input', show);
            step.addEventListener('change', () => { fx.click(); show(); window.Owl && Owl.play(quiz.querySelector('[data-owl]'), 'encourage', 900); });
        });
        prev.addEventListener('click', () => { if (at > 0) { fx.click(); at--; show(); } });
        next.addEventListener('click', () => {
            if (at < steps.length - 1) {
                fx.click();
                at++; show();
                const auto = steps[at].querySelector('.listen-big');
                if (auto) setTimeout(() => speak(auto.dataset.speak), 250);
            }
        });
        quiz.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && e.target.matches('.fill-input')) {
                e.preventDefault();
                (next.hidden ? submit : next).click();
            }
        });
        quiz.addEventListener('submit', (e) => {
            if (steps.some(s => !answered(s)) && !confirm(L('Ba’zi savollarga javob berilmagan. Baribir yakunlaysizmi?'))) e.preventDefault();
        });
        show();
    }

    // ------------------------------------------------------------------ practice engine
    const practice = document.getElementById('practice');
    const vocab = (window.LESSON_VOCAB || []).filter(v => v.word_uz && v.word_en);
    if (practice && vocab.length >= 2) {
        const body = practice.querySelector('.ex-body');
        const foot = practice.querySelector('.ex-foot');
        const feedback = practice.querySelector('.ex-feedback');
        const btn = practice.querySelector('[data-act=check]');
        const fill = practice.querySelector('.ex-top .bar > i');
        const count = practice.querySelector('.ex-count');
        const practiceOwl = practice.querySelector('[data-owl]');

        const plain = (w) => w.replace(/\s*\(.*?\)\s*/g, '').split('/')[0].trim();
        const pickOthers = (item, key, n) => shuffle(vocab.filter(v => v !== item)).slice(0, n).map(v => v[key]);
        const makeTasks = () => {
            const pool = shuffle(vocab).slice(0, Math.min(10, vocab.length));
            return pool.map((item, i) => {
                const kind = ['listen', 'uz2en', 'speak', 'en2uz', 'type'][i % 5];
                if (kind === 'speak' || kind === 'type') return { kind, item, answer: plain(item.word_uz) };
                const key = kind === 'en2uz' ? 'word_uz' : 'word_en';
                const opts = shuffle([item[key], ...pickOthers(item, key, Math.min(3, vocab.length - 1))]);
                return { kind, item, options: opts, answer: item[key] };
            });
        };

        let tasks = makeTasks();
        let at = 0;
        let right = 0;
        let mode = 'check';
        let current = null;

        const render = () => {
            const t = tasks[at];
            mode = 'check';
            current = null;
            foot.classList.remove('is-right', 'is-wrong');
            feedback.innerHTML = '';
            btn.textContent = L('Tekshirish');
            btn.disabled = true;
            fill.style.width = Math.round(at / tasks.length * 100) + '%';
            count.textContent = `${at + 1} / ${tasks.length}`;

            let html = '';
            if (t.kind === 'listen') {
                html = `<span class="ex-kind">${L('Tinglang')}</span><h3 class="ex-prompt">${L('Nima deyildi?')}</h3>
                    <button type="button" class="listen-big" data-speak="${esc(t.item.word_uz)}" aria-label="${L('Tinglaş')}">${ICON_SPEAKER}</button>`;
            } else if (t.kind === 'speak') {
                html = `<span class="ex-kind">${L('Talaffuz qiling')}</span>
                    <h3 class="ex-prompt">${L('«:word» — baland ovozda ayting').replace(':word', esc(t.item.word_uz))}</h3>
                    <p class="ex-sub">${L('Inglizça:')} ${esc(t.item.word_en)}${t.item.pronunciation ? ' · [' + esc(t.item.pronunciation) + ']' : ''}</p>
                    <div style="text-align:center;margin-bottom:8px;">
                        <button type="button" class="btn btn-outline btn-sm" data-speak="${esc(t.item.word_uz)}" style="border-radius:99px;padding:6px 14px;">
                            ${ICON_SPEAKER_SM} ${L('Töğri talaffuzni eşitiş')}
                        </button>
                    </div>
                    <button type="button" class="mic-big" id="btnPracticeMic" aria-label="${L('Mikrofon')}">
                        ${ICON_MIC}
                    </button>
                    <div class="speech-status-box">
                        <div class="speech-status-label" id="practiceSpeechStatus">${L('Mikrofonni bosing va gapiring')}</div>
                        <div id="practiceSpeechResult"></div>
                    </div>`;
            } else if (t.kind === 'uz2en') {
                html = `<span class="ex-kind">${L('Tarjima qiling')}</span><h3 class="ex-prompt">${L('«:word» — bu nima?').replace(':word', esc(t.item.word_uz))}</h3><p class="ex-sub">${L('Inglizça ma’nosini tanlang')}</p>`;
            } else if (t.kind === 'en2uz') {
                html = `<span class="ex-kind">${L('Özbekça toping')}</span><h3 class="ex-prompt">${esc(t.item.word_en)}</h3><p class="ex-sub">${L('Özbekça qanday böladi?')}</p>`;
            } else {
                html = `<span class="ex-kind">${L('Yozing')}</span><h3 class="ex-prompt">${esc(t.item.word_en)}</h3><p class="ex-sub">${L('Özbekça yozing')}</p>
                    <input class="fill-input" type="text" autocomplete="off" autocapitalize="off" spellcheck="false" placeholder="${L('Javob…')}">
                    <div class="char-keys"><button type="button" data-char="ö">ö</button><button type="button" data-char="ğ">ğ</button><button type="button" data-char="ş">ş</button><button type="button" data-char="ç">ç</button></div>`;
            }
            if (t.options) {
                html += '<div class="ex-options">' + t.options.map((o, i) =>
                    `<button type="button" class="ex-opt" data-val="${esc(o)}"><span class="key">${i + 1}</span><span>${esc(o)}</span></button>`).join('') + '</div>';
            }
            body.innerHTML = html;

            body.querySelectorAll('.ex-opt').forEach(b => b.addEventListener('click', () => {
                if (mode !== 'check') return;
                body.querySelectorAll('.ex-opt').forEach(x => x.classList.remove('is-picked'));
                b.classList.add('is-picked');
                current = b.dataset.val;
                btn.disabled = false;
                fx.click();
                if (t.kind === 'en2uz') speak(current);
            }));
            const input = body.querySelector('.fill-input');
            if (input) {
                input.addEventListener('input', () => { current = input.value; btn.disabled = input.value.trim() === ''; });
                input.addEventListener('keydown', (e) => { if (e.key === 'Enter' && !btn.disabled) { e.preventDefault(); btn.click(); } });
            }

            const micBtn = body.querySelector('#btnPracticeMic');
            if (micBtn) {
                const statusLbl = body.querySelector('#practiceSpeechStatus');
                const resultBox = body.querySelector('#practiceSpeechResult');
                const checker = window.speechChecker;

                if (!checker || !checker.isSupported) {
                    statusLbl.innerHTML = '<span style="color:#b91c1c;">' + L('Brauzeringiz ovoz tanishni qöllab-quvvatlamaydi. Yozib topshirishingiz mumkin:') + '</span>';
                    resultBox.innerHTML = `<input class="fill-input" type="text" style="margin-top:10px;" placeholder="${L('Sözni yozing…')}">`;
                    const inp = resultBox.querySelector('input');
                    inp.addEventListener('input', () => { current = inp.value; btn.disabled = inp.value.trim() === ''; });
                    inp.focus();
                } else {
                    micBtn.addEventListener('click', () => {
                        if (mode !== 'check') return;
                        if (micBtn.classList.contains('is-recording')) {
                            checker.stop();
                            micBtn.classList.remove('is-recording');
                            statusLbl.textContent = L('Mikrofonni bosing va gapiring');
                            return;
                        }

                        micBtn.classList.add('is-recording');
                        statusLbl.innerHTML = '<span style="color:#e11d48;font-weight:800;">🎙️ ' + L('Tinglanmoqda... Marhamat, gapiring!') + '</span>';
                        resultBox.innerHTML = '';

                        checker.start({
                            lang: 'uz-UZ',
                            onResult: (primary, alternatives) => {
                                micBtn.classList.remove('is-recording');
                                current = primary;
                                const evalRes = window.evaluateSpeech ? window.evaluateSpeech(t.item.word_uz, primary, alternatives) : { score: 90, ok: true };
                                const isOk = evalRes.ok;

                                resultBox.innerHTML = `
                                    <div class="speech-transcript-box">${L('Eşitildi:')} «${esc(primary)}»</div>
                                    <br>
                                    <span class="speech-score-badge ${isOk ? 'speech-score-pass' : 'speech-score-retry'}">
                                        ${evalRes.score}% ${L('moslik')} ${isOk ? '🎉 (' + L('Ajoyib!') + ')' : '🤔 (' + L('Qayta urinib köring') + ')'}
                                    </span>
                                `;
                                statusLbl.textContent = isOk ? L('Javobingiz qabul qilindi!') : L('Qayta aytish uçun mikrofonni yana bosing');
                                btn.disabled = false;
                                if (isOk) {
                                    fx.click();
                                    setTimeout(() => {
                                        if (mode === 'check' && !btn.disabled) btn.click();
                                    }, 850);
                                }
                            },
                            onError: (err) => {
                                micBtn.classList.remove('is-recording');
                                statusLbl.textContent = err.error === 'no-speech'
                                    ? L('Ovoz eşitilmadi. Mikrofonni bosib qayta gapiring.')
                                    : L('Xatolik yuz berdi. Qayta urinib köring.');
                            },
                            onEnd: (hasResult) => {
                                micBtn.classList.remove('is-recording');
                            }
                        });
                    });
                }
            }

            if (t.kind === 'listen') setTimeout(() => speak(t.item.word_uz), 300);
        };

        const finish = () => {
            fill.style.width = '100%';
            const pct = Math.round(right / tasks.length * 100);
            body.innerHTML = `<div class="ex-done">
                <div class="ex-done-owl"></div>
                <div class="big">${pct >= 80 ? L('Barakalla!') : L('Yaxşi harakat!')}</div>
                <p class="muted">${L(':score / :total töğri').replace(':score', right).replace(':total', tasks.length)}. ${pct >= 80 ? L('Endi yakuniy testga tayyorsiz!') : L('Luğatni yana bir körib, qayta urinib köring.')}</p>
            </div>`;
            const big = practiceOwl ? practiceOwl.cloneNode(true) : null;
            if (big) {
                big.removeAttribute('data-speaker');
                big.classList.remove('owl--mini');
                big.style.setProperty('--owl-size', '110px');
                window.Owl && Owl.set(big, pct >= 80 ? 'celebrate' : 'encourage');
                body.querySelector('.ex-done-owl').appendChild(big);
            }
            if (pct >= 80) {
                fx.fanfare();
                if (typeof window.triggerConfetti === 'function') window.triggerConfetti();
            } else {
                fx.encourage();
            }
            foot.classList.remove('is-right', 'is-wrong');
            feedback.innerHTML = '';
            btn.textContent = L('Yana maşq qiliş');
            btn.disabled = false;
            mode = 'restart';
        };

        btn.addEventListener('click', () => {
            if (mode === 'restart') { tasks = makeTasks(); at = 0; right = 0; render(); return; }
            if (mode === 'next') { at++; if (at >= tasks.length) finish(); else render(); return; }
            const t = tasks[at];
            const evalResult = t.kind === 'speak' && window.evaluateSpeech ? window.evaluateSpeech(t.item.word_uz, current) : null;
            const ok = t.kind === 'speak'
                ? (evalResult ? evalResult.ok : norm(current) === norm(t.answer))
                : norm(current) === norm(t.answer);
            right += ok ? 1 : 0;
            body.querySelectorAll('.ex-opt').forEach(b => {
                if (norm(b.dataset.val) === norm(t.answer)) b.classList.add('is-right');
                else if (b.classList.contains('is-picked')) b.classList.add('is-wrong');
            });
            foot.classList.add(ok ? 'is-right' : 'is-wrong');
            feedback.innerHTML = ok
                ? `<span>${L('Barakalla! Töğri.')} ${evalResult ? '<small>(' + evalResult.score + '% ' + L('moslik') + ')</small>' : ''}</span>`
                : `<span>${L('Töğri javob:')} ${esc(t.answer)}<small>${esc(t.item.word_uz)} — ${esc(t.item.word_en)}</small></span>`;
            if (ok) {
                if (typeof fx.correct === 'function') fx.correct(); else fx.coin();
                if (typeof window.showFloatingXP === 'function') {
                    window.showFloatingXP(btn, '+10 XP');
                }
            } else {
                if (typeof fx.wrong === 'function') fx.wrong(); else fx.encourage();
                if (typeof window.shakeElement === 'function') {
                    const wrongTarget = body.querySelector('.is-wrong') || body.querySelector('.fill-input') || body;
                    window.shakeElement(wrongTarget);
                }
            }
            window.Owl && Owl.play(practiceOwl, ok ? 'success' : 'mistake', 1800);
            speak(t.item.word_uz);
            btn.textContent = L('Davom etiş');
            mode = 'next';
        });

        render();
    } else if (practice) {
        practice.querySelector('.ex-body').innerHTML = '<div class="empty">' + L('Maşq uçun kamida 2 ta söz kerak.') + '</div>';
    }
})();
