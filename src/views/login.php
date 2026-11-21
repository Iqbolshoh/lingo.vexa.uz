<div class="auth-page">
    <div class="container">
        <div class="auth-card">
            <div class="auth-header">
                <?= mascot('welcome', 96) ?>
                <h1><?= t('Tizimga Kirish') ?></h1>
                <p><?= t('LingoUz ta’lim paneliga xush kelibsiz!') ?></p>
            </div>

            <?php if (!empty($currentUser)): ?>
                <div class="current-session-box" style="background:#f0f9ff; border:1.5px solid #bae6fd; border-radius:12px; padding:14px; margin-bottom:20px; font-size:13.5px; text-align:center;">
                    <div style="color:#0369a1; font-weight:600;"><?= t('Siz hozir :name sifatida tizimdasiz.', ['name' => $currentUser['display_name']]) ?></div>
                    <div style="margin-top:10px; display:flex; justify-content:center; gap:8px; flex-wrap:wrap;">
                        <a href="<?= $currentUser['role'] === 'admin' ? '/admin' : '/dashboard' ?>" class="btn btn-primary btn-sm">
                            <?= icon('layers', '', 14) ?> <?= t('Panelga o‘tish') ?>
                        </a>
                        <form action="/logout" method="POST" class="inline-form">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-danger btn-sm"><?= icon('log-out', '', 14) ?> <?= t('Tizimdan chiqish') ?></button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <form action="/login" method="POST" class="auth-form" id="loginForm">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="email"><?= t('Elektron pochta yoki Login') ?></label>
                    <input type="text" id="email" name="email" required placeholder="<?= t('demo@lingo.uz yoki demo') ?>">
                </div>

                <div class="form-group">
                    <label for="password"><?= t('Parol') ?></label>
                    <input type="password" id="password" name="password" required placeholder="<?= t('Parolni kiriting') ?>">
                </div>

                <button type="submit" class="btn btn-primary btn-lg btn-block">
                    Kirish <?= icon('arrow-right', '', 18) ?>
                </button>
            </form>

            <div class="auth-footer">
                <span><?= t('Hali hisobingiz yo‘qmi?') ?></span>
                <a href="/register"><?= t('Ro‘yxatdan o‘tish') ?></a> <?= t('yoki') ?> <a href="/#ariza"><?= t('Pilotga yozilish') ?></a>
            </div>
        </div>
    </div>
</div>
