<div class="admin-page">
    <div class="container">
        <div class="admin-header-row">
            <div>
                <span class="admin-badge">BOSHQARUV PANELI</span>
                <h1>LingoUz Boshqaruv Markazi</h1>
                <p>O‘quvchilar progressi, pilot arizalari va darslarni boshqarish</p>
            </div>
            <div class="admin-actions-top">
                <a href="/admin/export" class="btn btn-primary btn-sm">
                    📥 Arizalarni CSV yuklab olish
                </a>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="admin-metrics-grid">
            <div class="metric-card">
                <span class="metric-icon">👦</span>
                <div class="metric-content">
                    <span class="metric-value"><?= count($students) ?></span>
                    <span class="metric-title">Faol O‘quvchilar</span>
                </div>
            </div>

            <div class="metric-card">
                <span class="metric-icon">📋</span>
                <div class="metric-content">
                    <span class="metric-value"><?= count($enrollments) ?></span>
                    <span class="metric-title">Pilot Arizalari</span>
                </div>
            </div>

            <div class="metric-card">
                <span class="metric-icon">⚡</span>
                <div class="metric-content">
                    <span class="metric-value"><?= count($attempts) ?></span>
                    <span class="metric-title">Topshirilgan Testlar</span>
                </div>
            </div>
        </div>

        <!-- Section 1: Students Roster & Manual Unlock (PRD 8.3) -->
        <div class="admin-card">
            <div class="card-title-row">
                <h3>👥 O‘quvchilar va Dars Progressi</h3>
                <span class="table-count">Jami: <?= count($students) ?> nafar</span>
            </div>

            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>O‘quvchi</th>
                            <th>Ota-ona Emaili</th>
                            <th>Yoshi / Darajasi</th>
                            <th>Vaqt zonasi</th>
                            <th>Tugatilgan darslar</th>
                            <th>Qo‘lda dars ochish (Manual Unlock)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $s): ?>
                            <tr>
                                <td>#<?= $s['id'] ?></td>
                                <td><strong><?= e($s['display_name']) ?></strong></td>
                                <td><?= e($s['email']) ?></td>
                                <td><?= e($s['age_band']) ?> yosh (<?= e($s['language_level']) ?>)</td>
                                <td><?= e($s['timezone']) ?></td>
                                <td>
                                    <span class="progress-pill">
                                        <?= (int)$s['completed_lessons'] ?> / 5 ta dars
                                    </span>
                                </td>
                                <td>
                                    <!-- Admin Manual Unlock Form (PRD 8.3) -->
                                    <form action="/admin/unlock" method="POST" class="inline-unlock-form">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="user_id" value="<?= $s['id'] ?>">
                                        <select name="lesson_id" class="select-sm">
                                            <option value="2">2-darsni ochish</option>
                                            <option value="3">3-darsni ochish</option>
                                            <option value="4">4-darsni ochish</option>
                                            <option value="5">5-darsni ochish</option>
                                        </select>
                                        <button type="submit" class="btn btn-outline btn-xs">🔓 Ochish</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 2: Parent Enrollments -->
        <div class="admin-card" style="margin-top: 24px;">
            <div class="card-title-row">
                <h3>📥 Kelib tushgan Pilot Arizalari</h3>
                <a href="/admin/export" class="btn btn-outline btn-xs">CSV Eksport</a>
            </div>

            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ota-ona</th>
                            <th>Email / Telefon</th>
                            <th>Bola Ismi</th>
                            <th>Yoshi</th>
                            <th>Darajasi</th>
                            <th>Vaqt Zonasi</th>
                            <th>Izoh</th>
                            <th>Sana</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($enrollments)): ?>
                            <tr>
                                <td colspan="9" class="text-center">Hozircha arizalar yo‘q. Landing pagedan to‘ldirishingiz mumkin.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($enrollments as $enr): ?>
                                <tr>
                                    <td>#<?= $enr['id'] ?></td>
                                    <td><strong><?= e($enr['parent_name']) ?></strong></td>
                                    <td>
                                        <div><?= e($enr['parent_email']) ?></div>
                                        <small><?= e($enr['parent_phone'] ?? '-') ?></small>
                                    </td>
                                    <td><?= e($enr['child_name']) ?></td>
                                    <td><?= e($enr['child_age']) ?> yosh</td>
                                    <td><span class="badge-tag"><?= e($enr['language_level']) ?></span></td>
                                    <td><?= e($enr['timezone']) ?></td>
                                    <td><small><?= e($enr['notes'] ?? '-') ?></small></td>
                                    <td><?= date('d.m.Y H:i', strtotime($enr['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 3: Recent Quiz Attempts -->
        <div class="admin-card" style="margin-top: 24px;">
            <div class="card-title-row">
                <h3>📝 Test Topshirish Tarixi (80% Qoidasi Nazorati)</h3>
                <span class="table-count">So‘nggi natijalar</span>
            </div>

            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Vaqt</th>
                            <th>O‘quvchi</th>
                            <th>Dars</th>
                            <th>Ball</th>
                            <th>Foiz</th>
                            <th>Holat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($attempts)): ?>
                            <tr>
                                <td colspan="6" class="text-center">Hozircha test topshirilmagan.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($attempts as $a): ?>
                                <tr>
                                    <td><?= date('d.m.Y H:i', strtotime($a['created_at'])) ?></td>
                                    <td><strong><?= e($a['display_name']) ?></strong></td>
                                    <td><?= e($a['lesson_title']) ?></td>
                                    <td><?= $a['score'] ?> / <?= $a['total'] ?></td>
                                    <td><strong><?= $a['percent'] ?>%</strong></td>
                                    <td>
                                        <?php if ($a['passed']): ?>
                                            <span class="badge-status open">✅ O‘tdi (Ochildi)</span>
                                        <?php else: ?>
                                            <span class="badge-status locked">❌ O‘tmadi (&lt;80%)</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
