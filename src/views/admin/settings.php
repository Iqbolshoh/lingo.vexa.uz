<div class="container adm">
    <?php require __DIR__ . '/_side.php'; ?>
    <main class="adm-main">
        <div class="adm-head"><div><div class="crumbs"><a href="/admin">Admin</a> / Sozlamalar</div><h1>Sozlamalar</h1></div></div>
        <div class="grid-2" style="align-items:start">
            <form method="POST" action="/admin/settings/password" class="card">
                <?= csrf_field() ?>
                <h3 class="card-title"><?= icon('lock', '', 20) ?> Parolni almashtirish</h3>
                <div class="adm-form" style="grid-template-columns:1fr">
                    <div class="field"><label>Joriy parol</label><input type="password" name="current_password" required autocomplete="current-password"></div>
                    <div class="field"><label>Yangi parol</label><input type="password" name="new_password" required minlength="8" autocomplete="new-password"><div class="hint">Kamida 8 belgi. Demo parolni (admin123) albatta almashtiring.</div></div>
                </div>
                <div class="form-foot"><button class="btn btn-primary">Saqlash</button></div>
            </form>
            <div class="stack-sm">
                <div class="tbl-wrap">
                    <table class="tbl">
                        <thead><tr><th class="col-index">#</th><th>Administrator</th><th>Qo‘shilgan</th></tr></thead>
                        <tbody>
                        <?php foreach ($admins as $i => $a): ?>
                            <tr><td class="col-index"><?= $i + 1 ?></td><td><span class="t-title"><?= e($a['display_name']) ?></span><?= (int) $a['id'] === (int) $me['id'] ? ' <span class="tag tag-emerald">siz</span>' : '' ?><div class="t-sub"><?= e($a['email']) ?></div></td><td class="t-sub"><?= e(substr((string) $a['created_at'], 0, 10)) ?></td></tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <form method="POST" action="/admin/settings/admins" class="card">
                    <?= csrf_field() ?>
                    <h3 class="card-title"><?= icon('shield-check', '', 20) ?> Yangi administrator / ustoz</h3>
                    <div class="adm-form" style="grid-template-columns:1fr">
                        <div class="field"><label>Ism</label><input type="text" name="display_name" required maxlength="80"></div>
                        <div class="field"><label>Email</label><input type="email" name="email" required maxlength="160"></div>
                        <div class="field"><label>Parol</label><input type="text" name="password" required minlength="8" autocomplete="off"></div>
                    </div>
                    <div class="form-foot"><button class="btn btn-primary"><?= icon('check-circle', '', 16) ?> Qo‘shish</button></div>
                </form>
            </div>
        </div>
    </main>
</div>
