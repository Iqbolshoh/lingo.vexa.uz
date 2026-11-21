<?php
$readingUz = array_values(array_filter(preg_split('/\R/u', (string) $lesson['reading_uz']), fn ($l) => trim($l) !== ''));
$readingEn = array_values(array_filter(preg_split('/\R/u', (string) $lesson['reading_en']), fn ($l) => trim($l) !== ''));
$hasReading = $readingUz || !empty($lesson['culture_note']);
$best = (int) ($currentProgress['best_score'] ?? 0);
$pass = (int) $lesson['passing_score'];
$kindLabels = ['choice' => 'Variantni tanlang', 'listen' => 'Tinglang', 'build' => 'Gap tuzing', 'fill' => 'Yozing'];
$kindIcons = ['choice' => 'check-circle', 'listen' => 'volume-2', 'build' => 'layers', 'fill' => 'message-square'];
$steps = [['sahna', 'Sahna'], ['lugat', 'Lug‘at']];
if ($hasReading) { $steps[] = ['oqish', 'O‘qish']; }
$steps[] = ['oyin', 'O‘yin'];
$steps[] = ['mashq', 'Mashq'];
$steps[] = ['test', 'Test'];
$n = 0;
?>
<div class="pg">
    <div class="container">
        <section class="pg-hero theme-<?= e($course['theme'] ?? 'lapis') ?>">
            <div class="hero-split">
                <div>
                    <div class="crumbs">
                        <a href="/courses/<?= (int) $course['id'] ?>"><?= e($course['title']) ?></a> <span>/</span>
                        <span><?= (int) $lesson['position'] ?>-dars / <?= $lessonCount ?></span>
                    </div>
                    <h1><?= e($lesson['title']) ?></h1>
                    <p style="font-style:italic;color:#a8cbe3;margin-bottom:6px"><?= e($lesson['title_en']) ?></p>
                    <p><strong style="color:#fde68a">Maqsad:</strong> <?= e($lesson['objective']) ?></p>
                    <div class="xp-strip">
                        <span class="xp-chip"><?= icon('clock', '', 16) ?> <?= (int) $lesson['duration_min'] ?> daqiqa</span>
                        <span class="xp-chip"><?= icon('book-open', '', 16) ?> <?= count($vocabulary) ?> so‘z</span>
                        <span class="xp-chip"><?= icon('check-circle', '', 16) ?> <?= count($questions) ?> savol · <?= $pass ?>%</span>
                    </div>
                </div>
                <div class="ring" style="--p: <?= $best ?>">
                    <div><strong><?= $best ?>%</strong><span><?= $best >= $pass ? 'o‘zlashtirildi' : 'eng yaxshi natija' ?></span></div>
                </div>
            </div>
        </section>

        <nav class="lesson-steps" aria-label="Dars bosqichlari">
            <?php foreach ($steps as $i => [$id, $label]): ?>
                <a href="#<?= $id ?>"><b><?= $i + 1 ?></b> <?= $label ?></a>
            <?php endforeach; ?>
        </nav>

        <!-- 1. Scene / video -->
        <section class="l-section" id="sahna">
            <div class="l-head"><span class="l-num"><?= ++$n ?></span><div><h2>Mikro-sahna</h2><p>Lingo bilan birga iboralarni tinglang va baland ovozda takrorlang.</p></div></div>
            <?php if (!empty($lesson['video_url'])): ?>
                <div class="video-frame" style="margin-bottom:16px">
                    <?php if (preg_match('~\.(mp4|webm)$~i', $lesson['video_url'])): ?>
                        <video src="<?= e($lesson['video_url']) ?>" controls preload="metadata"></video>
                    <?php else: ?>
                        <iframe src="<?= e($lesson['video_url']) ?>" title="<?= e($lesson['title']) ?>" loading="lazy" allow="accelerometer; encrypted-media; picture-in-picture" allowfullscreen></iframe>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <div class="video-container-card registon-video-card">
                <div class="interactive-video-player">
                    <div class="player-canvas" id="playerCanvas">
                        <div class="player-scene">
                            <div class="scene-avatar" id="avatarEmoji"><img src="/assets/mascot-lingo.svg" alt="Lingo" width="80" height="80"></div>
                            <h3 class="scene-phrase" id="scenePhrase">«Salom!»</h3>
                            <p class="scene-sub" id="sceneSub">Hello!</p>
                            <button class="btn btn-accent btn-sm" type="button" onclick="speakCurrentPhrase()"><?= icon('volume-2', '', 16) ?> Ovozni eshitish</button>
                        </div>
                        <div class="player-controls">
                            <button class="btn btn-light btn-sm" type="button" onclick="prevPhrase()"><?= icon('arrow-left', '', 14) ?> Oldingisi</button>
                            <span class="phrase-indicator" id="phraseIndicator">1 / 1</span>
                            <button class="btn btn-light btn-sm" type="button" onclick="nextPhrase()">Keyingisi <?= icon('arrow-right', '', 14) ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 2. Vocabulary -->
        <section class="l-section" id="lugat">
            <div class="l-head"><span class="l-num"><?= ++$n ?></span><div><h2>Ovozli lug‘at</h2><p>Kartani bosing — ag‘dariladi. Karnaycha — talaffuz.</p></div></div>
            <div class="vocab-cards-grid">
                <?php foreach ($vocabulary as $v): ?>
                    <div class="vocab-card-flipper" title="Ag‘darish uchun bosing">
                        <div class="flipper-inner">
                            <div class="flipper-front">
                                <div class="vocab-card-header">
                                    <span class="vocab-icon-wrapper"><?= icon($lesson['icon'], '', 22) ?></span>
                                    <button class="btn-audio-circle" type="button" data-speak="<?= e($v['word_uz']) ?>" title="Eshitish"><?= icon('volume-2', '', 16) ?></button>
                                </div>
                                <h3 class="vocab-title-uz"><?= e($v['word_uz']) ?></h3>
                                <?php if (!empty($v['pronunciation'])): ?><div class="vocab-phonetic-badge">[<?= e($v['pronunciation']) ?>]</div><?php endif; ?>
                                <div class="flip-hint-badge"><?= icon('rotate-cw', '', 11) ?> Ag‘darish</div>
                            </div>
                            <div class="flipper-back">
                                <div class="back-title">English:</div>
                                <div class="back-meaning"><?= e($v['word_en']) ?></div>
                                <?php if (!empty($v['example'])): ?><div class="back-example">«<?= e($v['example']) ?>»</div><?php endif; ?>
                                <button class="btn btn-sm btn-light btn-audio-circle" type="button" data-speak="<?= e($v['example'] ?: $v['word_uz']) ?>" style="margin-top:10px;width:auto;padding:6px 12px;border-radius:10px"><?= icon('volume-2', '', 14) ?> Misolni eshitish</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- 3. Reading + culture -->
        <?php if ($hasReading): ?>
            <section class="l-section" id="oqish">
                <div class="l-head"><span class="l-num"><?= ++$n ?></span><div><h2>O‘qish va madaniyat</h2><p>Har bir gapni bosing — Lingo uni o‘qib beradi. So‘ng o‘zingiz baland ovozda takrorlang.</p></div></div>
                <div class="reading-card">
                    <?php if ($readingUz): ?>
                        <div class="reading-text" id="readingText">
                            <div class="reading-tools">
                                <button type="button" class="btn btn-sm btn-accent" id="readAll"><?= icon('play', '', 14) ?> Hammasini o‘qish</button>
                                <button type="button" class="btn btn-sm btn-ghost" id="toggleEn"><?= icon('message-square', '', 14) ?> Tarjimani ko‘rsatish</button>
                            </div>
                            <?php foreach ($readingUz as $i => $line): ?>
                                <button type="button" class="read-line" data-speak="<?= e($line) ?>">
                                    <span class="spk"><?= icon('volume-2', '', 14) ?></span>
                                    <span><span class="uz"><?= e($line) ?></span><?php if (!empty($readingEn[$i])): ?><span class="en"><?= e($readingEn[$i]) ?></span><?php endif; ?></span>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($lesson['culture_note'])): ?>
                        <aside class="culture-card">
                            <div class="c-kicker"><?= icon('sparkles', '', 14) ?> Bilasizmi?</div>
                            <h3><?= e($lesson['culture_title'] ?: 'Madaniyat burchagi') ?></h3>
                            <p><?= e($lesson['culture_note']) ?></p>
                            <div class="flag-pair"><span>O‘zbekiston</span><span>Amerika</span></div>
                        </aside>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>

        <!-- 4. Match game -->
        <section class="l-section" id="oyin">
            <div class="l-head"><span class="l-num"><?= ++$n ?></span><div><h2>Juftlik o‘yini</h2><p>O‘zbekcha so‘zni va uning inglizcha ma’nosini toping. Birinchi g‘alaba uchun +<?= GAME_COINS ?> tanga!</p></div></div>
            <div class="game-box-card registon-game-box">
                <div class="game-score-row">
                    <div>
                        <span>Juftliklar: <strong id="matchedPairsCount">0</strong> / <span id="totalPairsCount">0</span></span>
                        <span class="coin-reward-pill" style="margin-left:14px"><?= icon('coins', '', 14) ?> <strong id="gameCoinCount">0</strong></span>
                    </div>
                    <button class="btn btn-outline btn-sm" type="button" onclick="initMatchGame()"><?= icon('rotate-cw', '', 14) ?> Qayta</button>
                </div>
                <div class="match-game-grid" id="matchGameGrid"></div>
                <div class="game-win-banner" id="gameWinBanner" style="display:none;">
                    <?= icon('trophy', '', 20) ?> <strong>Ajoyib!</strong> <span id="gameReward"><?= !empty($currentProgress['game_done']) ? 'Siz bu o‘yinni avval ham yutgansiz.' : '' ?></span>
                </div>
            </div>
        </section>

        <!-- 5. Practice (instant feedback, not graded) -->
        <section class="l-section" id="mashq">
            <div class="l-head"><span class="l-num"><?= ++$n ?></span><div><h2>Tezkor mashq</h2><p>Tinglang, tanlang, yozing — har javobdan keyin darhol bilib olasiz. Bu baholanmaydi, bemalol xato qiling!</p></div></div>
            <div class="ex-shell" id="practice">
                <div class="ex-top"><div class="bar"><i style="width:0%"></i></div><span class="ex-count">0 / 0</span></div>
                <div class="ex-body"></div>
                <div class="ex-foot">
                    <div class="ex-feedback"></div>
                    <button type="button" class="btn btn-check" data-act="check" disabled>Tekshirish</button>
                </div>
            </div>
        </section>

        <!-- 6. Final test (server graded, stepper) -->
        <section class="l-section" id="test">
            <div class="l-head"><span class="l-num"><?= ++$n ?></span><div><h2>Yakuniy test</h2><p>Keyingi dars ochilishi uchun kamida <strong><?= $pass ?>%</strong> to‘plang. Javoblar oxirida tekshiriladi.</p></div></div>
            <?php if (!$questions): ?>
                <div class="card empty"><div class="e-ico"><?= icon('check-circle', '', 26) ?></div>Bu darsga test savollari hali qo‘shilmagan.</div>
            <?php else: ?>
                <form action="/lessons/<?= (int) $lesson['id'] ?>/quiz" method="POST" class="ex-shell" id="quizStepper">
                    <?= csrf_field() ?>
                    <div class="ex-top"><div class="bar"><i style="width:0%"></i></div><span class="ex-count">1 / <?= count($questions) ?></span></div>
                    <div class="ex-body">
                        <?php foreach ($questions as $i => $q): $type = $q['type']; ?>
                            <div class="q-step <?= $i === 0 ? 'is-active' : '' ?>" data-type="<?= e($type) ?>">
                                <span class="ex-kind"><?= icon($kindIcons[$type] ?? 'check-circle', '', 13) ?> <?= $kindLabels[$type] ?? 'Savol' ?></span>
                                <?php if ($type === 'build'): ?>
                                    <h3 class="ex-prompt"><?= e($q['prompt_en'] ?: $q['prompt']) ?></h3>
                                    <p class="ex-sub"><?= e($q['prompt']) ?></p>
                                    <div class="build-answer" aria-live="polite"></div>
                                    <div class="build-bank">
                                        <?php foreach ($q['tiles'] as $t): ?><button type="button" class="tile"><?= e($t) ?></button><?php endforeach; ?>
                                    </div>
                                    <input type="hidden" name="q_<?= (int) $q['id'] ?>" value="">
                                <?php elseif ($type === 'fill'): ?>
                                    <h3 class="ex-prompt"><?= e($q['prompt']) ?></h3>
                                    <?php if ($q['prompt_en']): ?><p class="ex-sub"><?= e($q['prompt_en']) ?></p><?php endif; ?>
                                    <input class="fill-input" type="text" name="q_<?= (int) $q['id'] ?>" autocomplete="off" autocapitalize="off" spellcheck="false" placeholder="Javobni o‘zbekcha yozing…">
                                    <div class="char-keys"><button type="button" data-char="o‘">o‘</button><button type="button" data-char="g‘">g‘</button><button type="button" data-char="sh">sh</button><button type="button" data-char="ch">ch</button></div>
                                <?php else: ?>
                                    <?php if ($type === 'listen'): ?>
                                        <h3 class="ex-prompt"><?= e($q['prompt']) ?></h3>
                                        <div class="listen-row">
                                            <button type="button" class="listen-big" data-speak="<?= e($q['audio_text']) ?>" aria-label="Tinglash"><?= icon('volume-2', '', 40) ?></button>
                                            <button type="button" class="listen-big listen-slow" data-speak="<?= e($q['audio_text']) ?>" data-slow="1" aria-label="Sekin tinglash" title="Sekin"><?= icon('clock', '', 22) ?></button>
                                        </div>
                                    <?php else: ?>
                                        <h3 class="ex-prompt"><?= e($q['prompt']) ?></h3>
                                        <?php if ($q['prompt_en']): ?><p class="ex-sub"><?= e($q['prompt_en']) ?></p><?php endif; ?>
                                    <?php endif; ?>
                                    <div class="ex-options">
                                        <?php foreach ($q['options'] as $oi => $opt): ?>
                                            <label class="ex-opt">
                                                <input type="radio" name="q_<?= (int) $q['id'] ?>" value="<?= $oi ?>">
                                                <span class="key"><?= chr(65 + $oi) ?></span><span><?= e($opt) ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="ex-foot">
                        <button type="button" class="btn btn-ghost" data-act="prev"><?= icon('arrow-left', '', 16) ?> Orqaga</button>
                        <button type="button" class="btn btn-primary" data-act="next">Keyingisi <?= icon('arrow-right', '', 16) ?></button>
                        <button type="submit" class="btn btn-check" data-act="submit" hidden><?= icon('check-circle', '', 18) ?> Testni yakunlash</button>
                    </div>
                </form>
            <?php endif; ?>
        </section>
    </div>
</div>

<script>
    window.LESSON_VOCAB = <?= json_encode($vocabulary, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;
    window.LESSON_ID = <?= (int) $lesson['id'] ?>;
</script>
<script src="/assets/lesson.js?v=<?= filemtime(__DIR__ . '/../../public/assets/lesson.js') ?>" defer></script>
