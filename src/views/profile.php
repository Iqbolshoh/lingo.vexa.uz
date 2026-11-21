<div class="dashboard-page">
    <div class="container" style="max-width: 800px;">
        <div class="section-header" style="margin-bottom: 24px;">
            <a href="/dashboard" class="btn btn-outline btn-sm" style="margin-bottom: 12px;">← O‘quvchi paneliga qaytish</a>
            <h1 class="section-title">Profil Sozlamalari</h1>
            <p class="section-desc">O‘quvchi ma’lumotlarini tahrirlash va hisobni boshqarish</p>
        </div>

        <div class="admin-card">
            <div class="profile-header-row" style="display:flex; align-items:center; gap:16px; margin-bottom:24px; padding-bottom:18px; border-bottom:1px solid var(--border);">
                <div style="width:64px; height:64px; border-radius:50%; background:var(--registon-light); color:var(--registon-blue); display:flex; align-items:center; justify-content:center; font-size:28px; box-shadow:var(--shadow-sm);">
                    <?= icon('user', '', 32) ?>
                </div>
                <div>
                    <h2 style="font-family:var(--font-heading); font-size:24px; margin-bottom:2px; color:var(--registon-dark);"><?= e($user['display_name']) ?></h2>
                    <span style="font-size:13px; color:#475569; font-weight:600;"><?= e($user['email']) ?> • <strong style="text-transform:uppercase; color:var(--registon-blue);"><?= e($user['role']) ?></strong></span>
                </div>
            </div>

            <form action="/profile" method="POST" class="auth-form">
                <?= csrf_field() ?>

                <div class="form-row">
                    <div class="form-group">
                        <label for="display_name">O‘quvchining ismi yoki taxallusi (Nickname) *</label>
                        <input type="text" id="display_name" name="display_name" value="<?= e($user['display_name']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email (o‘zgartirib bo‘lmaydi)</label>
                        <input type="email" id="email" value="<?= e($user['email']) ?>" disabled style="background:#f1f5f9; cursor:not-allowed;">
                    </div>
                </div>

                <div class="form-row three-cols">
                    <div class="form-group">
                        <label for="age_band">Yoshi</label>
                        <select id="age_band" name="age_band">
                            <?php foreach (AGE_LABELS as $ageKey => $ageLabel): ?><option value="<?= $ageKey ?>" <?= $user['age_band'] === $ageKey ? 'selected' : '' ?>><?= $ageLabel ?></option><?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="language_level">Til Darajasi</label>
                        <select id="language_level" name="language_level">
                            <option value="A0" <?= $user['language_level'] === 'A0' ? 'selected' : '' ?>>A0 — Boshlang‘ich</option>
                            <option value="A1" <?= $user['language_level'] === 'A1' ? 'selected' : '' ?>>A1 — Tushunadi</option>
                            <option value="A2" <?= $user['language_level'] === 'A2' ? 'selected' : '' ?>>A2 — Savodxonlik</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="timezone">Vaqt Zonasi</label>
                        <select id="timezone" name="timezone">
                            <?php foreach (TIMEZONES as $tzKey => $tzLabel): ?><option value="<?= $tzKey ?>" <?= normalize_timezone($user['timezone']) === $tzKey ? 'selected' : '' ?>><?= $tzLabel ?></option><?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="padding-top:14px; border-top:1px solid var(--border);">
                    <label for="new_password">Yangi parol (faqat parolni o‘zgartirmoqchi bo‘lsangiz to‘ldiring):</label>
                    <input type="password" id="new_password" name="new_password" placeholder="Yangi parol (ixtiyoriy)">
                </div>

                <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:20px; flex-wrap:wrap;">
                    <?php if ($user['role'] === 'student'): ?>
                        <a href="/onboarding" class="btn btn-ghost btn-lg" style="margin-right:auto"><?= icon('heart', '', 16) ?> Qiziqishlar va kunlik maqsad</a>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary btn-lg">
                        Saqlash
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
