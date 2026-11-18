<div class="auth-page">
    <div class="container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-icon-wrap">
                    <?= icon('lock', 'auth-svg-icon', 36) ?>
                </div>
                <h1>Tizimga Kirish</h1>
                <p>LingoUz ta’lim paneliga xush kelibsiz!</p>
            </div>

            <!-- Credentials Hint Card for Testers (Fills the form directly) -->
            <div class="login-hint-card">
                <span class="hint-title"><?= icon('shield-check', '', 14) ?> Sinov hisoblari ma’lumotlari:</span>
                <div class="hint-creds-row">
                    <button type="button" class="btn-fill-creds" onclick="fillCreds('ali@lingo.uz', 'ali123')">
                        <strong>O‘quvchi:</strong> ali@lingo.uz
                    </button>
                    <button type="button" class="btn-fill-creds" onclick="fillCreds('admin@lingo.uz', 'admin123')">
                        <strong>Admin:</strong> admin@lingo.uz
                    </button>
                </div>
            </div>

            <form action="/login" method="POST" class="auth-form" id="loginForm">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="email">Elektron pochta (Email)</label>
                    <input type="email" id="email" name="email" required placeholder="ali@lingo.uz yoki otaona@gmail.com">
                </div>

                <div class="form-group">
                    <label for="password">Parol</label>
                    <input type="password" id="password" name="password" required placeholder="Parolni kiriting">
                </div>

                <button type="submit" class="btn btn-primary btn-lg btn-block">
                    Kirish <?= icon('arrow-right', '', 18) ?>
                </button>
            </form>

            <div class="auth-footer">
                <span>Hali hisobingiz yo‘qmi?</span>
                <a href="/register">Ro‘yxatdan o‘tish</a> yoki <a href="/#ariza">Pilotga yozilish</a>
            </div>
        </div>
    </div>
</div>

<script>
function fillCreds(email, pass) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = pass;
    SoundFX.click();
}
</script>
