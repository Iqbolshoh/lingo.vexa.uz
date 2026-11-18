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

            <form action="/login" method="POST" class="auth-form" id="loginForm">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="email">Elektron pochta yoki Login</label>
                    <input type="text" id="email" name="email" required placeholder="demo@lingo.uz yoki demo">
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
