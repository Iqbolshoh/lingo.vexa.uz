/**
 * LingoUz by Gulomov's Academy - Interactive Audio, Animations & Mascot Features
 */

// Global Audio Context for zero-latency Sound FX
let audioCtx = null;
let soundEnabled = true;

function getAudioContext() {
    if (!audioCtx) {
        const AudioContextClass = window.AudioContext || window.webkitAudioContext;
        if (AudioContextClass) {
            audioCtx = new AudioContextClass();
        }
    }
    if (audioCtx && audioCtx.state === 'suspended') {
        audioCtx.resume();
    }
    return audioCtx;
}

// Sound Synthesizer via Web Audio API (Zero external assets, instant 100% reliable)
const SoundFX = {
    playTone(freq, type = 'sine', duration = 0.15, delay = 0, gainLevel = 0.15) {
        if (!soundEnabled) return;
        try {
            const ctx = getAudioContext();
            if (!ctx) return;

            const osc = ctx.createOscillator();
            const gain = ctx.createGain();

            osc.type = type;
            osc.frequency.setValueAtTime(freq, ctx.currentTime + delay);

            gain.gain.setValueAtTime(gainLevel, ctx.currentTime + delay);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + delay + duration);

            osc.connect(gain);
            gain.connect(ctx.destination);

            osc.start(ctx.currentTime + delay);
            osc.stop(ctx.currentTime + delay + duration);
        } catch (e) {
            console.log("Audio FX error:", e);
        }
    },

    // Soft UI Click
    click() {
        this.playTone(600, 'triangle', 0.05, 0, 0.1);
    },

    // Coin Pickup Sound 🪙
    coin() {
        this.playTone(987.77, 'sine', 0.08, 0, 0.2); // B5
        this.playTone(1318.51, 'sine', 0.25, 0.08, 0.2); // E6
    },

    // Match Success Chime ✨
    match() {
        this.playTone(523.25, 'sine', 0.1, 0, 0.15); // C5
        this.playTone(659.25, 'sine', 0.1, 0.08, 0.15); // E5
        this.playTone(783.99, 'sine', 0.12, 0.16, 0.15); // G5
        this.playTone(1046.50, 'sine', 0.3, 0.24, 0.2); // C6
    },

    // Fanfare for Quiz 80%+ Victory 🎺
    fanfare() {
        const notes = [523.25, 659.25, 783.99, 1046.50];
        notes.forEach((freq, idx) => {
            this.playTone(freq, 'triangle', 0.25, idx * 0.12, 0.25);
        });
        setTimeout(() => {
            this.playTone(1046.50, 'square', 0.6, 0.5, 0.15);
            this.playTone(1318.51, 'sine', 0.6, 0.5, 0.2);
        }, 100);
    },

    // Gentle encouraging tone on wrong attempt
    encourage() {
        this.playTone(440, 'sine', 0.15, 0, 0.15);
        this.playTone(392, 'sine', 0.25, 0.15, 0.15);
    }
};

/**
 * Lingo mascot API: Owl.set(el, state) switches the base state,
 * Owl.play(el, state, ms) shows a state briefly and returns to the base one.
 */
const Owl = {
    states: ['idle', 'welcome', 'thinking', 'learning', 'success', 'celebrate', 'mistake', 'encourage', 'sleeping', 'speaking'],
    apply(el, state) {
        if (!el) return;
        this.states.forEach(s => el.classList.remove('owl--' + s));
        el.classList.add('owl--' + state);
    },
    set(el, state) {
        if (!el) return;
        el.dataset.state = state;
        clearTimeout(el._owlTimer);
        this.apply(el, state);
    },
    play(el, state, ms = 1500) {
        if (!el) return;
        clearTimeout(el._owlTimer);
        this.apply(el, state);
        el._owlTimer = setTimeout(() => this.apply(el, el.dataset.state || 'idle'), ms);
    },
    /** Every owl marked data-speaker moves its beak while the utterance plays. */
    talk(utterance) {
        const owls = document.querySelectorAll('.owl[data-speaker]');
        if (!owls.length || !utterance) return;
        owls.forEach(o => { clearTimeout(o._owlTimer); this.apply(o, 'speaking'); });
        const stop = () => owls.forEach(o => this.apply(o, o.dataset.state || 'idle'));
        utterance.addEventListener('end', stop);
        utterance.addEventListener('error', stop);
        setTimeout(stop, 6000);
    },
};
window.Owl = Owl;

