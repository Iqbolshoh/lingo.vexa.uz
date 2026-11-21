<?php
$totalDone = array_sum(array_column($courseStates, 'completed'));
$totalLessons = array_sum(array_column($courseStates, 'total'));
$overall = $totalLessons > 0 ? (int) round($totalDone / $totalLessons * 100) : 0;
$earned = array_filter($badges, fn ($b) => $b['earned_at']);
?>
<div class="pg">
    <div class="container">
        <section class="pg-hero">
            <div class="hero-split">
                <div>
                    <span class="hero-chip"><?= icon('registon', '', 14) ?> O‘quvchi fazosi</span>
                    <h1>Assalomu alaykum, <span style="color:#fde68a"><?= e($user['display_name']) ?></span>!</h1>
                    <p>
                        <?php if ($continue): ?>
                            Bugun «<?= e($continue['title']) ?>» darsini davom ettiramiz. Har kuni 5 daqiqa — va o‘zbek tili sizniki!
                        <?php else: ?>
                            Barcha ochiq darslarni tugatdingiz. Ajoyib! Diplomingizni yuklab oling yoki darslarni takrorlang.
                        <?php endif; ?>
                    </p>
                    <div class="xp-strip">
                        <span class="xp-chip"><span class="ico-flame"><?= icon('flame', '', 18) ?></span> <?= $streak ?> kun <small>ketma-ket</small></span>
                        <span class="xp-chip"><span class="ico-star"><?= icon('star', '', 18) ?></span> <?= (int) $user['xp'] ?> XP <small><?= $level['level'] ?>-daraja</small></span>
                        <span class="xp-chip"><span class="ico-coins"><?= icon('coins', '', 18) ?></span> <?= (int) $user['coins'] ?> <small>tanga</small></span>
                    </div>
                    <div class="dashboard-cta-row" style="margin-top:20px">
                        <?php if ($continue): ?>
                            <a href="/lessons/<?= (int) $continue['id'] ?>" class="btn btn-gold btn-lg"><?= icon('play', '', 18) ?> Darsni davom ettirish</a>
                        <?php endif; ?>
                        <a href="/courses" class="btn btn-glass btn-lg"><?= icon('book-open', '', 18) ?> Barcha kurslar</a>
                    </div>
                </div>
                <div class="ring" style="--p: <?= $overall ?>">
                    <div><strong><?= $overall ?>%</strong><span>umumiy yo‘l</span></div>
                </div>
            </div>
        </section>

        <div class="stat-grid">
            <div class="stat-tile tone-lapis">
                <span class="stat-ico"><?= icon('check-circle', '', 22) ?></span>
                <div><strong><?= $totalDone ?> / <?= $totalLessons ?></strong><span>o‘zlashtirilgan darslar</span></div>
            </div>
            <div class="stat-tile tone-emerald">
                <span class="stat-ico"><?= icon('book-open', '', 22) ?></span>
                <div><strong><?= $wordsLearned ?></strong><span>o‘rganilgan so‘zlar</span></div>
            </div>
            <div class="stat-tile tone-gold">
                <span class="stat-ico"><?= icon('star', '', 22) ?></span>
                <div style="flex:1">
                    <strong><?= $level['level'] ?>-daraja</strong><span><?= $level['into'] ?> / <?= $level['need'] ?> XP</span>
                    <div class="bar bar-gold"><i style="width: <?= $level['percent'] ?>%"></i></div>
                </div>
            </div>
            <div class="stat-tile tone-ruby">
                <span class="stat-ico"><?= icon('award', '', 22) ?></span>
                <div><strong><?= count($earned) ?> / <?= count($badges) ?></strong><span>nishonlar</span></div>
            </div>
        </div>

        <div class="layout-main-side">
            <div>
                <h2 class="section-h mt-0"><?= icon('layers', '', 24) ?> Mening kurslarim <small>Keyingi dars uchun testdan 80%+ kerak</small></h2>
                <div class="course-grid">
                    <?php foreach ($courseStates as $cs): $c = $cs['course']; ?>
                        <?php require __DIR__ . '/partials/course_card.php'; ?>
                    <?php endforeach; ?>
                </div>

                <h2 class="section-h"><?= icon('target', '', 24) ?> So‘nggi natijalar</h2>
                <div class="card">
                    <?php if (!$recent): ?>
                        <div class="empty">
                            <div class="e-ico"><?= icon('sparkles', '', 26) ?></div>
                            Hali test topshirilmagan. Birinchi darsni boshlang — natijalar shu yerda ko‘rinadi!
                        </div>
                    <?php else: ?>
                        <div class="review-list">
                            <?php foreach ($recent as $r): ?>
                                <a class="review-item <?= $r['passed'] ? '' : 'is-wrong' ?>" href="/lessons/<?= (int) $r['lesson_id'] ?>/result?attempt=<?= (int) $r['id'] ?>" style="color:inherit">
                                    <span class="mark"><?= $r['passed'] ? icon('check-circle', '', 18) : icon('rotate-cw', '', 16) ?></span>
                                    <div class="row-between">
                                        <div>
                                            <h4><?= e($r['lesson_title']) ?></h4>
                                            <div class="small muted"><?= e(substr((string) $r['created_at'], 0, 16)) ?> · +<?= (int) $r['xp_earned'] ?> XP</div>
                                        </div>
                                        <span class="heat heat-<?= mastery_label((int) $r['percent'])[1] ?>"><?= (int) $r['percent'] ?>%</span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <aside class="stack-sm">
                <div class="card tone-gold">
                    <h3 class="card-title"><span class="icon-wrap"><?= icon('trophy', '', 20) ?></span> Nishonlar</h3>
                    <div class="bdg-grid">
                        <?php foreach ($badges as $b): ?>
                            <div class="bdg-item <?= $b['earned_at'] ? '' : 'is-off' ?>" title="<?= e($b['description']) ?>">
                                <div class="b-ico"><?= icon($b['icon'], '', 20) ?></div>
                                <strong><?= e($b['title']) ?></strong>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="card tone-lapis">
                    <h3 class="card-title"><span class="icon-wrap"><?= icon('video', '', 20) ?></span> Jonli muloqot</h3>
                    <?php if ($liveSession): ?>
                        <strong style="display:block;color:var(--ink)"><?= e($liveSession['title']) ?></strong>
                        <p class="small muted" style="margin:4px 0 10px"><?= e(uz_date($liveSession['starts_at'], normalize_timezone($user['timezone']))) ?></p>
                        <div class="course-meta" style="margin-bottom:12px">
                            <?php foreach (live_time_labels($liveSession['starts_at']) as $zone => $time): ?>
                                <span class="tag tag-lapis"><?= e($time) ?> <?= e($zone) ?></span>
                            <?php endforeach; ?>
                        </div>
                        <a href="/live" class="btn btn-primary btn-sm btn-block"><?= icon('video', '', 16) ?> Xonaga o‘tish</a>
                    <?php else: ?>
                        <p class="small muted">Yaqin kunlarda jonli dars rejalashtirilmagan. Ustoz yangi vaqt e’lon qilganda shu yerda ko‘rinadi.</p>
                    <?php endif; ?>
                </div>

                <div class="card tone-emerald">
                    <h3 class="card-title"><span class="icon-wrap"><?= icon('heart', '', 20) ?></span> Kunlik maslahat</h3>
                    <p class="small muted">Har kuni 5 daqiqa yetarli: o‘qish bo‘limidagi gaplarni baland ovozda takrorlang. Farzandingiz bilan o‘qisangiz — navbatma-navbat ayting.</p>
                    <a href="/parent-tips" class="btn btn-ghost btn-sm btn-block" style="margin-top:10px">Samarali o‘rganish qo‘llanmasi</a>
                </div>
            </aside>
        </div>
    </div>
</div>
