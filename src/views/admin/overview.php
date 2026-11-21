<div class="container adm">
    <?php require __DIR__ . '/_side.php'; ?>
    <main class="adm-main">
        <div class="adm-head">
            <div><div class="crumbs">Admin panel</div><h1>Umumiy ko‘rinish</h1></div>
            <div class="adm-actions">
                <a href="/admin/courses" class="btn btn-primary btn-sm"><?= icon('book-open', '', 16) ?> Dars qo‘shish</a>
                <a href="/admin/export" class="btn btn-ghost btn-sm"><?= icon('download', '', 16) ?> Arizalar CSV</a>
            </div>
        </div>

        <div class="stat-grid mt-0">
            <div class="stat-tile tone-lapis"><span class="stat-ico"><?= icon('users', '', 22) ?></span><div><strong><?= $counts['students'] ?></strong><span>o‘quvchi · 7 kunda faol: <?= $counts['active7'] ?></span></div></div>
            <div class="stat-tile tone-gold"><span class="stat-ico"><?= icon('book-open', '', 22) ?></span><div><strong><?= $counts['lessons'] ?></strong><span>dars · <?= $counts['courses'] ?> kurs</span></div></div>
            <div class="stat-tile tone-emerald"><span class="stat-ico"><?= icon('check-circle', '', 22) ?></span><div><strong><?= $counts['pass_rate'] ?>%</strong><span>testdan o‘tish · o‘rtacha <?= $counts['avg_score'] ?>%</span></div></div>
            <div class="stat-tile tone-ruby"><span class="stat-ico"><?= icon('message-square', '', 22) ?></span><div><strong><?= $counts['pending'] ?></strong><span>yangi ariza</span></div></div>
        </div>

        <div class="grid-2">
            <div class="card">
                <h3 class="card-title"><?= icon('calendar', '', 20) ?> So‘nggi 14 kun faolligi</h3>
                <?php $max = max(1, max($daily)); ?>
                <div class="spark">
                    <?php foreach ($daily as $d => $v): ?><div style="height: <?= max(3, (int) round($v / $max * 100)) ?>%" data-v="<?= $v ?>" title="<?= e($d) ?>: <?= $v ?> ta test"></div><?php endforeach; ?>
                </div>
                <div class="spark-labels"><?php foreach (array_keys($daily) as $d): ?><span><?= (int) substr($d, 8) ?></span><?php endforeach; ?></div>
                <p class="small muted" style="margin-top:8px">Kunlik topshirilgan testlar soni. Jami: <?= $counts['attempts'] ?> ta urinish, <?= $counts['questions'] ?> ta savol, <?= $counts['words'] ?> ta so‘z.</p>
            </div>
            <div class="card">
                <h3 class="card-title"><?= icon('target', '', 20) ?> Eng qiyin savollar</h3>
                <?php if (!$hardest): ?>
                    <div class="empty">Hali yetarli ma’lumot yo‘q — o‘quvchilar test topshirgach ko‘rinadi.</div>
                <?php else: ?>
                    <div class="stack-sm">
                        <?php foreach ($hardest as $h): ?>
                            <a href="/admin/lessons/<?= $h['lesson_id'] ?>?tab=questions" style="display:block;color:inherit">
                                <div class="row-between"><span class="small" style="font-weight:700;flex:1;min-width:0"><?= e(mb_strimwidth($h['prompt'], 0, 70, '…')) ?></span><span class="heat heat-low"><?= $h['error_rate'] ?>% xato</span></div>
                                <div class="small muted"><?= e(QUESTION_TYPES[$h['type']] ?? $h['type']) ?> · <?= $h['total'] ?> ta javob</div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <h2 class="section-h"><?= icon('layers', '', 22) ?> Darslar bo‘yicha o‘zlashtirish</h2>
        <div class="tbl-wrap">
            <table class="tbl">
                <thead><tr><th class="col-index">#</th><th>Dars</th><th>Kurs</th><th>Urinib ko‘rgan</th><th>O‘tgan</th><th>O‘rtacha eng yaxshi ball</th></tr></thead>
                <tbody>
                <?php foreach ($lessonStats as $i => $l): $avg = (int) round((float) $l['avg_best']); ?>
                    <tr>
                        <td class="col-index"><?= $i + 1 ?></td>
                        <td><a class="t-title" href="/admin/lessons/<?= (int) $l['id'] ?>"><?= e($l['title']) ?></a></td>
                        <td class="t-sub"><?= e($l['course_title']) ?></td>
                        <td><?= (int) $l['tried'] ?></td>
                        <td><?= (int) $l['passed'] ?></td>
                        <td><div class="mini-bar"><div class="bar <?= $avg >= (int) $l['passing_score'] ? 'bar-emerald' : 'bar-gold' ?>"><i style="width: <?= $avg ?>%"></i></div><b><?= $l['tried'] ? $avg . '%' : '—' ?></b></div></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <h2 class="section-h"><?= icon('check-circle', '', 22) ?> So‘nggi test urinishlari</h2>
        <div class="tbl-wrap">
            <table class="tbl">
                <thead><tr><th class="col-index">#</th><th>O‘quvchi</th><th>Dars</th><th>Natija</th><th>XP</th><th>Sana</th></tr></thead>
                <tbody>
                <?php if (!$attempts): ?><tr><td colspan="6" class="empty">Hali test topshirilmagan.</td></tr><?php endif; ?>
                <?php foreach ($attempts as $i => $a): ?>
                    <tr>
                        <td class="col-index"><?= $i + 1 ?></td>
                        <td><a class="t-title" href="/admin/students/<?= (int) $a['user_id'] ?>"><?= e($a['display_name']) ?></a></td>
                        <td><?= e($a['lesson_title']) ?></td>
                        <td><span class="heat heat-<?= mastery_label((int) $a['percent'])[1] ?>"><?= (int) $a['percent'] ?>%</span> <span class="t-sub"><?= (int) $a['score'] ?>/<?= (int) $a['total'] ?></span></td>
                        <td>+<?= (int) $a['xp_earned'] ?></td>
                        <td class="t-sub"><?= e(substr((string) $a['created_at'], 0, 16)) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
