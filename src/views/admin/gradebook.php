<div class="container adm">
    <?php require __DIR__ . '/_side.php'; ?>
    <main class="adm-main">
        <div class="adm-head">
            <div><div class="crumbs"><a href="/admin">Admin</a> / Baholar jurnali</div><h1>Baholar jurnali</h1></div>
            <form method="GET" action="/admin/gradebook" class="field">
                <select name="course" onchange="this.form.submit()">
                    <?php foreach ($courses as $c): ?><option value="<?= (int) $c['id'] ?>" <?= $course && (int) $course['id'] === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['title']) ?></option><?php endforeach; ?>
                </select>
            </form>
        </div>
        <div class="tbl-wrap">
            <table class="tbl gradebook">
                <thead>
                    <tr>
                        <th class="col-index">#</th><th>O‘quvchi</th>
                        <?php foreach ($lessons as $l): ?><th class="l-col" title="<?= e($l['title']) ?>"><?= (int) $l['position'] ?>-dars</th><?php endforeach; ?>
                        <th>O‘rtacha</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!$students || !$lessons): ?><tr><td colspan="<?= count($lessons) + 3 ?>" class="empty">Ma’lumot yo‘q.</td></tr><?php endif; ?>
                <?php foreach ($students as $i => $s): $scores = []; ?>
                    <tr>
                        <td class="col-index"><?= $i + 1 ?></td>
                        <td><a class="t-title" href="/admin/students/<?= (int) $s['id'] ?>"><?= e($s['display_name']) ?></a></td>
                        <?php foreach ($lessons as $l): $p = $grid[(int) $s['id']][(int) $l['id']] ?? null; $score = (int) ($p['best_score'] ?? 0); $scores[] = $score; ?>
                            <td><span class="heat heat-<?= mastery_label($score)[1] ?>"><?= $score ? $score . '%' : '—' ?></span></td>
                        <?php endforeach; ?>
                        <?php $avg = $scores ? (int) round(array_sum($scores) / count($scores)) : 0; ?>
                        <td><strong><?= $avg ?>%</strong></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="legend">
            <span><i class="heat heat-excellent">90+</i> A’lo</span>
            <span><i class="heat heat-good">80+</i> O‘zlashtirildi</span>
            <span><i class="heat heat-mid">50+</i> Rivojlanmoqda</span>
            <span><i class="heat heat-low">1+</i> Qayta ishlash kerak</span>
            <span><i class="heat heat-none">—</i> Boshlanmagan</span>
        </div>
    </main>
</div>
