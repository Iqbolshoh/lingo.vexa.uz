<?php /** @var array $cs course_state(); $c = $cs['course'] */ ?>
<a class="course-card theme-<?= e($c['theme']) ?>" href="/courses/<?= (int) $c['id'] ?>">
    <div class="course-cover">
        <div class="cover-tags">
            <span class="tag"><?= e($c['level']) ?></span>
            <?php if ($c['category'] === 'culture'): ?><span class="tag"><?= t('Madaniyat') ?></span><?php endif; ?>
        </div>
        <div class="cover-ico"><?= icon($c['icon'], '', 26) ?></div>
        <h3><?= e(loc($c, 'title')) ?></h3>
        <?php if (!empty($c['subtitle'])): ?><p><?= e(loc($c, 'subtitle')) ?></p><?php endif; ?>
    </div>
    <div class="course-body">
        <div class="course-meta">
            <span><?= icon('book-open', '', 14) ?> <?= t(':n ta dars', ['n' => $cs['total']]) ?></span>
            <span><?= icon('target', '', 14) ?> <?= t('O‘zlashtirish: :n%', ['n' => $cs['mastery']]) ?></span>
        </div>
        <div>
            <div class="course-progress-row"><span><?= t(':done / :total tugatildi', ['done' => $cs['completed'], 'total' => $cs['total']]) ?></span><span><?= $cs['percent'] ?>%</span></div>
            <div class="bar <?= $c['theme'] === 'gold' ? 'bar-gold' : '' ?>" style="margin-top:6px"><i style="width: <?= $cs['percent'] ?>%"></i></div>
        </div>
        <?php if ($cs['total'] > 0 && $cs['completed'] === $cs['total']): ?>
            <span class="btn btn-accent btn-block"><?= icon('award', '', 16) ?> <?= t('Kurs tugatildi — diplom tayyor') ?></span>
        <?php elseif ($cs['next']): ?>
            <span class="btn btn-primary btn-block"><?= icon('play', '', 16) ?> <?= $cs['completed'] > 0 ? t('Davom ettirish') : t('Boshlash') ?>: <?= e(loc($cs['next'], 'title')) ?></span>
        <?php else: ?>
            <span class="btn btn-ghost btn-block"><?= t('Darslar tez orada') ?></span>
        <?php endif; ?>
    </div>
</a>
