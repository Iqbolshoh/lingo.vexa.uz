<div class="pg">
    <div class="container" style="max-width: 760px; margin: 40px auto;">
        <?php if (!empty($isValid)): ?>
            <!-- Verified Card -->
            <div class="card cert-verify-card" style="padding: 40px 32px; text-align: center; border-top: 6px solid #059669; box-shadow: var(--shadow-lg);">
                <div class="verify-badge-icon" style="width: 80px; height: 80px; margin: 0 auto 16px; border-radius: 50%; background: #dcfce7; display: flex; align-items: center; justify-content: center; color: #16a34a;">
                    <?= icon('check-circle', '', 48) ?>
                </div>

                <span class="tag tag-emerald" style="font-size: 13px; font-weight: 800; padding: 6px 16px; letter-spacing: 1px; text-transform: uppercase;">
                    <?= t('Rasman Tasdiqlangan Sertifikat') ?>
                </span>

                <h1 style="font-family: var(--font-heading); font-size: 32px; font-weight: 800; color: var(--ink); margin: 18px 0 6px;">
                    <?= t('Haqiqiy Diplom / Sertifikat') ?>
                </h1>
                <p style="color: #64748b; font-size: 15px; margin: 0 0 28px;">
                    <?= t('Ushbu sertifikat LingoUz Onlayn Ta’lim Akademiyasi ma’lumotlar bazasida ro‘yxatdan o‘tgan va haqiqiyligi to‘liq tasdiqlanadi.') ?>
                </p>

                <div class="cert-verify-details" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 24px; text-align: left; margin-bottom: 28px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div>
                            <span style="font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 700;"><?= t('O‘quvchi (Egasi)') ?></span>
                            <div style="font-size: 18px; font-weight: 800; color: #0f172a; margin-top: 2px;">
                                <?= e($student['display_name']) ?>
                            </div>
                        </div>
                        <div>
                            <span style="font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 700;"><?= t('Sertifikat ID') ?></span>
                            <div style="font-size: 18px; font-weight: 800; color: #0284c7; font-family: monospace; margin-top: 2px;">
                                <?= e($code) ?>
                            </div>
                        </div>
                        <div>
                            <span style="font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 700;"><?= t('Muvaffaqiyatli tamomlangan kurs') ?></span>
                            <div style="font-size: 16px; font-weight: 700; color: #0f172a; margin-top: 2px;">
                                <?= e(loc($course, 'title')) ?>
                            </div>
                        </div>
                        <div>
                            <span style="font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 700;"><?= t('Tamomlangan sana') ?></span>
                            <div style="font-size: 16px; font-weight: 700; color: #0f172a; margin-top: 2px;">
                                <?= e(date('d.m.Y', strtotime((string) $completedAt))) ?>
                            </div>
                        </div>
                        <div>
                            <span style="font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 700;"><?= t('O‘zlashtirish ko‘rsatkichi') ?></span>
                            <div style="font-size: 16px; font-weight: 800; color: #059669; margin-top: 2px;">
                                <?= (int) ($state['mastery'] ?? 100) ?>% (<?= (int) $state['completed'] ?> / <?= (int) $state['total'] ?> <?= t('dars') ?>)
                            </div>
                        </div>
                        <div>
                            <span style="font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 700;"><?= t('Muassasa') ?></span>
                            <div style="font-size: 15px; font-weight: 700; color: #0f172a; margin-top: 2px;">
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
            <div class="card cert-verify-card" style="padding: 40px 32px; text-align: center; border-top: 6px solid #ef4444; box-shadow: var(--shadow-lg);">
                <div class="verify-badge-icon" style="width: 80px; height: 80px; margin: 0 auto 16px; border-radius: 50%; background: #fee2e2; display: flex; align-items: center; justify-content: center; color: #dc2626;">
                    <?= icon('alert-triangle', '', 48) ?>
                </div>

                <span class="tag tag-ruby" style="font-size: 13px; font-weight: 800; padding: 6px 16px; letter-spacing: 1px; text-transform: uppercase;">
                    <?= t('Tasdiqlanmadi') ?>
                </span>

                <h1 style="font-family: var(--font-heading); font-size: 28px; font-weight: 800; color: var(--ink); margin: 18px 0 8px;">
                    <?= t('Sertifikat topilmadi yoki hali yopiq') ?>
                </h1>
                <p style="color: #64748b; font-size: 15px; max-width: 520px; margin: 0 auto 24px;">
                    <?= e($error ?? t('Kiritilgan identifikatsiya raqamiga mos sertifikat mavjud emas yoki o‘quvchi kurs darslarini hali to‘liq yakunlamagan.')) ?>
                </p>

                <div style="display: inline-block; background: #f1f5f9; padding: 8px 20px; border-radius: 8px; font-family: monospace; font-size: 16px; font-weight: 700; color: #475569; margin-bottom: 28px;">
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
