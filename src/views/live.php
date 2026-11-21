<div class="live-page">
    <div class="container">
        <div class="live-header-banner">
            <span class="live-pill"><?= icon('video', '', 14) ?> JONLI EFIR VA NUTQIY AMALIYOT</span>
            <h1>Zoom Live Speaking Room</h1>
            <p>Ustoz va boshqa o‘quvchilar bilan o‘zbek tilida jonli muloqot. Vaqt Toshkent, Yevropa va AQSh bo‘yicha ko‘rsatiladi.</p>
        </div>

        <div class="live-grid">
            <div class="live-main-card">
                <?php if (!$sessions): ?>
                    <div class="card empty"><div class="e-ico"><?= icon('calendar', '', 26) ?></div>Hozircha rejalashtirilgan jonli dars yo‘q. Ustoz yangi vaqtni e’lon qilganda shu yerda paydo bo‘ladi.</div>
                <?php endif; ?>
                <?php foreach ($sessions as $s): ?>
                    <div class="session-box registon-session-box">
                        <div class="session-status-badge">
                            <span class="pulse-dot red"></span>
                            Rejalashtirilgan sessiya
                        </div>

                        <h2><?= e($s['title']) ?></h2>
                        <p class="session-desc"><?= e($s['description']) ?></p>

                        <p class="small muted" style="margin:-4px 0 10px;font-weight:700"><?= icon('calendar', '', 14) ?> <?= e(uz_date($s['starts_at'], normalize_timezone($user['timezone']))) ?></p>
                        <div class="session-time-grid">
                            <?php $zoneNames = ['Toshkent' => 'O‘zbekiston (Toshkent)', 'London' => 'Yevropa (London)', 'ET' => 'AQSh Sharqiy (NY)', 'CT' => 'AQSh Markaziy (Chicago)', 'PT' => 'AQSh G‘arbiy (LA)']; ?>
                            <?php foreach (live_time_labels($s['starts_at']) as $zone => $time): ?>
                                <div class="time-block">
                                    <span class="time-zone"><?= e($zoneNames[$zone] ?? $zone) ?></span>
                                    <span class="time-val"><?= e($time) ?> <?= e($zone) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="session-host-info">
                            <span><?= icon('user', '', 16) ?> Mas’ul Mentor: <strong><?= e($s['mentor_name']) ?></strong></span>
                        </div>

                        <div class="session-action">
                            <a href="<?= e($s['zoom_url']) ?>" target="_blank" rel="noopener" class="btn btn-primary btn-xl pulse-hover">
                                <?= icon('video', '', 20) ?> Zoom Xonasiga Ulanish
                            </a>
                            <span class="zoom-note">Parol va kirish huquqi faqat ro‘yxatdan o‘tgan oilalarga beriladi.</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="live-rules-card">
                <h3><?= icon('shield-check', '', 20) ?> Xavfsizlik qoidalari</h3>
                <ul class="rules-list">
                    <li>
                        <strong>Kutish xonasi (Waiting Room):</strong> Har bir qatnashchi mentor tomonidan tekshirilib, xonaga qabul qilinadi.
                    </li>
                    <li>
                        <strong>Bolalar guruhlari alohida:</strong> bolalar va kattalar darslari alohida o‘tadi; bolalar darsida ota-onaning birga o‘tirishi tavsiya etiladi.
                    </li>
                    <li>
                        <strong>Ochiq chat yo‘qligi:</strong> Qatnashchilar o‘rtasida shaxsiy xabar yozish xavfsizlik uchun yopilgan.
                    </li>
                    <li>
                        <strong>Yozib olish siyosati:</strong> Darslar qatnashchilarning roziligisiz yozib olinmaydi va ommaga tarqatilmaydi.
                    </li>
                </ul>

                <div class="badge-speaker-earned">
                    <?= icon('award', '', 18) ?> <em>Ushbu xonaga kirganingiz uchun profilingizga <strong>«Jonli Muloqotchi»</strong> nishoni qo‘shildi!</em>
                </div>
            </div>
        </div>
    </div>
</div>
