<div class="pg">
    <div class="container cert-verify-container">
        <?php if (!empty($isValid)): ?>
            <!-- Verified Card -->
            <div class="card cert-verify-card">
                <div class="verify-badge-icon">
                    <?= icon('check-circle', '', 48) ?>
                </div>

                <span class="tag tag-emerald" style="font-size: 13px; font-weight: 800; padding: 6px 16px; letter-spacing: 1px; text-transform: uppercase;">
                    <?= t('Rasman Tasdiqlangan Sertifikat') ?>
                </span>

                <h1 class="verify-card-title">
                    <?= t('Haqiqiy Diplom / Sertifikat') ?>
                </h1>
                <p class="verify-card-desc">
                    <?= t('Ushbu sertifikat LingoUz Onlayn Ta’lim Akademiyasi ma’lumotlar bazasida ro‘yxatdan o‘tgan va haqiqiyligi to‘liq tasdiqlanadi.') ?>
                </p>

                <div class="cert-verify-details">
                    <div class="cert-detail-grid">
                        <div class="cert-detail-item">
                            <span class="cert-detail-label"><?= t('O‘quvchi (Egasi)') ?></span>
                            <div class="cert-detail-val">
                                <?= e($student['display_name']) ?>
                            </div>
                        </div>
                        <div class="cert-detail-item">
                            <span class="cert-detail-label"><?= t('Sertifikat ID') ?></span>
                            <div class="cert-detail-val mono">
                                <?= e($code) ?>
                            </div>
                        </div>
                        <div class="cert-detail-item">
                            <span class="cert-detail-label"><?= t('Muvaffaqiyatli tamomlangan kurs') ?></span>
                            <div class="cert-detail-val">
                                <?= e(loc($course, 'title')) ?>
                            </div>
                        </div>
                        <div class="cert-detail-item">
                            <span class="cert-detail-label"><?= t('Tamomlangan sana') ?></span>
                            <div class="cert-detail-val">
                                <?= e(date('d.m.Y', strtotime((string) $completedAt))) ?>
                            </div>
                        </div>
                        <div class="cert-detail-item">
                            <span class="cert-detail-label"><?= t('O‘zlashtirish ko‘rsatkichi') ?></span>
                            <div class="cert-detail-val success">
                                <?= (int) ($state['mastery'] ?? 100) ?>% (<?= (int) $state['completed'] ?> / <?= (int) $state['total'] ?> <?= t('dars') ?>)
                            </div>
                        </div>
                        <div class="cert-detail-item">
                            <span class="cert-detail-label"><?= t('Muassasa') ?></span>
                            <div class="cert-detail-val">
                                Gulomov’s Academy • LingoUz
                            </div>
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                    <a href="/certificate?course=<?= (int) $course['id'] ?>" class="btn btn-primary btn-lg">
                        <?= icon('award', '', 18) ?> <?= t('Diplomni to‘liq ko‘rish / Chop etish') ?>
                    </a>
                    <a href="/courses" class="btn btn-outline btn-lg">
                        <?= icon('book-open', '', 18) ?> <?= t('Barcha kurslar') ?>
                    </a>
                </div>
            </div>
        <?php else: ?>
            <!-- Invalid / Not Found Card -->
            <div class="card cert-verify-card is-invalid">
                <div class="verify-badge-icon">
                    <?= icon('alert-triangle', '', 48) ?>
                </div>

                <span class="tag tag-ruby" style="font-size: 13px; font-weight: 800; padding: 6px 16px; letter-spacing: 1px; text-transform: uppercase;">
                    <?= t('Tasdiqlanmadi') ?>
                </span>

                <h1 class="verify-card-title">
                    <?= t('Sertifikat topilmadi yoki hali yopiq') ?>
                </h1>
                <p class="verify-card-desc">
                    <?= e($error ?? t('Kiritilgan identifikatsiya raqamiga mos sertifikat mavjud emas yoki o‘quvchi kurs darslarini hali to‘liq yakunlamagan.')) ?>
                </p>

                <div style="display: inline-block; background: var(--sunken); border: 1px solid var(--line); padding: 8px 20px; border-radius: 8px; font-family: monospace; font-size: 16px; font-weight: 700; color: var(--ink-soft); margin-bottom: 28px;">
                    <?= e($code) ?>
                </div>

                <div>
                    <a href="/courses" class="btn btn-primary btn-lg">
                        <?= icon('arrow-left', '', 18) ?> <?= t('Kurslar katalogiga o‘tish') ?>
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
