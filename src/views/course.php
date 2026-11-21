<?php $c = $state['course']; $done = $state['total'] > 0 && $state['completed'] === $state['total']; ?>
<div class="pg">
    <div class="container">
        <section class="pg-hero theme-<?= e($c['theme']) ?>">
            <div class="hero-split">
                <div>
                    <div class="crumbs"><a href="/courses">Kurslar</a> <span>/</span> <span><?= e($c['title']) ?></span></div>
                    <h1><?= e($c['title']) ?></h1>
                    <p><?= e($c['description']) ?></p>
                    <div class="xp-strip">
                        <span class="xp-chip"><?= icon('book-open', '', 16) ?> <?= $state['total'] ?> dars</span>
                        <span class="xp-chip"><?= icon('users', '', 16) ?> <?= e($c['age_range']) ?></span>
                        <span class="xp-chip"><?= icon('target', '', 16) ?> Daraja: <?= e($c['level']) ?></span>
                    </div>
                </div>
                <div class="ring" style="--p: <?= $state['percent'] ?>">
                    <div><strong><?= $state['completed'] ?>/<?= $state['total'] ?></strong><span>tugatildi</span></div>
                </div>
            </div>
        </section>

        <div class="layout-main-side" style="margin-top:28px">
            <div>
                <h2 class="section-h mt-0"><?= icon('map-pin', '', 24) ?> Darslar yo‘li</h2>
                <?php if (!$state['lessons']): ?>
                    <div class="card empty"><div class="e-ico"><?= icon('book-open', '', 26) ?></div>Bu kursga darslar tez orada qo‘shiladi.</div>
                <?php endif; ?>
                <div class="path">
                    <?php $currentShown = false; foreach ($state['lessons'] as $l):
                        $st = $l['user_status'];
                        $isCurrent = $st === 'available' && !$currentShown;
                        $currentShown = $currentShown || $isCurrent;
                        [$mLabel, $mTone] = mastery_label((int) $l['best_score']);
                    ?>
                        <div class="path-node is-<?= $st ?> <?= $isCurrent ? 'is-current' : '' ?>">
                            <?php if ($isCurrent): ?><span class="path-start-tip">Boshlash</span><?php endif; ?>
                            <?php if ($st === 'locked'): ?>
                                <span class="path-dot" aria-label="Qulflangan"><?= icon('lock', '', 26) ?></span>
                            <?php else: ?>
                                <a class="path-dot" href="/lessons/<?= (int) $l['id'] ?>" aria-label="<?= e($l['title']) ?>">
                                    <?= $st === 'completed' ? icon('check-circle', '', 30) : icon($l['icon'], '', 28) ?>
                                </a>
                            <?php endif; ?>
                            <div class="path-card">
                                <div class="small muted" style="font-weight:800;letter-spacing:.4px"><?= (int) $l['position'] ?>-DARS</div>
                                <h3><?= e($l['title']) ?></h3>
                                <div class="en"><?= e($l['title_en']) ?></div>
                                <div class="row">
                                    <div class="meta">
                                        <span><?= icon('clock', '', 13) ?> <?= (int) $l['duration_min'] ?> daq</span>
                                        <?php if ($st !== 'locked' && $l['best_score'] > 0): ?><span class="heat heat-<?= $mTone ?>"><?= (int) $l['best_score'] ?>%</span><?php endif; ?>
                                    </div>
                                    <?php if ($st === 'completed'): ?>
                                        <a href="/lessons/<?= (int) $l['id'] ?>" class="btn btn-ghost btn-xs"><?= icon('rotate-cw', '', 13) ?> Takrorlash</a>
                                    <?php elseif ($st === 'available'): ?>
                                        <a href="/lessons/<?= (int) $l['id'] ?>" class="btn btn-primary btn-xs"><?= icon('play', '', 12) ?> Boshlash</a>
                                    <?php else: ?>
                                        <span class="small muted"><?= icon('lock', '', 12) ?> Oldingi darsdan <?= (int) ($l['prev_lesson']['passing_score'] ?? 80) ?>%+</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if ($state['lessons']): ?>
                    <div class="path-finish">
                        <span class="finish-ico"><?= icon('award', '', 30) ?></span>
                        <div style="flex:1">
                            <strong style="font-family:var(--font-heading);font-size:19px">Marra — kurs diplomi!</strong>
                            <div class="small">Barcha darslarni 80%+ bilan tugating va diplomni chop eting.</div>
                        </div>
                        <?php if ($done): ?>
                            <a class="btn btn-accent btn-sm" href="/certificate?course=<?= (int) $c['id'] ?>">Diplomni ochish</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <aside class="stack-sm">
                <div class="card tone-lapis">
                    <h3 class="card-title"><span class="icon-wrap"><?= icon('target', '', 20) ?></span> O‘zlashtirish</h3>
                    <div class="row-between" style="align-items:center">
                        <div class="ring ring-light" style="--p: <?= $state['mastery'] ?>; --size:110px">
                            <div><strong><?= $state['mastery'] ?>%</strong><span>o‘rtacha ball</span></div>
                        </div>
                        <div class="small muted" style="flex:1;min-width:140px">Har bir darsning eng yaxshi test natijasi asosida hisoblanadi. 80% va undan yuqori — dars o‘zlashtirilgan.</div>
                    </div>
                </div>
                <div class="card tone-gold">
                    <h3 class="card-title"><span class="icon-wrap"><?= icon('sparkles', '', 20) ?></span> Har bir dars ichida</h3>
                    <ul class="lock-list">
                        <li><?= icon('video', '', 16) ?> Mikro-sahna va video</li>
                        <li><?= icon('book-open', '', 16) ?> Ovozli lug‘at kartalari</li>
                        <li><?= icon('message-square', '', 16) ?> O‘qish matni + «Bilasizmi?»</li>
                        <li><?= icon('coins', '', 16) ?> Juftlik o‘yini va mashqlar</li>
                        <li><?= icon('check-circle', '', 16) ?> Yakuniy test (80%)</li>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</div>
