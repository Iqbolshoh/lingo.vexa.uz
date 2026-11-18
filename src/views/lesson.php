<div class="lesson-page">
    <div class="container">
        <!-- Top Navigation -->
        <div class="lesson-top-nav">
            <a href="/dashboard" class="btn btn-outline btn-sm">
                ← O‘quvchi paneliga qaytish
            </a>
            <div class="lesson-meta-badge">
                <?= $lesson['sequence_number'] ?>-Dars: <?= e($lesson['title']) ?>
            </div>
        </div>

        <!-- Lesson Header Card -->
        <div class="lesson-header-card">
            <div class="lesson-header-content">
                <span class="lesson-badge-tag">Mavzu <?= $lesson['sequence_number'] ?> • <?= $lesson['duration_min'] ?> daqiqa</span>
                <h1 class="lesson-page-title"><?= e($lesson['title']) ?></h1>
                <p class="lesson-page-en"><?= e($lesson['title_en']) ?></p>
                <div class="lesson-objective-box">
                    <strong>🎯 Ushbu dars maqsadi:</strong> <?= e($lesson['objective']) ?>
                </div>
            </div>

            <?php if ($currentProgress && $currentProgress['best_score'] > 0): ?>
                <div class="lesson-current-score-box">
                    <span class="score-label">Sizning natijangiz:</span>
                    <span class="score-value <?= $currentProgress['best_score'] >= 80 ? 'passed' : 'failed' ?>">
                        <?= $currentProgress['best_score'] ?>%
                    </span>
                    <span class="score-status">
                        <?= $currentProgress['best_score'] >= 80 ? '🏆 O‘zlashtirildi' : '🌱 Yana urinib ko‘ring' ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Step 1: Animated / Audio-Visual Micro-Lesson -->
        <section class="lesson-section" id="video">
            <div class="section-title-wrap">
                <span class="step-badge">1-BOSQICH</span>
                <h2>🎬 Animatsion Mikro-Dars (3–5 daqiqa)</h2>
                <p>Videoni diqqat bilan tomosha qiling va qahramonlar aytgan so‘zlarni birga takrorlang:</p>
            </div>

            <div class="video-container-card">
                <div class="interactive-video-player">
                    <div class="player-canvas" id="playerCanvas">
                        <div class="player-scene">
                            <div class="scene-avatar" id="avatarEmoji">👋</div>
                            <h3 class="scene-phrase" id="scenePhrase">«Salom, xush kelibsiz!»</h3>
                            <p class="scene-sub" id="sceneSub">Hello, welcome!</p>
                            <button class="btn btn-accent btn-sm" onclick="speakCurrentPhrase()">
                                🔊 Ovozni eshitish
                            </button>
                        </div>

                        <div class="player-controls">
                            <button class="btn btn-light btn-sm" onclick="prevPhrase()">⏮️ Oldingisi</button>
                            <span class="phrase-indicator" id="phraseIndicator">1 / 4</span>
                            <button class="btn btn-light btn-sm" onclick="nextPhrase()">Keyingisi ⏭️</button>
                        </div>
                    </div>
                </div>
                <div class="video-tip">
                    💡 <strong>Ota-onalarga maslahat:</strong> Bolangiz bilan birga so‘zlarni baland ovozda qaytaring!
                </div>
            </div>
        </section>

        <!-- Step 2: Visual Vocabulary with Audio Pronunciation -->
        <section class="lesson-section" id="lugat">
            <div class="section-title-wrap">
                <span class="step-badge">2-BOSQICH</span>
                <h2>📚 Vizual Lug‘at (Yangi so‘zlar)</h2>
                <p>Har bir kartochkadagi karnaychani (🔊) bosib, o‘zbekcha talaffuzni tinglang va eslab qoling:</p>
            </div>

            <div class="vocab-cards-grid">
                <?php foreach ($vocabulary as $idx => $v): ?>
                    <div class="vocab-card" id="vocabCard_<?= $v['id'] ?>">
                        <div class="vocab-card-header">
                            <span class="vocab-emoji"><?= $v['icon'] ?></span>
                            <button class="btn-audio-circle" type="button" onclick="speakUzbek('<?= e($v['word_uz']) ?>')" title="Ovozli eshitish">
                                🔊
                            </button>
                        </div>

                        <h3 class="vocab-title-uz"><?= e($v['word_uz']) ?></h3>
                        <div class="vocab-phonetic-badge">[<?= e($v['pronunciation']) ?>]</div>
                        <div class="vocab-meaning-en">English: <strong><?= e($v['word_en']) ?></strong></div>

                        <?php if (!empty($v['example'])): ?>
                            <div class="vocab-example-sentence">
                                💬 «<?= e($v['example']) ?>»
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Step 3: Interactive Matching Game -->
        <section class="lesson-section" id="oyin">
            <div class="section-title-wrap">
                <span class="step-badge">3-BOSQICH</span>
                <h2>🎮 Interaktiv O‘yin: So‘zlarni Moslashtir!</h2>
                <p>O‘zbekcha so‘zni bosing, so‘ng uning inglizcha ma’nosini toping. Barcha juftliklarni birlashtiring!</p>
            </div>

            <div class="game-box-card">
                <div class="game-score-row">
                    <span>Topilgan juftliklar: <strong id="matchedPairsCount">0</strong> / <span id="totalPairsCount">0</span></span>
                    <button class="btn btn-outline btn-sm" onclick="initMatchGame()">🔄 Qayta boshlash</button>
                </div>

                <div class="match-game-grid" id="matchGameGrid">
                    <!-- Populated by JavaScript from vocabulary array -->
                </div>

                <div class="game-win-banner" id="gameWinBanner" style="display:none;">
                    🎉 <strong>Ajoyib! Barcha juftliklarni to‘g‘ri topdingiz!</strong> Endi yakuniy testga o‘tishingiz mumkin!
                </div>
            </div>
        </section>

        <!-- Step 4: 80% Final Quiz -->
        <section class="lesson-section" id="test">
            <div class="section-title-wrap">
                <span class="step-badge">4-BOSQICH</span>
                <h2>📝 Yakuniy Test (80% Content Locking)</h2>
                <p>Keyingi darsni ochish uchun kamida <strong><?= $quiz['pass_score'] ?>%</strong> (5 tadan 4 ta to‘g‘ri javob) to‘plang:</p>
            </div>

            <div class="quiz-card">
                <form action="/lessons/<?= $lesson['id'] ?>/quiz" method="POST" id="quizForm">
                    <?= csrf_field() ?>

                    <div class="questions-list">
                        <?php foreach ($questions as $qIdx => $q): ?>
                            <div class="question-block" id="q_block_<?= $q['id'] ?>">
                                <div class="q-header">
                                    <span class="q-number"><?= $qIdx + 1 ?>-Savol</span>
                                    <h3 class="q-prompt"><?= e($q['prompt']) ?></h3>
                                    <?php if (!empty($q['prompt_en'])): ?>
                                        <p class="q-prompt-en"><em><?= e($q['prompt_en']) ?></em></p>
                                    <?php endif; ?>
                                </div>

                                <div class="q-options-grid">
                                    <?php foreach ($q['options'] as $oIdx => $opt): ?>
                                        <label class="q-option-label" onclick="selectOption(<?= $q['id'] ?>, <?= $oIdx ?>)">
                                            <input type="radio" name="q_<?= $q['id'] ?>" value="<?= $oIdx ?>" required>
                                            <span class="option-marker"><?= chr(65 + $oIdx) ?></span>
                                            <span class="option-text"><?= e($opt) ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="quiz-submit-bar">
                        <button type="submit" class="btn btn-accent btn-xl btn-block">
                            🚀 Testni Yakunlash va Natijani Ko‘rish
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>

<script>
    // Embed vocabulary data into client-side JS for the interactive matching game and player
    window.LESSON_VOCAB = <?= json_encode($vocabulary, JSON_UNESCAPED_UNICODE) ?>;
</script>
