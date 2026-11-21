<div class="auth-page">
    <div class="container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-icon-wrap">
                    <?= icon('user', 'auth-svg-icon', 36) ?>
                </div>
                <h1>O‘quvchini Ro‘yxatdan O‘tkazish</h1>
                <p>O‘zingiz yoki farzandingiz uchun bepul profil</p>
            </div>

            <form action="/register" method="POST" class="auth-form">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="display_name">Ism yoki taxallus *</label>
                    <input type="text" id="display_name" name="display_name" required placeholder="Masalan: Sardor">
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required placeholder="otaona@gmail.com">
                </div>

                <div class="form-group">
                    <label for="password">Parol yarating *</label>
                    <input type="password" id="password" name="password" required minlength="6" placeholder="Kamida 6 ta belgi" autocomplete="new-password">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="age_band">Yoshi</label>
                        <select id="age_band" name="age_band">
                            <?php foreach (AGE_LABELS as $ageKey => $ageLabel): ?><option value="<?= $ageKey ?>" <?= $ageKey === '18+' ? 'selected' : '' ?>><?= $ageLabel ?></option><?php endforeach; ?>
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
                    <label for="timezone">Vaqt zonasi</label>
                    <select id="timezone" name="timezone">
                        <?php foreach (TIMEZONES as $tzKey => $tzLabel): ?><option value="<?= $tzKey ?>"><?= $tzLabel ?></option><?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-lg btn-block">
                    Boshlash va 1-Darsni Ochish <?= icon('arrow-right', '', 18) ?>
                </button>
            </form>

            <div class="auth-footer">
                <span>Akkauntingiz bormi?</span>
                <a href="/login">Tizimga kirish</a>
            </div>
        </div>
    </div>
</div>
