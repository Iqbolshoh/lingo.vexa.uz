<?php $c = $state['course']; ?>
<div class="pg">
    <div class="container pg-narrow">
        <section class="pg-hero theme-gold" style="text-align:center">
            <span class="hero-chip"><?= icon('award', '', 14) ?> Kurs diplomi</span>
            <h1>Diplom hali yopiq</h1>
            <p style="margin:0 auto">«<?= e($c['title']) ?>» kursining barcha darslarini kamida 80% bilan tugatganingizda diplom avtomatik ochiladi.</p>
            <div class="ring" style="--p: <?= $state['percent'] ?>; margin:20px auto 0">
                <div><strong><?= $state['completed'] ?>/<?= $state['total'] ?></strong><span>dars tugatildi</span></div>
            </div>
        </section>
        <div class="card" style="margin-top:22px">
            <h3 class="card-title"><?= icon('target', '', 20) ?> Qolgan qadamlar</h3>
            <ul class="lock-list">
                <?php foreach ($state['lessons'] as $l): ?>
                    <li class="<?= $l['user_status'] === 'completed' ? 'done' : '' ?>">
                        <?= $l['user_status'] === 'completed' ? icon('check-circle', '', 18) : icon('lock', '', 16) ?>
                        <?= (int) $l['position'] ?>. <?= e($l['title']) ?>
                        <?php if ($l['best_score'] > 0): ?><span class="heat heat-<?= mastery_label((int) $l['best_score'])[1] ?>" style="margin-left:auto"><?= (int) $l['best_score'] ?>%</span><?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="form-foot">
                <a href="/courses/<?= (int) $c['id'] ?>" class="btn btn-primary"><?= icon('play', '', 16) ?> Darslarni davom ettirish</a>
            </div>
        </div>
    </div>
</div>
