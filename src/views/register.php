<div class="auth-page">
    <div class="container">
        <div class="auth-card">
            <div class="auth-header">
                <span class="auth-icon">🎒</span>
                <h1>O‘quvchini Ro‘yxatdan O‘tkazish</h1>
                <p>Ota-ona yoki bola uchun tezkor profil yaratish</p>
            </div>

            <form action="/register" method="POST" class="auth-form">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="display_name">Bolaning ismi yoki taxallusi (Nickname) *</label>
                    <input type="text" id="display_name" name="display_name" required placeholder="Masalan: Sardor">
                </div>

                <div class="form-group">
                    <label for="email">Ota-onaning Emaili *</label>
                    <input type="email" id="email" name="email" required placeholder="otaona@gmail.com">
                </div>

                <div class="form-group">
                    <label for="password">Parol yarating *</label>
                    <input type="password" id="password" name="password" required minlength="4" placeholder="Kamida 4 ta belgi">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="age_band">Yoshi</label>
                        <select id="age_band" name="age_band">
                            <option value="7-8">7–8 yosh</option>
                            <option value="9-10" selected>9–10 yosh</option>
                            <option value="11">11 yosh</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="language_level">Til darajasi</label>
                        <select id="language_level" name="language_level">
                            <option value="A0">A0 — Boshlang‘ich</option>
                            <option value="A1" selected>A1 — Tushunadi</option>
                            <option value="A2">A2 — Savodxonlik</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="timezone">AQSh Vaqt zonasi</label>
                    <select id="timezone" name="timezone">
                        <option value="America/New_York (EST)">Eastern Time (New York, FL)</option>
                        <option value="America/Chicago (CST)">Central Time (Chicago, TX)</option>
                        <option value="America/Denver (MST)">Mountain Time (Denver)</option>
                        <option value="America/Los_Angeles (PST)">Pacific Time (Los Angeles, WA)</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-accent btn-lg btn-block">
                    🚀 Boshlash va 1-Darsni Ochish
                </button>
            </form>

            <div class="auth-footer">
                <span>Akkauntingiz bormi?</span>
                <a href="/login">Tizimga kirish</a>
            </div>
        </div>
    </div>
</div>
