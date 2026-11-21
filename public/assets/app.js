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
 * Uzbek Speech Synthesis via Web Speech API
 */
function speakUzbek(text) {
    if (!('speechSynthesis' in window)) return;

    window.speechSynthesis.cancel();
    const utterance = new SpeechSynthesisUtterance(text);
    utterance.rate = 0.85; // pleasant natural pacing for kids
    utterance.pitch = 1.08;

    const voices = window.speechSynthesis.getVoices();
    const uzVoice = voices.find(v => v.lang.startsWith('uz') || v.lang.includes('uz_UZ'));
    if (uzVoice) {
        utterance.voice = uzVoice;
    } else {
        utterance.lang = 'uz-UZ';
    }

    window.speechSynthesis.speak(utterance);
}

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