/**
 * Natural Uzbek Speech Synthesis via Web Speech API
 * Automatically selects the best phonetically matching Turkic voice model (Uzbek, Turkish)
 * and applies phonetic text adaptation so pronunciation matches authentic Uzbek, NOT English!
 */
let uzVoiceInfo = null;

function getBestUzbekVoice() {
    if (!('speechSynthesis' in window)) return null;
    const voices = window.speechSynthesis.getVoices() || [];
    if (!voices.length) return null;

    // 1. Native Uzbek voice if available
    let v = voices.find(v => v.lang && v.lang.toLowerCase().startsWith('uz'));
    if (v) return { voice: v, lang: 'uz-UZ' };

    // 2. Turkish voice (built into Chrome, Edge, Safari, Windows, Mac, Android, iOS)
    // Closest Turkic phonetics: pure vowels, rolled r, matching consonants (ş, ç, ö, ğ)
    v = voices.find(v => v.lang && v.lang.toLowerCase().startsWith('tr'));
    if (v) return { voice: v, lang: 'tr-TR' };

    // 3. Azerbaijani
    v = voices.find(v => v.lang && v.lang.toLowerCase().startsWith('az'));
    if (v) return { voice: v, lang: 'az-AZ' };

    // 4. Kazakh / Tatar
    v = voices.find(v => v.lang && (v.lang.toLowerCase().startsWith('kk') || v.lang.toLowerCase().startsWith('tt')));
    if (v) return { voice: v, lang: v.lang };

    // 5. Russian (good phonetic consonant/vowel accuracy)
    v = voices.find(v => v.lang && v.lang.toLowerCase().startsWith('ru'));
    if (v) return { voice: v, lang: 'ru-RU' };

    // 6. Italian or Spanish (pure Latin vowels)
    v = voices.find(v => v.lang && (v.lang.toLowerCase().startsWith('it') || v.lang.toLowerCase().startsWith('es')));
    if (v) return { voice: v, lang: v.lang };

    return null;
}

if (typeof window !== 'undefined' && 'speechSynthesis' in window) {
    window.speechSynthesis.onvoiceschanged = () => {
        uzVoiceInfo = getBestUzbekVoice();
    };
}

