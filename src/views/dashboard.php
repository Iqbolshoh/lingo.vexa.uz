<div class="dashboard-page">
    <div class="container">
        <!-- Top Welcome Card -->
        <div class="welcome-card registon-welcome">
            <div class="welcome-text">
                <span class="welcome-badge"><?= icon('registon', '', 14) ?> O‘quvchi Fazosi</span>
                <h1 class="welcome-title">
                    Assalomu alaykum, <span class="student-name"><?= e($user['display_name']) ?></span>!
                </h1>
                <p class="welcome-sub">
                    Bugun qaysi yangi o‘zbekcha so‘zlarni o‘rganamiz? Sarguzashtni davom ettirishga tayyormisiz?
                </p>

                <div class="dashboard-cta-row">
                    <a href="/lessons/<?= $nextLessonId ?>" class="btn btn-primary btn-lg pulse-hover">
                        <?= icon('play', '', 18) ?> Darsni Davom Ettirish (<?= $nextLessonId ?>-Dars)
                    </a>
                    <a href="/live" class="btn btn-secondary btn-lg">
                        <?= icon('video', '', 18) ?> Jonli Zoom Xonasi
                    </a>
                    <a href="/certificate" class="btn btn-accent btn-lg">
                        <?= icon('award', '', 18) ?> Diplomingizni ko‘rish
                    </a>
                </div>
            </div>

            <div class="welcome-progress-box">
                <div class="progress-circle-wrap">
                    <div class="progress-stat-header">
                        <span class="stat-number-big"><?= $progressPercent ?>%</span>
                        <span class="stat-subtitle">Umumiy o‘zlashtirish</span>
                    </div>
                    <div class="progress-bar-track">
                        <div class="progress-bar-fill" style="width: <?= $progressPercent ?>%;"></div>
                    </div>
                    <div class="progress-subtext">
                        <strong><?= $completedCount ?></strong> / <?= $totalLessons ?> ta dars a’lo yakunlandi
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-grid">
            <!-- Left: 5 Lessons List -->
            <div class="dashboard-main-col">
                <div class="section-heading-row">
                    <h2><?= icon('book-open', '', 22) ?> Darslar Xaritasi (MVP Kursi)</h2>
                    <span class="rule-hint">Qoida: Keyingi dars ochilishi uchun testdan <strong>80%+</strong> kerak!</span>
                </div>

                <div class="lessons-track">
                    <?php foreach ($lessons as $l): ?>
                        <?php
                            $status = $l['user_status'];
                            $isCompleted = ($status === 'completed');
                            $isAvailable = ($status === 'available');
                            $isLocked = ($status === 'locked');
                        ?>
                        <div class="lesson-track-card status-<?= $status ?>">
                            <div class="card-seq-col">
                                <div class="seq-circle">
                                    <?php if ($isCompleted): ?>
                                        <?= icon('check-circle', 'icon-green', 22) ?>
                                    <?php elseif ($isAvailable): ?>
                                        <?= $l['sequence_number'] ?>
                                    <?php else: ?>
                                        <?= icon('lock', 'icon-gray', 20) ?>
                                    <?php endif; ?>
                                </div>
                                <div class="seq-connector"></div>
                            </div>

                            <div class="card-body-col">
                                <div class="card-meta">
                                    <span class="meta-seq"><?= $l['sequence_number'] ?>-DARS</span>
                                    <span class="meta-time"><?= icon('clock', '', 13) ?> <?= $l['duration_min'] ?> daqiqa</span>
                                    <?php if ($isCompleted): ?>
                                        <span class="badge-status completed">
                                            <?= icon('trophy', '', 14) ?> A’lo natija: <?= $l['best_score'] ?>%
                                        </span>
                                    <?php elseif ($isAvailable): ?>
                                        <span class="badge-status open">
                                            <?= icon('sparkles', '', 14) ?> Boshlashga tayyor
                                        </span>
                                    <?php else: ?>
                                        <span class="badge-status locked">
                                            <?= icon('lock', '', 14) ?> Qulflangan
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <h3 class="track-lesson-title"><?= e($l['title']) ?></h3>
                                <p class="track-lesson-en"><?= e($l['title_en']) ?></p>
                                <p class="track-lesson-obj">🎯 <?= e($l['objective']) ?></p>

                                <div class="card-actions">
                                    <?php if ($isCompleted): ?>
                                        <a href="/lessons/<?= $l['id'] ?>" class="btn btn-sm btn-outline">
                                            <?= icon('rotate-cw', '', 14) ?> Darsni qayta ko‘rish
                                        </a>
                                        <a href="/lessons/<?= $l['id'] ?>#test" class="btn btn-sm btn-secondary">
                                            <?= icon('check-circle', '', 14) ?> Test natijalari
                                        </a>
                                    <?php elseif ($isAvailable): ?>
                                        <a href="/lessons/<?= $l['id'] ?>" class="btn btn-sm btn-primary">
                                            <?= icon('play', '', 14) ?> Darsni boshlash
                                        </a>
                                    <?php else: ?>
                                        <div class="locked-note">
                                            <span><?= icon('lock', '', 13) ?> Ushbu darsni ochish uchun avval <strong><?= $l['sequence_number'] - 1 ?>-dars</strong> testidan kamida <strong>80%</strong> to‘plang.</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Right: Badges & Live Session -->
            <div class="dashboard-side-col">
                <!-- Mascot Lingo Daily Tip -->
                <div class="side-card mascot-tip-card">
                    <div class="mascot-tip-inner">
                        <img src="/assets/mascot-lingo.svg" alt="Lingo" width="68" height="68">
                        <div>
                            <div class="mascot-tip-title">Lingo maslahati <?= icon('sparkles', '', 14) ?></div>
                            <p class="mascot-tip-text">
                                «Har kuni 1 marta yangi so‘zning ovozini eshitib, baland ovozda qaytarsang, xotirang yanada mustahkam bo‘ladi!»
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Badges Box -->
                <div class="side-card badges-card">
                    <div class="side-card-header">
                        <h3><?= icon('award', '', 18) ?> Yutuqlar va Nishonlar</h3>
                        <span class="badge-count">
                            <?= count(array_filter($badges, fn($b) => !empty($b['earned_at']))) ?> / <?= count($badges) ?>
                        </span>
                    </div>

                    <div class="badges-list">
                        <?php foreach ($badges as $b): ?>
                            <?php $isEarned = !empty($b['earned_at']); ?>
                            <div class="badge-item <?= $isEarned ? 'earned' : 'locked' ?>" title="<?= e($b['description']) ?>">
                                <div class="badge-icon-wrap">
                                    <span class="badge-svg-wrap"><?= $isEarned ? icon('trophy', 'icon-gold', 22) : icon('lock', 'icon-gray', 20) ?></span>
                                </div>
                                <div class="badge-text-wrap">
                                    <div class="badge-title">
                                        <?= e($b['title']) ?>
                                        <?php if ($isEarned): ?>
                                            <span class="check-mark">✓</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="badge-desc"><?= e($b['description']) ?></div>
                                    <?php if ($isEarned): ?>
                                        <div class="badge-date">Olingan: <?= date('d.m.Y', strtotime($b['earned_at'])) ?></div>
                                    <?php else: ?>
                                        <div class="badge-lock-hint"><?= icon('lock', '', 11) ?> Hali olinmadi</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Live Zoom Session Reminder -->
                <?php if ($liveSession): ?>
                    <div class="side-card live-reminder-card">
                        <div class="live-tag">
                            <span class="pulse-dot red"></span>
                            <span>JONLI MULOQOT XONASI</span>
                        </div>
                        <h4><?= e($liveSession['title']) ?></h4>
                        <p class="live-desc"><?= e($liveSession['description']) ?></p>

                        <div class="live-meta-row">
                            <div><?= icon('calendar', '', 14) ?> <strong>Sana:</strong> <?= date('d-M, H:i', strtotime($liveSession['starts_at'])) ?></div>
                            <div><?= icon('clock', '', 14) ?> <strong>Vaqt:</strong> <?= e($liveSession['timezone']) ?></div>
                            <div><?= icon('user', '', 14) ?> <strong>Ustoz:</strong> <?= e($liveSession['mentor_name']) ?></div>
                        </div>

                        <a href="/live" class="btn btn-accent btn-block" style="margin-top: 14px;">
                            <?= icon('video', '', 16) ?> Zoom Xonasiga Kirish
                        </a>
                    </div>
                <?php endif; ?>

                <!-- User profile info -->
                <div class="side-card profile-mini-card">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                        <h4 style="margin:0;"><?= icon('user', '', 16) ?> O‘quvchi Ma’lumoti</h4>
                        <a href="/profile" class="btn btn-xs btn-outline" title="Tahrirlash"><?= icon('settings', '', 12) ?> Sozlamalar</a>
                    </div>
                    <p><strong>Ism:</strong> <?= e($user['display_name']) ?></p>
                    <p><strong>Yoshi:</strong> <?= e($user['age_band']) ?> yosh</p>
                    <p><strong>Daraja:</strong> <?= e($user['language_level']) ?></p>
                    <p><strong>Vaqt zonasi:</strong> <?= e($user['timezone']) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
