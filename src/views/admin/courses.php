<div class="container adm">
    <?php require __DIR__ . '/_side.php'; ?>
    <main class="adm-main">
        <div class="adm-head">
            <div><div class="crumbs"><a href="/admin">Admin</a> / Kurslar</div><h1>Kurslar va darslar</h1></div>
            <a href="/admin/courses/create" class="btn btn-primary btn-sm"><?= icon('sparkles', '', 16) ?> Yangi kurs</a>
        </div>
        <?php if (!$courses): ?>
            <div class="card empty"><div class="e-ico"><?= icon('book-open', '', 26) ?></div>Hali kurs yöq. Birinchi kursni yarating.</div>
        <?php endif; ?>
        <div class="course-grid">
            <?php foreach ($courses as $c): ?>
                <div class="course-card theme-<?= e($c['theme']) ?>">
                    <a href="/admin/courses/<?= (int) $c['id'] ?>" class="course-cover" style="color:#fff">
                        <div class="cover-tags">
                            <span class="tag"><?= $c['status'] === 'published' ? 'Oçiq' : 'Qoralama' ?></span>
                            <span class="tag"><?= $c['category'] === 'culture' ? 'Madaniyat' : 'Til' ?></span>
                        </div>
                        <div class="cover-ico"><?= icon($c['icon'], '', 26) ?></div>
                        <h3><?= e($c['title']) ?></h3>
                        <?php if ($c['subtitle']): ?><p><?= e($c['subtitle']) ?></p><?php endif; ?>
                    </a>
                    <div class="course-body">
                        <div class="course-meta">
                            <span><?= icon('book-open', '', 14) ?> <?= (int) $c['lesson_count'] ?> dars (<?= (int) $c['published_count'] ?> oçiq)</span>
                            <span><?= icon('message-square', '', 14) ?> <?= (int) $c['word_count'] ?> söz</span>
                            <span><?= icon('check-circle', '', 14) ?> <?= (int) $c['question_count'] ?> savol</span>
                            <span><?= icon('users', '', 14) ?> <?= (int) $c['learners'] ?> öquvçi</span>
                        </div>
                        <div class="row-between">
                            <a href="/admin/courses/<?= (int) $c['id'] ?>" class="btn btn-primary btn-sm"><?= icon('layers', '', 14) ?> Darslarni boshqarish</a>
                            <a href="/admin/courses/<?= (int) $c['id'] ?>/edit" class="btn btn-ghost btn-sm"><?= icon('settings', '', 14) ?> Sozlash</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</div>
