<?php
$pendingCount = (int) db()->query("SELECT count(*) FROM enrollments WHERE status = 'pending'")->fetchColumn();
$items = [
    ['overview', '/admin', 'layers', 'Umumiy ko‘rinish'],
    ['courses', '/admin/courses', 'book-open', 'Kurslar va darslar'],
    ['students', '/admin/students', 'users', 'O‘quvchilar'],
    ['gradebook', '/admin/gradebook', 'target', 'Baholar jurnali'],
    ['enrollments', '/admin/enrollments', 'message-square', 'Arizalar'],
    ['live', '/admin/live', 'video', 'Jonli darslar'],
];
?>
<aside class="adm-side">
    <div class="adm-brand">
        <img src="/assets/mascot-lingo.svg" alt="" width="38" height="38">
        <div><strong>Boshqaruv</strong><span>LingoUz ustoz paneli</span></div>
    </div>
    <div class="adm-label">Menyu</div>
    <nav class="adm-nav">
        <?php foreach ($items as [$key, $href, $ico, $label]): ?>
            <a href="<?= $href ?>" class="<?= ($adminSection ?? '') === $key ? 'is-active' : '' ?>">
                <?= icon($ico, '', 18) ?> <?= $label ?>
                <?php if ($key === 'enrollments' && $pendingCount > 0): ?><span class="cnt"><?= $pendingCount ?></span><?php endif; ?>
            </a>
        <?php endforeach; ?>
        <a href="/dashboard"><?= icon('arrow-left', '', 18) ?> O‘quvchi ko‘rinishi</a>
    </nav>
</aside>
