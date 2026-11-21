<?php
$passScore = (int) $lesson['passing_score'];
$passed = $result['passed'];
?>
<div class="pg">
    <div class="container pg-narrow">
        <section class="pg-hero result-hero <?= $passed ? '' : 'theme-gold' ?>">
            <span class="hero-chip"><?= icon($passed ? 'trophy' : 'sparkles', '', 14) ?> <?= e($lesson['title']) ?></span>
            <div style="margin-top:10px"><?= mascot($passed ? 'celebrate' : 'encourage', 120) ?></div>
            <h1><?= $passed ? 'Barakalla! Dars o‘zlashtirildi' : 'Yaxshi harakat! Yana bir urinish' ?></h1>
            <div class="ring" style="--p: <?= (int) $result['percent'] ?>">
                <div><strong><?= (int) $result['percent'] ?>%</strong><span><?= (int) $result['score'] ?> / <?= (int) $result['total'] ?> to‘g‘ri</span></div>
            </div>
            <div class="xp-strip" style="justify-content:center">
                <span class="xp-chip"><span class="ico-star"><?= icon('star', '', 18) ?></span> +<?= (int) $result['xp'] ?> XP</span>
                <span class="xp-chip"><?= icon('target', '', 16) ?> O‘tish bali: <?= $passScore ?>%</span>
            </div>
            <p style="margin:16px auto 0">
                <?php if ($passed && $nextLesson): ?>
                    «<?= e($nextLesson['title']) ?>» darsi ochildi!
                <?php elseif ($passed): ?>
                    Kursning barcha darslari tugadi — diplomingizni ko‘ring!
                <?php else: ?>
                    Keyingi dars uchun <?= $passScore ?>% kerak. Lug‘at va mashqni yana bir ko‘rib chiqing — albatta uddalaysiz!
                <?php endif; ?>
            </p>
            <div class="dashboard-cta-row" style="justify-content:center;margin-top:18px">
                <?php if ($passed && $nextLesson): ?>
                    <a href="/lessons/<?= (int) $nextLesson['id'] ?>" class="btn btn-gold btn-lg">Keyingi dars <?= icon('arrow-right', '', 18) ?></a>
                <?php elseif ($passed): ?>
                    <a href="/certificate?course=<?= (int) $course['id'] ?>" class="btn btn-gold btn-lg"><?= icon('award', '', 18) ?> Diplomni ochish</a>
                <?php else: ?>
                    <a href="/lessons/<?= (int) $lesson['id'] ?>#mashq" class="btn btn-gold btn-lg"><?= icon('rotate-cw', '', 18) ?> Mashq qilib, qayta topshirish</a>
                <?php endif; ?>
                <a href="/courses/<?= (int) $course['id'] ?>" class="btn btn-glass btn-lg"><?= icon('map-pin', '', 18) ?> Darslar yo‘li</a>
            </div>
        </section>

        <?php if ($result['details']): ?>
            <h2 class="section-h"><?= icon('check-circle', '', 24) ?> Javoblar tahlili</h2>
            <div class="review-list">
                <?php foreach ($result['details'] as $i => $d): ?>
                    <div class="review-item <?= $d['is_correct'] ? '' : 'is-wrong' ?>">
                        <span class="mark"><?= $d['is_correct'] ? icon('check-circle', '', 18) : icon('rotate-cw', '', 16) ?></span>
                        <div>
                            <h4><?= $i + 1 ?>. <?= e($d['prompt']) ?></h4>
                            <?php if (!empty($d['prompt_en']) && ($d['type'] ?? '') === 'build'): ?><div class="small muted"><?= e($d['prompt_en']) ?></div><?php endif; ?>
                            <div class="ans">
                                <?php if ($d['is_correct']): ?>
                                    Javobingiz: <b class="ok"><?= e($d['chosen']) ?></b>
                                <?php else: ?>
                                    Javobingiz: <b class="no"><?= e($d['chosen']) ?></b> · To‘g‘risi: <b class="ok"><?= e($d['correct']) ?></b>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($d['explanation'])): ?><div class="why"><?= icon('sparkles', '', 12) ?> <?= e($d['explanation']) ?></div><?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php if ($passed): ?>
<script>document.addEventListener('DOMContentLoaded', () => { if (typeof triggerConfetti === 'function') triggerConfetti(); if (typeof SoundFX !== 'undefined') SoundFX.fanfare(); });</script>
<?php endif; ?>
