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
            <h1><?= t('Tayyor, :name!', ['name' => $user['display_name']]) ?></h1>
            <p style="margin:0 auto"><?= t('Endi sizni yaxshiroq bilaman. Darslar va tavsiyalar qiziqishlaringizga moslandi.') ?></p>
            <div class="xp-strip" style="justify-content:center">
                <span class="xp-chip"><?= icon('target', '', 16) ?> <?= t('Daraja: :level', ['level' => $user['language_level']]) ?></span>
                <span class="xp-chip"><span class="ico-flame"><?= icon('flame', '', 16) ?></span> <?= t('Kunlik maqsad: :goal', ['goal' => t($goal[0])]) ?></span>
                <?php if ($result): ?><span class="xp-chip"><?= icon('check-circle', '', 16) ?> <?= t('Test') ?>: <?= (int) $result['correct'] ?>/<?= (int) $result['total'] ?></span><?php endif; ?>
            </div>
        </section>

        <?php if ($result && $result['unlocked'] > 0): ?>
            <div class="card tone-emerald" style="margin-top:18px">
                <h3 class="card-title"><span class="icon-wrap"><?= icon('unlock', '', 20) ?></span> <?= t(':n ta dars siz uchun ochildi', ['n' => (int) $result['unlocked']]) ?></h3>
                <p class="muted"><?= t('Daraja testida bu mavzularni bilishingizni ko‘rsatdingiz — ularni o‘tkazib yuborib, keyingisidan boshlashingiz mumkin.') ?></p>
            </div>
        <?php endif; ?>

        <h2 class="section-h"><?= icon('sparkles', '', 24) ?> <?= t('Siz uchun tavsiya') ?></h2>
        <div class="course-grid">
            <?php foreach ($recommended as $i => $c): $cs = course_state($c, (int) $user['id']); ?>
                <div style="position:relative">
                    <?php if ($i === 0): ?><span class="tag tag-gold rec-badge"><?= icon('star', '', 12) ?> <?= t('Eng mos') ?></span><?php endif; ?>
                    <?php require __DIR__ . '/partials/course_card.php'; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($motives || $places || $interests): ?>
            <div class="card" style="margin-top:22px">
                <h3 class="card-title"><?= icon('heart', '', 20) ?> <?= t('Sizning tanlovlaringiz') ?></h3>
                <div class="chip-cloud">
                    <?php foreach ($motives as $k): ?><span class="tag tag-lapis"><?= t(ONB_MOTIVES[$k][0]) ?></span><?php endforeach; ?>
                    <?php foreach ($places as $k): ?><span class="tag tag-emerald"><?= t(ONB_PLACES[$k][0]) ?></span><?php endforeach; ?>
                    <?php foreach ($interests as $k): ?><span class="tag tag-gold"><?= t(ONB_INTERESTS[$k][0]) ?></span><?php endforeach; ?>
                </div>
                <div class="form-foot"><a href="/onboarding" class="btn btn-ghost btn-sm"><?= t('O‘zgartirish') ?></a><a href="/dashboard" class="btn btn-primary"><?= icon('play', '', 16) ?> <?= t('O‘rganishni boshlash') ?></a></div>
            </div>
        <?php else: ?>
            <div class="form-foot"><a href="/dashboard" class="btn btn-primary"><?= icon('play', '', 16) ?> <?= t('O‘rganishni boshlash') ?></a></div>
        <?php endif; ?>
    </div>
</div>
