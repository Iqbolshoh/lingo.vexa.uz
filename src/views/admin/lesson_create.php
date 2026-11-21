<?php $lesson = ['title' => '', 'title_en' => '', 'objective' => '', 'summary' => '', 'video_url' => '', 'duration_min' => 5, 'reading_uz' => '', 'reading_en' => '', 'culture_title' => '', 'culture_note' => '', 'passing_score' => 80, 'status' => 'draft', 'icon' => 'book-open']; ?>
<div class="container adm">
    <?php require __DIR__ . '/_side.php'; ?>
    <main class="adm-main">
        <div class="adm-head">
            <div><div class="crumbs"><a href="/admin">Admin</a> / <a href="/admin/courses">Kurslar</a> / <a href="/admin/courses/<?= (int) $course['id'] ?>"><?= e($course['title']) ?></a> / Yangi dars</div><h1>Yangi dars</h1></div>
        </div>
        <form method="POST" action="/admin/courses/<?= (int) $course['id'] ?>/lessons" class="card">
            <?= csrf_field() ?>
            <?php require __DIR__ . '/_lesson_fields.php'; ?>
            <div class="form-foot">
                <span class="left small muted">Saqlagandan so‘ng lug‘at va test savollarini qo‘shasiz.</span>
                <a href="/admin/courses/<?= (int) $course['id'] ?>" class="btn btn-ghost">Bekor qilish</a>
                <button type="submit" class="btn btn-primary"><?= icon('check-circle', '', 16) ?> Darsni yaratish</button>
            </div>
        </form>
    </main>
</div>
