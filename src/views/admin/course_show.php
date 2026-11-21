<div class="container adm">
    <?php require __DIR__ . '/_side.php'; ?>
    <main class="adm-main">
        <div class="adm-head">
            <div>
                <div class="crumbs"><a href="/admin">Admin</a> / <a href="/admin/courses">Kurslar</a> / <?= e($course['title']) ?></div>
                <h1><?= e($course['title']) ?></h1>
                <div class="course-meta" style="margin-top:6px">
                    <span class="status-pill st-<?= e($course['status']) ?>"><?= $course['status'] === 'published' ? 'Oçiq' : 'Qoralama' ?></span>
                    <span><?= e($course['level']) ?> · <?= e($course['age_range']) ?></span>
                </div>
            </div>
            <div class="adm-actions">
                <a href="/courses/<?= (int) $course['id'] ?>" class="btn btn-ghost btn-sm" target="_blank"><?= icon('play', '', 14) ?> Öquvçi körinişi</a>
                <a href="/admin/courses/<?= (int) $course['id'] ?>/edit" class="btn btn-ghost btn-sm"><?= icon('settings', '', 14) ?> Kurs sozlamalari</a>
                <a href="/admin/courses/<?= (int) $course['id'] ?>/lessons/create" class="btn btn-primary btn-sm"><?= icon('sparkles', '', 14) ?> Yangi dars</a>
            </div>
        </div>

        <div class="tbl-wrap">
            <table class="tbl">
                <thead><tr><th class="col-index">#</th><th>Dars</th><th>Holat</th><th>Söz</th><th>Savol</th><th>Ötgan</th><th>Örtaça</th><th style="text-align:right">Amallar</th></tr></thead>
                <tbody>
                <?php if (!$lessons): ?>
                    <tr><td colspan="8"><div class="empty"><div class="e-ico"><?= icon('book-open', '', 26) ?></div>Bu kursda hali dars yöq. «Yangi dars» tugmasini bosing.</div></td></tr>
                <?php endif; ?>
                <?php foreach ($lessons as $i => $l): $avg = (int) round((float) $l['avg_best']); ?>
                    <tr>
                        <td class="col-index"><?= $i + 1 ?></td>
                        <td>
                            <a class="t-title" href="/admin/lessons/<?= (int) $l['id'] ?>"><?= e($l['title']) ?></a>
                            <div class="t-sub"><?= e($l['title_en']) ?> · <?= (int) $l['duration_min'] ?> daq · ötiş <?= (int) $l['passing_score'] ?>%</div>
                        </td>
                        <td><span class="status-pill st-<?= e($l['status']) ?>"><?= $l['status'] === 'published' ? 'Oçiq' : 'Qoralama' ?></span></td>
                        <td><?= (int) $l['word_count'] ?></td>
                        <td><?= (int) $l['question_count'] ?: '<span class="tag tag-ruby">0</span>' ?></td>
                        <td><?= (int) $l['passed_count'] ?></td>
                        <td><?= $l['avg_best'] !== null ? '<span class="heat heat-' . mastery_label($avg)[1] . '">' . $avg . '%</span>' : '<span class="t-sub">—</span>' ?></td>
                        <td>
                            <div class="t-actions">
                                <form method="POST" action="/admin/lessons/<?= (int) $l['id'] ?>/move" class="inline-form"><?= csrf_field() ?><input type="hidden" name="dir" value="up"><button class="icon-btn" title="Yuqoriga" <?= $i === 0 ? 'disabled' : '' ?>>&uarr;</button></form>
                                <form method="POST" action="/admin/lessons/<?= (int) $l['id'] ?>/move" class="inline-form"><?= csrf_field() ?><input type="hidden" name="dir" value="down"><button class="icon-btn" title="Pastga" <?= $i === count($lessons) - 1 ? 'disabled' : '' ?>>&darr;</button></form>
                                <a class="icon-btn" href="/admin/lessons/<?= (int) $l['id'] ?>" title="Tahrirlaş"><?= icon('settings', '', 16) ?></a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="small muted" style="margin-top:10px"><?= icon('sparkles', '', 12) ?> Öquvçilar darslarni şu tartibda ötadi: har bir dars oldingisidan ötiş balli töplanganda oçiladi.</p>
    </main>
</div>