function prepareUzbekSpeech(text, targetLang) {
    if (!text || typeof text !== 'string') return '';
    let s = text.replace(/<[^>]+>/g, '').trim();

    // Standardize to new Latin alphabet first
    s = s.replace(/o['‘`ʻ’]/gi, m => m[0] === 'O' ? 'Ö' : 'ö')
         .replace(/g['‘`ʻ’]/gi, m => m[0] === 'G' ? 'Ğ' : 'ğ')
         .replace(/sh/gi, m => m[0] === 'S' ? 'Ş' : 'ş')
         .replace(/ch/gi, m => m[0] === 'C' ? 'Ç' : 'ç');

    const lang = (targetLang || 'tr-TR').toLowerCase();

    if (lang.startsWith('uz')) {
        return s;
    }

    if (lang.startsWith('tr') || lang.startsWith('az')) {
        return s
            .replace(/q/g, 'k').replace(/Q/g, 'K')
            .replace(/x/g, 'h').replace(/X/g, 'H')
            .replace(/['‘`ʻ’]/g, '');
    }

    if (lang.startsWith('ru')) {
        const cyr = {
            'Ö': 'Ў', 'ö': 'ў', 'Ğ': 'Ғ', 'ğ': 'ғ', 'Ş': 'Ш', 'ş': 'ш', 'Ç': 'Ч', 'ç': 'ч',
            'A': 'А', 'a': 'а', 'B': 'Б', 'b': 'б', 'D': 'Д', 'd': 'д', 'E': 'Е', 'e': 'е',
            'F': 'Ф', 'f': 'ф', 'G': 'Г', 'g': 'г', 'H': 'Ҳ', 'h': 'ҳ', 'I': 'И', 'i': 'и',
            'J': 'Ж', 'j': 'ж', 'K': 'К', 'k': 'к', 'L': 'Л', 'l': 'л', 'M': 'М', 'm': 'м',
            'N': 'Н', 'n': 'н', 'O': 'О', 'o': 'о', 'P': 'П', 'p': 'п', 'Q': 'Қ', 'q': 'қ',
            'R': 'Р', 'r': 'р', 'S': 'С', 's': 'с', 'T': 'Т', 't': 'т', 'U': 'У', 'u': 'у',
            'V': 'В', 'v': 'в', 'X': 'Х', 'x': 'х', 'Y': 'Й', 'y': 'й', 'Z': 'З', 'z': 'з'
        };
        return s.split('').map(c => cyr[c] || c).join('');
    }

    // Default fallback
    return s
        .replace(/['‘`ʻ’]/g, '')
        .replace(/ö/g, 'u').replace(/Ö/g, 'U')
        .replace(/ğ/g, 'g').replace(/Ğ/g, 'G')
        .replace(/ş/g, 'sh').replace(/Ş/g, 'Sh')
        .replace(/ç/g, 'ch').replace(/Ç/g, 'Ch')
        .replace(/x/g, 'h').replace(/X/g, 'H')
        .replace(/q/g, 'k').replace(/Q/g, 'K');
}

function speakUzbek(text, slow = false) {
    if (!text || !('speechSynthesis' in window)) return null;

    window.speechSynthesis.cancel();

    const info = uzVoiceInfo || getBestUzbekVoice();
    const targetLang = info ? info.lang : 'tr-TR';
    const spokenText = prepareUzbekSpeech(text, targetLang);

    const utterance = new SpeechSynthesisUtterance(spokenText);
    utterance.rate = slow ? 0.6 : 0.88;
    utterance.pitch = 1.05;

    if (info && info.voice) {
        utterance.voice = info.voice;
        utterance.lang = info.voice.lang;
    } else {
        utterance.lang = 'tr-TR';
    }

    if (window.Owl && typeof window.Owl.talk === 'function') {
        window.Owl.talk(utterance);
    }
    window.speechSynthesis.speak(utterance);
    return utterance;
}
window.speakUzbek = speakUzbek;

document.addEventListener('DOMContentLoaded', () => {
    // 1. Mobile Menu Toggle
    const menuToggle = document.getElementById('menuToggle');
    const mainNav = document.getElementById('mainNav');
    if (menuToggle && mainNav) {
        menuToggle.addEventListener('click', () => {
            mainNav.classList.toggle('open');
            SoundFX.click();
        });
    }

    // 2. Play sound when clicking interactive buttons
    document.querySelectorAll('.btn, .nav-pill, .badge-status').forEach(el => {
        el.addEventListener('mouseenter', () => {
            // subtle hover effect if desired
        });
    });

    // 3. Flip Card Toggle for Vocabulary
    document.querySelectorAll('.vocab-card-flipper').forEach(card => {
        card.addEventListener('click', (e) => {
            // Don't flip if clicking audio button directly
            if (e.target.closest('.btn-audio-circle')) return;
            card.classList.toggle('flipped');
            SoundFX.click();
        });
    });

    // 4. Initialize Interactive Matching Game
    if (document.getElementById('matchGameGrid') && window.LESSON_VOCAB) {
        initMatchGame();
    }

    // 5. Interactive Hero Mascot (Lingo) click reaction
    const heroMascotWrap = document.getElementById('heroMascotWrap');
    if (heroMascotWrap) {
        heroMascotWrap.addEventListener('click', () => {
            const owl = document.getElementById('heroOwlMascot') || heroMascotWrap.querySelector('.owl');
            if (window.Owl && owl) Owl.play(owl, 'celebrate', 2200);
            if (typeof SoundFX !== 'undefined') SoundFX.click();
            speakUzbek('Assalomu alaykum! Men Lingoman — sizning o‘zbek tili do‘stingiz. Birga o‘rganamiz!');
        });
    }

    // 5. Initialize Interactive Scene Player
    if (document.getElementById('playerCanvas') && window.LESSON_VOCAB) {
        initScenePlayer();
    }

    // 6. Confetti & Victory sound on result page
    if (document.querySelector('.result-card.passed-card')) {
        SoundFX.fanfare();
        triggerConfetti();
    } else if (document.querySelector('.result-card.failed-card')) {
        SoundFX.encourage();
    }
});

/**
 * Interactive Video Scene Player
 */
let currentPhraseIdx = 0;
let scenePhrases = [];

function initScenePlayer() {
    if (!window.LESSON_VOCAB || window.LESSON_VOCAB.length === 0) return;

    scenePhrases = window.LESSON_VOCAB.slice(0, 5).map(v => ({
        uz: v.example || v.word_uz,
        en: v.word_en,
        word: v.word_uz
    }));

    updateScene();
}

function updateScene() {
    if (scenePhrases.length === 0) return;
    const item = scenePhrases[currentPhraseIdx];

    const phraseEl = document.getElementById('scenePhrase');
    const subEl = document.getElementById('sceneSub');
    const indicatorEl = document.getElementById('phraseIndicator');

    if (phraseEl) phraseEl.innerText = '«' + item.uz + '»';
    if (subEl) subEl.innerText = item.en;
    if (indicatorEl) indicatorEl.innerText = `${currentPhraseIdx + 1} / ${scenePhrases.length}`;
}

function nextPhrase() {
    if (scenePhrases.length === 0) return;
    currentPhraseIdx = (currentPhraseIdx + 1) % scenePhrases.length;
    updateScene();
    SoundFX.click();
    speakCurrentPhrase();
}

function prevPhrase() {
    if (scenePhrases.length === 0) return;
    currentPhraseIdx = (currentPhraseIdx - 1 + scenePhrases.length) % scenePhrases.length;
    updateScene();
    SoundFX.click();
    speakCurrentPhrase();
}

function speakCurrentPhrase() {
    if (scenePhrases[currentPhraseIdx]) {
        speakUzbek(scenePhrases[currentPhraseIdx].word || scenePhrases[currentPhraseIdx].uz);
    }
}

/**
 * Interactive Word-Match Game with Coins and Chimes
 */
let firstCard = null;
let lockBoard = false;
let matchedCount = 0;
let totalPairs = 0;
let scoreCoins = 0;

function initMatchGame() {
    const grid = document.getElementById('matchGameGrid');
    if (!grid || !window.LESSON_VOCAB) return;

    grid.innerHTML = '';
    matchedCount = 0;
    firstCard = null;
    lockBoard = false;
    scoreCoins = 0;

    const vocabSlice = [...window.LESSON_VOCAB].sort(() => Math.random() - 0.5).slice(0, 5);
    totalPairs = vocabSlice.length;

    const matchedEl = document.getElementById('matchedPairsCount');
    const totalEl = document.getElementById('totalPairsCount');
    const winBanner = document.getElementById('gameWinBanner');
    const coinEl = document.getElementById('gameCoinCount');

    if (matchedEl) matchedEl.innerText = '0';
    if (totalEl) totalEl.innerText = totalPairs;
    if (winBanner) winBanner.style.display = 'none';
    if (coinEl) coinEl.innerText = '0';

    const cards = [];
    vocabSlice.forEach((item, index) => {
        cards.push({ id: index, type: 'uz', text: item.word_uz, sound: item.word_uz });
        cards.push({ id: index, type: 'en', text: item.word_en, sound: item.word_uz });
    });

    cards.sort(() => Math.random() - 0.5);

    cards.forEach(c => {
        const div = document.createElement('div');
        div.className = 'match-card';
        div.innerText = c.text;
        div.dataset.id = c.id;
        div.dataset.type = c.type;
        div.dataset.sound = c.sound;

        div.addEventListener('click', () => handleCardClick(div));
        grid.appendChild(div);
    });
}

function handleCardClick(card) {
    if (lockBoard) return;
    if (card === firstCard) return;
    if (card.classList.contains('matched')) return;

    SoundFX.click();
    if (card.dataset.sound) {
        speakUzbek(card.dataset.sound);
    }

    card.classList.add('selected');

    if (!firstCard) {
        firstCard = card;
        return;
    }

    const isMatch = firstCard.dataset.id === card.dataset.id && firstCard.dataset.type !== card.dataset.type;

    if (isMatch) {
        firstCard.classList.remove('selected');
        card.classList.remove('selected');
        firstCard.classList.add('matched');
        card.classList.add('matched');

        matchedCount++;
        scoreCoins += 10;

        SoundFX.coin();

        const matchedEl = document.getElementById('matchedPairsCount');
        const coinEl = document.getElementById('gameCoinCount');
        if (matchedEl) matchedEl.innerText = matchedCount;
        if (coinEl) coinEl.innerText = scoreCoins;

        firstCard = null;

        if (matchedCount === totalPairs) {
            setTimeout(() => {
                SoundFX.match();
                const winBanner = document.getElementById('gameWinBanner');
                if (winBanner) winBanner.style.display = 'block';
                document.dispatchEvent(new CustomEvent('lingo:game-won'));
            }, 300);
        }
    } else {
        lockBoard = true;
        SoundFX.encourage();
        setTimeout(() => {
            firstCard.classList.remove('selected');
            card.classList.remove('selected');
            firstCard = null;
            lockBoard = false;
        }, 800);
    }
}

/**
 * Quiz Question Selection UI Helper
 */
function selectOption(qId, oIdx) {
    SoundFX.click();
    const block = document.getElementById('q_block_' + qId);
    if (!block) return;

    const labels = block.querySelectorAll('.q-option-label');
    labels.forEach((label, idx) => {
        if (idx === oIdx) {
            label.classList.add('active');
        } else {
            label.classList.remove('active');
        }
    });
}

/**
 * Enrollment Form Password Toggle
 */
function togglePasswordField() {
    SoundFX.click();
    const check = document.getElementById('createAccountCheck');
    const group = document.getElementById('passwordGroup');
    if (check && group) {
        group.style.display = check.checked ? 'block' : 'none';
    }
}

/**
 * Lightweight Celebration Confetti
 */
function triggerConfetti() {
    const canvas = document.createElement('canvas');
    canvas.style.position = 'fixed';
    canvas.style.top = '0';
    canvas.style.left = '0';
    canvas.style.width = '100vw';
    canvas.style.height = '100vh';
    canvas.style.pointerEvents = 'none';
    canvas.style.zIndex = '9999';
    document.body.appendChild(canvas);

    const ctx = canvas.getContext('2d');
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;

    const pieces = [];
    const colors = ['#059669', '#2563eb', '#f59e0b', '#ec4899', '#8b5cf6', '#10b981'];

    for (let i = 0; i < 80; i++) {
        pieces.push({
            x: Math.random() * canvas.width,
            y: Math.random() * canvas.height - canvas.height,
            size: Math.random() * 10 + 6,
            color: colors[Math.floor(Math.random() * colors.length)],
            speed: Math.random() * 4 + 3,
            rotation: Math.random() * 360,
            rotationSpeed: Math.random() * 6 - 3
        });
    }

    let frame = 0;
    function render() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        pieces.forEach(p => {
            p.y += p.speed;
            p.rotation += p.rotationSpeed;
            ctx.save();
            ctx.translate(p.x, p.y);
            ctx.rotate((p.rotation * Math.PI) / 180);
            ctx.fillStyle = p.color;
            ctx.fillRect(-p.size / 2, -p.size / 2, p.size, p.size);
            ctx.restore();
        });

        frame++;
        if (frame < 180) {
            requestAnimationFrame(render);
        } else {
            canvas.remove();
        }
    }
    render();
}

/**
 * Responsive Mobile Drawer Navigation
 */
function initMobileDrawer() {
    const toggleBtn = document.getElementById('menuToggle');
    const drawer = document.getElementById('mobileDrawer');
    const backdrop = document.getElementById('drawerBackdrop');
    const closeBtn = document.getElementById('drawerClose');

    if (!toggleBtn || !drawer || !backdrop) return;

    function openDrawer() {
        if (typeof SoundFX !== 'undefined') SoundFX.click();
        drawer.classList.add('open');
        backdrop.classList.add('open');
        document.body.classList.add('drawer-open');
    }

    function closeDrawer() {
        drawer.classList.remove('open');
        backdrop.classList.remove('open');
        document.body.classList.remove('drawer-open');
    }

    toggleBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        openDrawer();
    });

    if (closeBtn) {
        closeBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (typeof SoundFX !== 'undefined') SoundFX.click();
            closeDrawer();
        });
    }

    backdrop.addEventListener('click', () => {
        if (typeof SoundFX !== 'undefined') SoundFX.click();
        closeDrawer();
    });

    // Close on escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && drawer.classList.contains('open')) {
            closeDrawer();
        }
    });

    // Close when clicking any link inside drawer
    const links = drawer.querySelectorAll('a');
    links.forEach(link => {
        link.addEventListener('click', () => {
            closeDrawer();
        });
    });
}

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initMobileDrawer);
} else {
    initMobileDrawer();
}

