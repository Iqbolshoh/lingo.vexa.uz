<div class="auth-page">
    <div class="container">
        <div class="auth-card">
            <div class="auth-header">
                <?= mascot('welcome', 96) ?>
                <h1><?= t('O‘quvchini Ro‘yxatdan O‘tkazish') ?></h1>
                <p><?= t('O‘zingiz yoki farzandingiz uchun bepul profil') ?></p>
            </div>

            <form action="/register" method="POST" class="auth-form">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="display_name"><?= t('Ism yoki taxallus *') ?></label>
                    <input type="text" id="display_name" name="display_name" required placeholder="<?= t('Masalan: Sardor') ?>">
                </div>

                <div class="form-group">
                    <label for="email"><?= t('Email *') ?></label>
                    <input type="email" id="email" name="email" required placeholder="<?= t('otaona@gmail.com') ?>">
                </div>

                <div class="form-group">
                    <label for="password"><?= t('Parol yarating *') ?></label>
                    <input type="password" id="password" name="password" required minlength="6" placeholder="<?= t('Kamida 6 ta belgi') ?>" autocomplete="new-password">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="age_band"><?= t('Yoshi') ?></label>
                        <select id="age_band" name="age_band">
                            <?php foreach (AGE_LABELS as $ageKey => $ageLabel): ?><option value="<?= $ageKey ?>" <?= $ageKey === '18+' ? 'selected' : '' ?>><?= t($ageLabel) ?></option><?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="language_level"><?= t('Til darajasi') ?></label>
                        <select id="language_level" name="language_level">
                            <option value="A0"><?= t('A0 — Boshlang‘ich') ?></option>
                            <option value="A1" selected><?= t('A1 — Tushunadi') ?></option>
                            <option value="A2"><?= t('A2 — Savodxonlik') ?></option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="timezone"><?= t('Vaqt zonasi') ?></label>
                    <select id="timezone" name="timezone">
                        <?php foreach (TIMEZONES as $tzKey => $tzLabel): ?><option value="<?= $tzKey ?>"><?= t($tzLabel) ?></option><?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-lg btn-block">
                    Boshlash va 1-Darsni Ochish <?= icon('arrow-right', '', 18) ?>
                </button>
            </form>

            <div class="auth-footer">
                <span><?= t('Akkauntingiz bormi?') ?></span>
                <a href="/login"><?= t('Tizimga kirish') ?></a>
            </div>
        </div>
    </div>
</div>
