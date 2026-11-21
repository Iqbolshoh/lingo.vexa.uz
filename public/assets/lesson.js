/**
 * LingoUz lesson page: speak buttons, reading, step nav, practice engine
 * (instant feedback, generated from the lesson vocabulary), quiz stepper with
 * build-a-sentence tiles, and the match-game reward call.
 */
(function () {
    'use strict';

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    // SoundFX is a top-level const in app.js, so it is not a window property.
    const fx = (typeof SoundFX !== 'undefined') ? SoundFX : { click() {}, coin() {}, match() {}, encourage() {}, fanfare() {} };

    function speak(text, slow) {
        if (!text || !('speechSynthesis' in window)) return;
        window.speechSynthesis.cancel();
        const u = new SpeechSynthesisUtterance(text);
        u.rate = slow ? 0.55 : 0.85;
        u.pitch = 1.05;
        const voice = window.speechSynthesis.getVoices().find(v => v.lang && v.lang.toLowerCase().startsWith('uz'));
        if (voice) u.voice = voice; else u.lang = 'uz-UZ';
        if (window.Owl) window.Owl.talk(u);
        window.speechSynthesis.speak(u);
        return u;
    }

    // Any element with data-speak reads its text aloud.
    document.addEventListener('click', (e) => {
        const el = e.target.closest('[data-speak]');
        if (!el) return;
        e.stopPropagation();
        document.querySelectorAll('.read-line.is-speaking').forEach(n => n.classList.remove('is-speaking'));
        if (el.classList.contains('read-line')) el.classList.add('is-speaking');
        speak(el.dataset.speak, el.dataset.slow === '1');
    }, true);

    // Reading: translation toggle + read everything in order.
    const reading = document.getElementById('readingText');
    document.getElementById('toggleEn')?.addEventListener('click', (e) => {
        reading.classList.toggle('show-en');
        e.currentTarget.lastChild.textContent = reading.classList.contains('show-en') ? ' Tarjimani yashirish' : ' Tarjimani ko‘rsatish';
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
                ? `+${data.coins_awarded} tanga hisobingizga qo‘shildi! Jami: ${data.coins}.`
                : 'Zo‘r takrorlash! Tangalar bu dars uchun avval berilgan.';
        } catch (_) { /* offline: the banner still shows */ }
    });

    // Typing helpers for o‘ / g‘.
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

    const norm = (t) => String(t).toLowerCase().replace(/[‘’ʻʼ`´']/g, "'").replace(/[.,!?«»"“”:;]/g, '').replace(/\s+/g, ' ').trim();
    const shuffle = (a) => { const b = [...a]; for (let i = b.length - 1; i > 0; i--) { const j = Math.floor(Math.random() * (i + 1)); [b[i], b[j]] = [b[j], b[i]]; } return b; };
    const esc = (t) => String(t).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const ICON_SPEAKER = '<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"/></svg>';

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
                chip.addEventListener('click', () => { chip.remove(); tile.classList.remove('is-used'); sync(); });
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
        prev.addEventListener('click', () => { if (at > 0) { at--; show(); } });
        next.addEventListener('click', () => {
            if (at < steps.length - 1) {
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
            if (steps.some(s => !answered(s)) && !confirm('Ba’zi savollarga javob berilmagan. Baribir yakunlaysizmi?')) e.preventDefault();
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
        const makeTasks = () => shuffle(vocab).slice(0, Math.min(8, vocab.length)).map((item, i) => {
            const kind = ['listen', 'uz2en', 'en2uz', 'type'][i % 4];
            if (kind === 'type') return { kind, item, answer: plain(item.word_uz) };
            const key = kind === 'en2uz' ? 'word_uz' : 'word_en';
            const opts = shuffle([item[key], ...pickOthers(item, key, Math.min(3, vocab.length - 1))]);
            return { kind, item, options: opts, answer: item[key] };
        });

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
            btn.textContent = 'Tekshirish';
            btn.disabled = true;
            fill.style.width = Math.round(at / tasks.length * 100) + '%';
            count.textContent = `${at + 1} / ${tasks.length}`;

            let html = '';
            if (t.kind === 'listen') {
                html = `<span class="ex-kind">Tinglang</span><h3 class="ex-prompt">Nima deyildi?</h3>
                    <button type="button" class="listen-big" data-speak="${esc(t.item.word_uz)}" aria-label="Tinglash">${ICON_SPEAKER}</button>`;
            } else if (t.kind === 'uz2en') {
                html = `<span class="ex-kind">Tarjima qiling</span><h3 class="ex-prompt">«${esc(t.item.word_uz)}» — bu nima?</h3><p class="ex-sub">Choose the English meaning</p>`;
            } else if (t.kind === 'en2uz') {
                html = `<span class="ex-kind">O‘zbekcha toping</span><h3 class="ex-prompt">${esc(t.item.word_en)}</h3><p class="ex-sub">O‘zbekcha qanday bo‘ladi?</p>`;
            } else {
                html = `<span class="ex-kind">Yozing</span><h3 class="ex-prompt">${esc(t.item.word_en)}</h3><p class="ex-sub">O‘zbekcha yozing</p>
                    <input class="fill-input" type="text" autocomplete="off" autocapitalize="off" spellcheck="false" placeholder="Javob…">
                    <div class="char-keys"><button type="button" data-char="o‘">o‘</button><button type="button" data-char="g‘">g‘</button></div>`;
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
            if (t.kind === 'listen') setTimeout(() => speak(t.item.word_uz), 300);
        };

        const finish = () => {
            fill.style.width = '100%';
            const pct = Math.round(right / tasks.length * 100);
            body.innerHTML = `<div class="ex-done">
                <div class="ex-done-owl"></div>
                <div class="big">${pct >= 80 ? 'Barakalla!' : 'Yaxshi harakat!'}</div>
                <p class="muted">${right} / ${tasks.length} to‘g‘ri. ${pct >= 80 ? 'Endi yakuniy testga tayyorsiz!' : 'Lug‘atni yana bir ko‘rib, qayta urinib ko‘ring.'}</p>
            </div>`;
            const big = practiceOwl ? practiceOwl.cloneNode(true) : null;
            if (big) {
                big.removeAttribute('data-speaker');
                big.classList.remove('owl--mini');
                big.style.setProperty('--owl-size', '110px');
                window.Owl && Owl.set(big, pct >= 80 ? 'celebrate' : 'encourage');
                body.querySelector('.ex-done-owl').appendChild(big);
            }
            if (pct >= 80) fx.fanfare(); else fx.encourage();
            foot.classList.remove('is-right', 'is-wrong');
            feedback.innerHTML = '';
            btn.textContent = 'Yana mashq qilish';
            btn.disabled = false;
            mode = 'restart';
        };

        btn.addEventListener('click', () => {
            if (mode === 'restart') { tasks = makeTasks(); at = 0; right = 0; render(); return; }
            if (mode === 'next') { at++; if (at >= tasks.length) finish(); else render(); return; }
            const t = tasks[at];
            const ok = norm(current) === norm(t.answer);
            right += ok ? 1 : 0;
            body.querySelectorAll('.ex-opt').forEach(b => {
                if (norm(b.dataset.val) === norm(t.answer)) b.classList.add('is-right');
                else if (b.classList.contains('is-picked')) b.classList.add('is-wrong');
            });
            foot.classList.add(ok ? 'is-right' : 'is-wrong');
            feedback.innerHTML = ok
                ? `<span>Barakalla! To‘g‘ri.</span>`
                : `<span>To‘g‘ri javob: ${esc(t.answer)}<small>${esc(t.item.word_uz)} — ${esc(t.item.word_en)}</small></span>`;
            ok ? fx.coin() : fx.encourage();
            window.Owl && Owl.play(practiceOwl, ok ? 'success' : 'mistake', 1800);
            speak(t.item.word_uz);
            btn.textContent = 'Davom etish';
            mode = 'next';
        });

        render();
    } else if (practice) {
        practice.querySelector('.ex-body').innerHTML = '<div class="empty">Mashq uchun kamida 2 ta so‘z kerak.</div>';
    }
})();