// Any [data-speak] element reads its text aloud (the lesson page has its own richer handler).
document.addEventListener('click', (e) => {
    const el = e.target.closest('[data-speak]');
    if (el) speakUzbek(el.dataset.speak);
});

// ============================================================================
// Uzbek Speech Recognition & Pronunciation Evaluator (Web Speech API)
// ============================================================================

function normalizeSpeech(s) {
    if (!s) return '';
    s = String(s).toLowerCase().trim();
    // Convert Cyrillic Uzbek to Latin if speech engine outputs Cyrillic
    const cyr = {
        'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'ғ': 'ğ', 'д': 'd',
        'е': 'e', 'ё': 'yo', 'ж': 'j', 'з': 'z', 'и': 'i', 'й': 'y',
        'к': 'k', 'қ': 'q', 'л': 'l', 'м': 'm', 'н': 'n', 'о': 'o',
        'п': 'p', 'р': 'r', 'с': 's', 'т': 't', 'у': 'u', 'ў': 'ö',
        'ф': 'f', 'х': 'h', 'ҳ': 'h', 'ч': 'ç', 'ш': 'ş', 'ъ': '',
        'ь': '', 'э': 'e', 'ю': 'yu', 'я': 'ya'
    };
    for (const [c, l] of Object.entries(cyr)) {
        s = s.replaceAll(c, l);
    }
    s = s.replace(/o['‘`ʻ’]/g, 'ö')
         .replace(/g['‘`ʻ’]/g, 'ğ')
         .replace(/sh/g, 'ş')
         .replace(/ch/g, 'ç')
         .replace(/x/g, 'h'); // smooth x and h for speech recognition
    s = s.replace(/[.,!?«»"“”:;()\[\]\-_/\\]/g, '');
    return s.replace(/\s+/g, ' ').trim();
}

function levenshteinDistance(a, b) {
    const m = a.length, n = b.length;
    if (m === 0) return n;
    if (n === 0) return m;
    const dp = Array.from({ length: m + 1 }, () => new Array(n + 1).fill(0));
    for (let i = 0; i <= m; i++) dp[i][0] = i;
    for (let j = 0; j <= n; j++) dp[0][j] = j;
    for (let i = 1; i <= m; i++) {
        for (let j = 1; j <= n; j++) {
            const cost = a[i - 1] === b[j - 1] ? 0 : 1;
            dp[i][j] = Math.min(
                dp[i - 1][j] + 1,
                dp[i][j - 1] + 1,
                dp[i - 1][j - 1] + cost
            );
        }
    }
    return dp[m][n];
}

function evaluateSpeech(target, recognized, alternatives = []) {
    const candidates = [recognized, ...alternatives].filter(Boolean);
    const normTarget = normalizeSpeech(target);
    if (!normTarget || !candidates.length) {
        return { score: 0, ok: false, normTarget, recognized: recognized || '' };
    }

    let bestScore = 0;
    let bestRec = recognized;

    for (const cand of candidates) {
        const normCand = normalizeSpeech(cand);
        if (normCand === normTarget) {
            bestScore = 100;
            bestRec = cand;
            break;
        }
        if (normCand.includes(normTarget) || normTarget.includes(normCand)) {
            const sc = Math.min(100, Math.round((Math.min(normTarget.length, normCand.length) / Math.max(normTarget.length, normCand.length)) * 100) + 15);
            if (sc > bestScore) {
                bestScore = Math.min(98, sc);
                bestRec = cand;
            }
        }
        const maxLen = Math.max(normTarget.length, normCand.length);
        const dist = levenshteinDistance(normTarget, normCand);
        const sim = Math.max(0, (maxLen - dist) / maxLen);
        const sc = Math.round(sim * 100);
        if (sc > bestScore) {
            bestScore = sc;
            bestRec = cand;
        }
    }

    const ok = bestScore >= 70; // 70% match is passing
    return {
        score: bestScore,
        ok,
        normTarget,
        recognized: bestRec,
        target
    };
}

class SpeechChecker {
    constructor() {
        const SpeechRec = window.SpeechRecognition || window.webkitSpeechRecognition;
        this.isSupported = !!SpeechRec;
        this.activeRecognition = null;
    }

    start({ onStart, onResult, onError, onEnd, lang = 'uz-UZ' } = {}) {
        if (!this.isSupported) {
            onError && onError({ error: 'not_supported', message: 'Brauzeringiz ovoz tanishni qo‘llab-quvvatlamaydi' });
            return null;
        }
        if (this.activeRecognition) {
            try { this.activeRecognition.abort(); } catch (_) {}
        }
        const SpeechRec = window.SpeechRecognition || window.webkitSpeechRecognition;
        const rec = new SpeechRec();
        rec.lang = lang;
        rec.continuous = false;
        rec.interimResults = false;
        rec.maxAlternatives = 5;

        let hasResult = false;

        rec.onstart = () => {
            onStart && onStart();
        };

        rec.onresult = (event) => {
            hasResult = true;
            const alternatives = [];
            for (let i = 0; i < event.results[0].length; i++) {
                alternatives.push(event.results[0][i].transcript);
            }
            const primary = event.results[0][0].transcript;
            onResult && onResult(primary, alternatives);
        };

        rec.onerror = (e) => {
            onError && onError(e);
        };

        rec.onend = () => {
            this.activeRecognition = null;
            onEnd && onEnd(hasResult);
        };

        try {
            rec.start();
            this.activeRecognition = rec;
        } catch (err) {
            onError && onError(err);
        }
        return rec;
    }

    stop() {
        if (this.activeRecognition) {
            try { this.activeRecognition.stop(); } catch (_) {}
            this.activeRecognition = null;
        }
    }
}

window.normalizeSpeech = normalizeSpeech;
window.levenshteinDistance = levenshteinDistance;
window.evaluateSpeech = evaluateSpeech;
window.SpeechChecker = SpeechChecker;
window.speechChecker = new SpeechChecker();

function showSpeechToast(message, type = 'info', duration = 3500) {
    let toast = document.getElementById('lingoSpeechToast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'lingoSpeechToast';
        toast.className = 'speech-toast';
        document.body.appendChild(toast);
    }
    toast.className = 'speech-toast ' + (type === 'success' ? 'is-success' : type === 'retry' ? 'is-retry' : '');
    toast.innerHTML = message;
    toast.classList.add('show');
    clearTimeout(toast._timer);
    toast._timer = setTimeout(() => {
        toast.classList.remove('show');
    }, duration);
}
window.showSpeechToast = showSpeechToast;

// Global handler for [data-check-speech] buttons
document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-check-speech]');
    if (!btn) return;
    e.stopPropagation();

    const targetWord = btn.dataset.checkSpeech;
    if (!targetWord) return;

    if (!window.speechChecker.isSupported) {
        showSpeechToast('⚠️ Brauzeringiz mikrofon orqali ovozni tanishni qo‘llab-quvvatlamaydi (Chrome yoki Edge tavsiya etiladi).', 'retry', 4000);
        return;
    }

    if (btn.classList.contains('is-recording')) {
        window.speechChecker.stop();
        btn.classList.remove('is-recording');
        return;
    }

    btn.classList.add('is-recording');
    showSpeechToast('🎙️ Tinglanmoqda... «<strong>' + targetWord + '</strong>» deb ayting!', 'info', 5000);

    window.speechChecker.start({
        lang: 'uz-UZ',
        onStart: () => {},
        onResult: (primary, alternatives) => {
            btn.classList.remove('is-recording');
            const res = evaluateSpeech(targetWord, primary, alternatives);
            if (res.ok) {
                showSpeechToast(`🎉 <strong>${res.score}% moslik!</strong> Ajoyib talaffuz: «${res.recognized}»`, 'success', 3500);
                if (typeof SoundFX !== 'undefined') SoundFX.coin();
                const owl = document.querySelector('[data-owl]');
                if (window.Owl && owl) Owl.play(owl, 'celebrate', 1500);
            } else {
                showSpeechToast(`🤔 <strong>${res.score}%</strong>. Eshitildi: «${res.recognized}». Qayta urinib ko‘ring!`, 'retry', 3500);
                if (typeof SoundFX !== 'undefined') SoundFX.encourage();
                const owl = document.querySelector('[data-owl]');
                if (window.Owl && owl) Owl.play(owl, 'encourage', 1500);
            }
        },
        onError: (err) => {
            btn.classList.remove('is-recording');
            if (err.error === 'no-speech') {
                showSpeechToast('Ovoz eshitilmadi. Qayta urinib ko‘ring.', 'retry', 2500);
            } else if (err.error === 'not-allowed') {
                showSpeechToast('⚠️ Mikrofonga ruxsat berilmadi. Iltimos, brauzerda mikrofonni yoqing.', 'retry', 3500);
            } else {
                showSpeechToast('Xatolik yuz berdi. Qayta urinib ko‘ring.', 'retry', 2500);
            }
        },
        onEnd: () => {
            btn.classList.remove('is-recording');
        }
    });
});
