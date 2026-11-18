<div class="auth-page">
    <div class="container">
        <div class="auth-card">
            <div class="auth-header">
                <span class="auth-icon">🔐</span>
                <h1>Tizimga Kirish</h1>
                <p>LingoUz ta’lim paneliga xush kelibsiz!</p>
            </div>

            <!-- Quick One-Click Demo Access -->
            <div class="quick-demo-box">
                <span class="quick-title">⚡ Tezkor sinov (Demo kirish):</span>
                <div class="quick-buttons">
                    <a href="/login/quick?as=student" class="btn btn-secondary btn-sm btn-block">
                        👦 Alijon (O‘quvchi) sifatida kirish
                    </a>
                    <a href="/login/quick?as=admin" class="btn btn-dark btn-sm btn-block">
                        👑 Admin (Gulomov) sifatida kirish
                    </a>
                </div>
            </div>

            <div class="divider-text">yoki parolingiz orqali</div>

            <form action="/login" method="POST" class="auth-form">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="email">Elektron pochta (Email)</label>
                    <input type="email" id="email" name="email" required placeholder="ali@lingo.uz yoki admin@lingo.uz">
                </div>

                <div class="form-group">
                    <label for="password">Parol</label>
                    <input type="password" id="password" name="password" required placeholder="Parolni kiriting">
                </div>

                <button type="submit" class="btn btn-primary btn-lg btn-block">
                    Kirish →
                </button>
            </form>

            <div class="auth-footer">
                <span>Hali akkauntingiz yo‘qmi?</span>
                <a href="/register">Ro‘yxatdan o‘tish</a> yoki <a href="/#ariza">Pilotga yozilish</a>
            </div>
        </div>
    </div>
</div>
