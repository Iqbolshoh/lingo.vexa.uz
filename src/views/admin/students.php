<div class="container adm">
    <?php require __DIR__ . '/_side.php'; ?>
    <main class="adm-main">
        <div class="adm-head">
            <div><div class="crumbs"><a href="/admin">Admin</a> / O‘quvchilar</div><h1>O‘quvchilar</h1></div>
            <form method="GET" action="/admin/students" class="search-bar field">
                <input type="search" name="q" value="<?= e($q) ?>" placeholder="Ism yoki email bo‘yicha qidirish">
                <button class="btn btn-primary btn-sm"><?= icon('search', '', 14) ?></button>
            </form>
        </div>
        <div class="tbl-wrap">
            <table class="tbl" data-sortable>
                <thead><tr><th class="col-index">#</th><th>O‘quvchi</th><th>Yosh / daraja</th><th>Darslar</th><th>O‘zlashtirish</th><th>XP</th><th>Urinishlar</th><th>Oxirgi faollik</th></tr></thead>
                <tbody>
                <?php if (!$students): ?><tr><td colspan="8" class="empty">O‘quvchi topilmadi.</td></tr><?php endif; ?>
                <?php foreach ($students as $i => $s): $avg = (int) round((float) $s['avg_best']); ?>
                    <tr>
                        <td class="col-index"><?= $i + 1 ?></td>
                        <td><a class="t-title" href="/admin/students/<?= (int) $s['id'] ?>"><?= e($s['display_name']) ?></a><div class="t-sub"><?= e($s['email']) ?></div></td>
                        <td><?= e($s['age_band']) ?> · <?= e($s['language_level']) ?></td>
                        <td><div class="mini-bar"><div class="bar"><i style="width: <?= $totalLessons ? (int) round($s['completed_lessons'] / $totalLessons * 100) : 0 ?>%"></i></div><b><?= (int) $s['completed_lessons'] ?>/<?= $totalLessons ?></b></div></td>
                        <td><?= $s['avg_best'] !== null ? '<span class="heat heat-' . mastery_label($avg)[1] . '">' . $avg . '%</span>' : '<span class="t-sub">—</span>' ?></td>
                        <td><?= (int) $s['xp'] ?></td>
                        <td><?= (int) $s['attempts'] ?></td>
                        <td class="t-sub"><?= $s['last_activity'] ? e(substr((string) $s['last_activity'], 0, 16)) : 'Hali yo‘q' ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
