<div class="container adm">
    <?php require __DIR__ . '/_side.php'; ?>
    <main class="adm-main">
        <div class="adm-head"><div><div class="crumbs"><a href="/admin">Admin</a> / <a href="/admin/students">Öquvçilar</a> / Yangi</div><h1>Yangi öquvçi</h1></div></div>
        <form method="POST" action="/admin/students" class="card">
            <?= csrf_field() ?>
            <?php $s = ['display_name' => '', 'email' => '', 'age_band' => '18+', 'language_level' => 'A0', 'timezone' => 'Asia/Tashkent']; require __DIR__ . '/_student_fields.php'; ?>
            <div class="adm-form" style="margin-top:14px">
                <div class="field"><label>Parol *</label><input type="text" name="password" required minlength="6" autocomplete="off" placeholder="Kamida 6 belgi"><div class="hint">Öquvçiga yuboring; u profilida o‘zgartira oladi.</div></div>
            </div>
            <div class="form-foot"><a href="/admin/students" class="btn btn-ghost">Bekor qiliş</a><button class="btn btn-primary"><?= icon('check-circle', '', 16) ?> Yaratiş</button></div>
        </form>
    </main>
</div>
