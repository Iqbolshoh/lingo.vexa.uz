<?php $isEdit = !empty($course['id']); ?>
<div class="container adm">
    <?php require __DIR__ . '/_side.php'; ?>
    <main class="adm-main">
        <div class="adm-head">
            <div><div class="crumbs"><a href="/admin">Admin</a> / <a href="/admin/courses">Kurslar</a> / <?= $isEdit ? e($course['title']) : 'Yangi' ?></div><h1><?= $isEdit ? 'Kursni tahrirlash' : 'Yangi kurs' ?></h1></div>
        </div>
        <form method="POST" action="<?= $isEdit ? '/admin/courses/' . (int) $course['id'] : '/admin/courses' ?>" class="card">
            <?= csrf_field() ?>
            <div class="adm-form">
                <div class="field full"><label for="title">Kurs nomi *</label><input type="text" id="title" name="title" required maxlength="120" value="<?= e($course['title']) ?>" placeholder="Masalan: O‘zbek tili: Ilk qadamlar"></div>
                <div class="field full"><label for="subtitle">Qisqa tavsif (kartada)</label><input type="text" id="subtitle" name="subtitle" maxlength="160" value="<?= e($course['subtitle']) ?>" placeholder="Salomlashish, oila, raqamlar…"></div>
                <div class="field full"><label for="description">To‘liq tavsif</label><textarea id="description" name="description" maxlength="2000"><?= e($course['description']) ?></textarea></div>
                <div class="field"><label for="level">Daraja</label><input type="text" id="level" name="level" maxlength="20" value="<?= e($course['level']) ?>"></div>
                <div class="field"><label for="age_range">Yosh</label><input type="text" id="age_range" name="age_range" maxlength="40" value="<?= e($course['age_range']) ?>"></div>
                <div class="field"><label for="category">Turi</label>
                    <select id="category" name="category">
                        <option value="language" <?= $course['category'] === 'language' ? 'selected' : '' ?>>Til kursi</option>
                        <option value="culture" <?= $course['category'] === 'culture' ? 'selected' : '' ?>>Madaniyat kursi</option>
                    </select>
                </div>
                <div class="field"><label for="theme">Rang mavzusi</label>
                    <select id="theme" name="theme">
                        <?php foreach (COURSE_THEMES as $k => $label): ?><option value="<?= $k ?>" <?= $course['theme'] === $k ? 'selected' : '' ?>><?= $label ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="field"><label for="status">Holati</label>
                    <select id="status" name="status">
                        <option value="published" <?= $course['status'] === 'published' ? 'selected' : '' ?>>Ochiq (o‘quvchilar ko‘radi)</option>
                        <option value="draft" <?= $course['status'] === 'draft' ? 'selected' : '' ?>>Qoralama (yashirin)</option>
                    </select>
                </div>
                <div class="field"><label for="sort_order">Tartib raqami</label><input type="number" id="sort_order" name="sort_order" min="0" max="999" value="<?= (int) $course['sort_order'] ?>"></div>
                <div class="field full"><label>Belgi</label>
                    <div class="icon-pick">
                        <?php foreach (LESSON_ICONS as $ico): ?>
                            <label title="<?= $ico ?>"><input type="radio" name="icon" value="<?= $ico ?>" <?= $course['icon'] === $ico ? 'checked' : '' ?>><span><?= icon($ico, '', 20) ?></span></label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="form-foot">
                <a href="<?= $isEdit ? '/admin/courses/' . (int) $course['id'] : '/admin/courses' ?>" class="btn btn-ghost">Bekor qilish</a>
                <button type="submit" class="btn btn-primary"><?= icon('check-circle', '', 16) ?> Saqlash</button>
            </div>
        </form>
        <?php if ($isEdit): ?>
            <form method="POST" action="/admin/courses/<?= (int) $course['id'] ?>/delete" class="card" style="margin-top:18px;border-color:#fecdd3" onsubmit="return confirm('Kurs va undagi BARCHA darslar, so‘zlar, savollar va natijalar o‘chiriladi. Davom etasizmi?')">
                <?= csrf_field() ?>
                <div class="row-between">
                    <div><strong style="color:#be123c">Kursni o‘chirish</strong><div class="small muted">Barcha darslar va o‘quvchi natijalari ham o‘chadi. Buni qaytarib bo‘lmaydi.</div></div>
                    <button type="submit" class="btn btn-danger btn-sm"><?= icon('log-out', '', 14) ?> O‘chirish</button>
                </div>
            </form>
        <?php endif; ?>
    </main>
</div>
