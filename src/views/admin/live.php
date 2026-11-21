<?php
$f = $edit ?: ['id' => null, 'title' => '', 'description' => '', 'starts_at' => '', 'timezone' => 'America/New_York', 'zoom_url' => '', 'mentor_name' => 'Gulomov Ustoz', 'status' => 'scheduled'];
$zone = normalize_timezone($f['timezone']);
$local = $f['starts_at'] ? (new DateTimeImmutable($f['starts_at'], new DateTimeZone('UTC')))->setTimezone(new DateTimeZone($zone))->format('Y-m-d\TH:i') : '';
?>
<div class="container adm">
    <?php require __DIR__ . '/_side.php'; ?>
    <main class="adm-main">
        <div class="adm-head"><div><div class="crumbs"><a href="/admin">Admin</a> / Jonli darslar</div><h1>Jonli darslar (Zoom)</h1></div></div>
        <div class="tbl-wrap">
            <table class="tbl">
                <thead><tr><th class="col-index">#</th><th>Mavzu</th><th>Vaqt (Toshkent / London / AQSh)</th><th>Mentor</th><th>Holat</th><th style="text-align:right">Amallar</th></tr></thead>
                <tbody>
                <?php if (!$sessions): ?><tr><td colspan="6" class="empty">Jonli dars yo‘q.</td></tr><?php endif; ?>
                <?php foreach ($sessions as $i => $s): $past = strtotime($s['starts_at'] . ' UTC') < time(); ?>
                    <tr style="<?= $past ? 'opacity:.6' : '' ?>">
                        <td class="col-index"><?= $i + 1 ?></td>
                        <td class="t-title"><?= e($s['title']) ?><div class="t-sub"><?= e(uz_date($s['starts_at'])) ?><?= $past ? ' · o‘tib ketgan' : '' ?></div></td>
                        <td><?php foreach (live_time_labels($s['starts_at']) as $z => $t): ?><span class="tag tag-lapis" style="margin:2px"><?= e($t) ?> <?= $z ?></span><?php endforeach; ?></td>
                        <td><?= e($s['mentor_name']) ?></td>
                        <td><span class="status-pill st-<?= e($s['status']) ?>"><?= $s['status'] === 'cancelled' ? 'Bekor qilingan' : 'Rejalashtirilgan' ?></span></td>
                        <td><div class="t-actions">
                            <a class="icon-btn" href="/admin/live?edit=<?= (int) $s['id'] ?>" title="Tahrirlash"><?= icon('settings', '', 15) ?></a>
                            <form method="POST" action="/admin/live/<?= (int) $s['id'] ?>/delete" class="inline-form" onsubmit="return confirm('Jonli dars o‘chirilsinmi?')"><?= csrf_field() ?><button class="icon-btn danger" title="O‘chirish">&times;</button></form>
                        </div></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <form method="POST" action="/admin/live<?= $f['id'] ? '/' . (int) $f['id'] : '' ?>" class="card" style="margin-top:18px">
            <?= csrf_field() ?>
            <h3 class="card-title"><?= icon('calendar', '', 20) ?> <?= $f['id'] ? 'Jonli darsni tahrirlash' : 'Yangi jonli dars' ?></h3>
            <div class="adm-form">
                <div class="field full"><label>Mavzu *</label><input type="text" name="title" required maxlength="160" value="<?= e($f['title']) ?>" placeholder="Haftalik suhbat: Mening oilam"></div>
                <div class="field full"><label>Tavsif</label><textarea name="description" maxlength="1000" style="min-height:70px"><?= e($f['description']) ?></textarea></div>
                <div class="field"><label>Sana va vaqt *</label><input type="datetime-local" name="starts_at" required value="<?= e($local) ?>"></div>
                <div class="field"><label>Qaysi vaqt zonasida kiritildi</label>
                    <select name="timezone"><?php foreach (TIMEZONES as $k => $label): ?><option value="<?= $k ?>" <?= $zone === $k ? 'selected' : '' ?>><?= $label ?></option><?php endforeach; ?></select>
                    <div class="hint">O‘quvchilarga vaqt avtomatik 4 ta zonada ko‘rsatiladi.</div>
                </div>
                <div class="field"><label>Zoom havolasi *</label><input type="url" name="zoom_url" required maxlength="400" value="<?= e($f['zoom_url']) ?>" placeholder="https://zoom.us/j/…"></div>
                <div class="field"><label>Mentor</label><input type="text" name="mentor_name" maxlength="80" value="<?= e($f['mentor_name']) ?>"></div>
                <div class="field"><label>Holat</label><select name="status"><option value="scheduled" <?= $f['status'] === 'scheduled' ? 'selected' : '' ?>>Rejalashtirilgan</option><option value="cancelled" <?= $f['status'] === 'cancelled' ? 'selected' : '' ?>>Bekor qilingan</option></select></div>
            </div>
            <div class="form-foot">
                <?php if ($f['id']): ?><a href="/admin/live" class="btn btn-ghost">Bekor qilish</a><?php endif; ?>
                <button type="submit" class="btn btn-primary"><?= icon('check-circle', '', 16) ?> Saqlash</button>
            </div>
        </form>
    </main>
</div>
