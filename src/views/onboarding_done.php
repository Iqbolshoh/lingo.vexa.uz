<?php
$motives = decode_list($user['motives_json'], ONB_MOTIVES);
$places = decode_list($user['places_json'], ONB_PLACES);
$interests = decode_list($user['interests_json'], ONB_INTERESTS);
$goal = ONB_GOALS[(int) $user['daily_goal_xp']] ?? ONB_GOALS[50];
?>
<div class="pg">
    <div class="container pg-narrow">
        <section class="pg-hero" style="text-align:center">
            <?= mascot('celebrate', 130) ?>
            <h1>Tayyor, <?= e($user['display_name']) ?>!</h1>
            <p style="margin:0 auto">Endi sizni yaxshiroq bilaman. Darslar va tavsiyalar qiziqishlaringizga moslandi.</p>
            <div class="xp-strip" style="justify-content:center">
                <span class="xp-chip"><?= icon('target', '', 16) ?> Daraja: <?= e($user['language_level']) ?></span>
                <span class="xp-chip"><span class="ico-flame"><?= icon('flame', '', 16) ?></span> Kunlik maqsad: <?= $goal[0] ?></span>
                <?php if ($result): ?><span class="xp-chip"><?= icon('check-circle', '', 16) ?> Test: <?= (int) $result['correct'] ?>/<?= (int) $result['total'] ?></span><?php endif; ?>
            </div>
        </section>

        <?php if ($result && $result['unlocked'] > 0): ?>
            <div class="card tone-emerald" style="margin-top:18px">
                <h3 class="card-title"><span class="icon-wrap"><?= icon('unlock', '', 20) ?></span> <?= (int) $result['unlocked'] ?> ta dars siz uchun ochildi</h3>
                <p class="muted">Daraja testida bu mavzularni bilishingizni ko‘rsatdingiz — ularni o‘tkazib yuborib, keyingisidan boshlashingiz mumkin.</p>
            </div>
        <?php endif; ?>

        <h2 class="section-h"><?= icon('sparkles', '', 24) ?> Siz uchun tavsiya</h2>
        <div class="course-grid">
            <?php foreach ($recommended as $i => $c): $cs = course_state($c, (int) $user['id']); ?>
                <div style="position:relative">
                    <?php if ($i === 0): ?><span class="tag tag-gold rec-badge"><?= icon('star', '', 12) ?> Eng mos</span><?php endif; ?>
                    <?php require __DIR__ . '/partials/course_card.php'; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($motives || $places || $interests): ?>
            <div class="card" style="margin-top:22px">
                <h3 class="card-title"><?= icon('heart', '', 20) ?> Sizning tanlovlaringiz</h3>
                <div class="chip-cloud">
                    <?php foreach ($motives as $k): ?><span class="tag tag-lapis"><?= ONB_MOTIVES[$k][0] ?></span><?php endforeach; ?>
                    <?php foreach ($places as $k): ?><span class="tag tag-emerald"><?= ONB_PLACES[$k][0] ?></span><?php endforeach; ?>
                    <?php foreach ($interests as $k): ?><span class="tag tag-gold"><?= ONB_INTERESTS[$k][0] ?></span><?php endforeach; ?>
                </div>
                <div class="form-foot"><a href="/onboarding" class="btn btn-ghost btn-sm">O‘zgartirish</a><a href="/dashboard" class="btn btn-primary"><?= icon('play', '', 16) ?> O‘rganishni boshlash</a></div>
            </div>
        <?php else: ?>
            <div class="form-foot"><a href="/dashboard" class="btn btn-primary"><?= icon('play', '', 16) ?> O‘rganishni boshlash</a></div>
        <?php endif; ?>
    </div>
</div>
