<?php $labels = ['pending' => 'Yangi', 'contacted' => 'Bog‘lanildi', 'approved' => 'Qabul qilindi', 'rejected' => 'Rad etildi']; ?>
<div class="container adm">
    <?php require __DIR__ . '/_side.php'; ?>
    <main class="adm-main">
        <div class="adm-head">
            <div><div class="crumbs"><a href="/admin">Admin</a> / Arizalar</div><h1>Pilot arizalari</h1></div>
            <a href="/admin/export" class="btn btn-ghost btn-sm"><?= icon('download', '', 14) ?> CSV yuklab olish</a>
        </div>
        <nav class="adm-tabs">
            <a href="/admin/enrollments" class="<?= $status === '' ? 'is-active' : '' ?>">Barchasi <span class="n"><?= array_sum($counts) ?></span></a>
            <?php foreach ($labels as $k => $label): ?>
                <a href="/admin/enrollments?status=<?= $k ?>" class="<?= $status === $k ? 'is-active' : '' ?>"><?= $label ?> <span class="n"><?= (int) ($counts[$k] ?? 0) ?></span></a>
            <?php endforeach; ?>
        </nav>
        <div class="tbl-wrap">
            <table class="tbl">
                <thead><tr><th class="col-index">#</th><th>Ariza beruvchi</th><th>O‘quvchi</th><th>Vaqt zonasi</th><th>Izoh</th><th>Sana</th><th>Holat</th></tr></thead>
                <tbody>
                <?php if (!$enrollments): ?><tr><td colspan="7" class="empty">Ariza yo‘q.</td></tr><?php endif; ?>
                <?php foreach ($enrollments as $i => $en): ?>
                    <tr>
                        <td class="col-index"><?= $i + 1 ?></td>
                        <td><span class="t-title"><?= e($en['parent_name']) ?></span><div class="t-sub"><a href="mailto:<?= e($en['parent_email']) ?>"><?= e($en['parent_email']) ?></a><?= $en['parent_phone'] ? ' · ' . e($en['parent_phone']) : '' ?></div></td>
                        <td><?= e($en['child_name']) ?><div class="t-sub"><?= e($en['child_age']) ?> yosh · <?= e($en['language_level']) ?></div></td>
                        <td class="t-sub"><?= e($en['timezone']) ?></td>
                        <td class="t-sub" style="max-width:220px"><?= e($en['notes']) ?></td>
                        <td class="t-sub"><?= e(substr((string) $en['created_at'], 0, 10)) ?></td>
                        <td>
                            <form method="POST" action="/admin/enrollments/status" class="inline-form">
                                <?= csrf_field() ?>
                                <input type="hidden" name="enrollment_id" value="<?= (int) $en['id'] ?>">
                                <input type="hidden" name="_back" value="/admin/enrollments<?= $status ? '?status=' . e($status) : '' ?>">
                                <select name="status" class="select-sm" onchange="this.form.submit()">
                                    <?php foreach ($labels as $k => $label): ?><option value="<?= $k ?>" <?= $en['status'] === $k ? 'selected' : '' ?>><?= $label ?></option><?php endforeach; ?>
                                </select>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
