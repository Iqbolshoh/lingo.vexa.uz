/**
 * LingoUz by Gulomov's Academy - Interactive Client Features
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Mobile Menu Toggle
    const menuToggle = document.getElementById('menuToggle');
    const mainNav = document.getElementById('mainNav');
    if (menuToggle && mainNav) {
        menuToggle.addEventListener('click', () => {
            mainNav.classList.toggle('open');
        });
    }

    // 2. Initialize Matching Game on Lesson Page
    if (document.getElementById('matchGameGrid') && window.LESSON_VOCAB) {
        initMatchGame();
    }

    // 3. Initialize Interactive Scene Player on Lesson Page
    if (document.getElementById('playerCanvas') && window.LESSON_VOCAB) {
        initScenePlayer();
    }
});

/**
 * Uzbek Speech Synthesis via Web Speech API
 */
function speakUzbek(text) {
    if (!('speechSynthesis' in window)) {
        alert("Brauzeringiz ovozli o‘qishni qo‘llab-quvvatlamaydi.");
        return;
    }

    window.speechSynthesis.cancel(); // Stop any pending speech
    const utterance = new SpeechSynthesisUtterance(text);
    utterance.rate = 0.85; // slightly slower for young learners
    utterance.pitch = 1.1; // friendly tone

    // Try finding an Uzbek or compatible voice
    const voices = window.speechSynthesis.getVoices();
    const uzVoice = voices.find(v => v.lang.startsWith('uz') || v.lang.includes('uz_UZ'));
    if (uzVoice) {
        utterance.voice = uzVoice;
    } else {
        utterance.lang = 'uz-UZ';
    }

    window.speechSynthesis.speak(utterance);
}

/**
 * Interactive Video Scene Player
 */
let currentPhraseIdx = 0;
let scenePhrases = [];

function initScenePlayer() {
    if (!window.LESSON_VOCAB || window.LESSON_VOCAB.length === 0) return;

    scenePhrases = window.LESSON_VOCAB.slice(0, 5).map(v => ({
        emoji: v.icon || '🌟',
        uz: v.example || v.word_uz,
        en: v.word_en,
        word: v.word_uz
    }));

    updateScene();
}

function updateScene() {
    if (scenePhrases.length === 0) return;
    const item = scenePhrases[currentPhraseIdx];

    const avatarEl = document.getElementById('avatarEmoji');
    const phraseEl = document.getElementById('scenePhrase');
    const subEl = document.getElementById('sceneSub');
    const indicatorEl = document.getElementById('phraseIndicator');

    if (avatarEl) avatarEl.innerText = item.emoji;
    if (phraseEl) phraseEl.innerText = '«' + item.uz + '»';
    if (subEl) subEl.innerText = item.en;
    if (indicatorEl) indicatorEl.innerText = `${currentPhraseIdx + 1} / ${scenePhrases.length}`;
}

function nextPhrase() {
    if (scenePhrases.length === 0) return;
    currentPhraseIdx = (currentPhraseIdx + 1) % scenePhrases.length;
    updateScene();
    speakCurrentPhrase();
}

function prevPhrase() {
    if (scenePhrases.length === 0) return;
    currentPhraseIdx = (currentPhraseIdx - 1 + scenePhrases.length) % scenePhrases.length;
    updateScene();
    speakCurrentPhrase();
}

function speakCurrentPhrase() {
    if (scenePhrases[currentPhraseIdx]) {
        speakUzbek(scenePhrases[currentPhraseIdx].word || scenePhrases[currentPhraseIdx].uz);
    }
}

/**
 * Interactive Word-Match Game
 */
let matchCards = [];
let firstCard = null;
let lockBoard = false;
let matchedCount = 0;
let totalPairs = 0;

function initMatchGame() {
    const grid = document.getElementById('matchGameGrid');
    if (!grid || !window.LESSON_VOCAB) return;

    grid.innerHTML = '';
    matchedCount = 0;
    firstCard = null;
    lockBoard = false;

    // Pick 4 words from vocab
    const vocabSlice = window.LESSON_VOCAB.slice(0, 4);
    totalPairs = vocabSlice.length;

    const matchedEl = document.getElementById('matchedPairsCount');
    const totalEl = document.getElementById('totalPairsCount');
    const winBanner = document.getElementById('gameWinBanner');

    if (matchedEl) matchedEl.innerText = '0';
    if (totalEl) totalEl.innerText = totalPairs;
    if (winBanner) winBanner.style.display = 'none';

    // Build pairs
    const cards = [];
    vocabSlice.forEach((item, index) => {
        cards.push({ id: index, type: 'uz', text: item.word_uz, sound: item.word_uz });
        cards.push({ id: index, type: 'en', text: item.word_en, sound: item.word_uz });
    });

    // Shuffle cards
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

    // Pronounce word when clicked
    if (card.dataset.sound) {
        speakUzbek(card.dataset.sound);
    }

    card.classList.add('selected');

    if (!firstCard) {
        firstCard = card;
        return;
    }

    // Second card clicked: check match
    const isMatch = firstCard.dataset.id === card.dataset.id && firstCard.dataset.type !== card.dataset.type;

    if (isMatch) {
        firstCard.classList.remove('selected');
        card.classList.remove('selected');
        firstCard.classList.add('matched');
        card.classList.add('matched');

        matchedCount++;
        const matchedEl = document.getElementById('matchedPairsCount');
        if (matchedEl) matchedEl.innerText = matchedCount;

        firstCard = null;

        if (matchedCount === totalPairs) {
            const winBanner = document.getElementById('gameWinBanner');
            if (winBanner) winBanner.style.display = 'block';
        }
    } else {
        lockBoard = true;
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
    const check = document.getElementById('createAccountCheck');
    const group = document.getElementById('passwordGroup');
    if (check && group) {
        group.style.display = check.checked ? 'block' : 'none';
    }
}
