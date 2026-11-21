<div class="adm-form">
    <div class="field"><label for="title">Dars nomi (özbekça) *</label><input type="text" id="title" name="title" required maxlength="160" value="<?= e($lesson['title']) ?>"></div>
    <div class="field"><label for="title_en">Dars nomi (inglizcha)</label><input type="text" id="title_en" name="title_en" maxlength="160" value="<?= e($lesson['title_en']) ?>"></div>
    <div class="field full"><label for="objective">Maqsad *</label><input type="text" id="objective" name="objective" required maxlength="600" value="<?= e($lesson['objective']) ?>" placeholder="Bola … ayta oladi"></div>
    <div class="field full"><label for="objective_en">Maqsad (inglizcha)</label><input type="text" id="objective_en" name="objective_en" maxlength="600" value="<?= e($lesson['objective_en'] ?? '') ?>" placeholder="Learners can …"></div>
    <div class="field full"><label for="summary">Qisqaça mazmun</label><textarea id="summary" name="summary" maxlength="1200" style="min-height:70px"><?= e($lesson['summary']) ?></textarea></div>
    <div class="field"><label for="video_url">Video havolasi</label><input type="url" id="video_url" name="video_url" maxlength="500" value="<?= e($lesson['video_url']) ?>" placeholder="https://youtu.be/…"><div class="hint">YouTube, Vimeo yoki töğridan-töğri .mp4 havola. Böş bölsa faqat mikro-sahna körinadi.</div></div>
    <div class="field"><label for="duration_min">Davomiyligi (daqiqa)</label><input type="number" id="duration_min" name="duration_min" min="1" max="120" value="<?= (int) $lesson['duration_min'] ?>"></div>
    <div class="field"><label for="reading_uz">Öqiş matni (özbekça)</label><textarea id="reading_uz" name="reading_uz" maxlength="4000" placeholder="Har bir gap yangi qatorda"><?= e($lesson['reading_uz']) ?></textarea><div class="hint">Har qator — alohida gap. Öquvçi bosganda ovoz bilan o‘qiladi.</div></div>
    <div class="field"><label for="reading_en">Tarjimasi (inglizcha)</label><textarea id="reading_en" name="reading_en" maxlength="4000" placeholder="Same line order"><?= e($lesson['reading_en']) ?></textarea><div class="hint">Qatorlar özbekça matn bilan bir xil tartibda bölsin.</div></div>
    <div class="field"><label for="culture_title">«Bilasizmi?» sarlavhasi</label><input type="text" id="culture_title" name="culture_title" maxlength="160" value="<?= e($lesson['culture_title']) ?>" placeholder="Masalan: Non — muqaddas"></div>
    <div class="field"><label for="passing_score">Ötiş bali (%)</label><input type="number" id="passing_score" name="passing_score" min="50" max="100" value="<?= (int) $lesson['passing_score'] ?>"></div>
    <div class="field full"><label for="culture_note">Madaniyat fakti</label><textarea id="culture_note" name="culture_note" maxlength="2000" placeholder="O‘zbekiston va Amerika madaniyatini solishtiruvchi qiziqarli fakt"><?= e($lesson['culture_note']) ?></textarea></div>
    <div class="field"><label for="culture_title_en">«Did you know?» sarlavhasi (inglizcha)</label><input type="text" id="culture_title_en" name="culture_title_en" maxlength="160" value="<?= e($lesson['culture_title_en'] ?? '') ?>"></div>
    <div class="field"></div>
    <div class="field full"><label for="culture_note_en">Madaniyat fakti (inglizcha)</label><textarea id="culture_note_en" name="culture_note_en" maxlength="2000"><?= e($lesson['culture_note_en'] ?? '') ?></textarea></div>
    <div class="field"><label for="status">Holati</label>
        <select id="status" name="status">
            <option value="published" <?= $lesson['status'] === 'published' ? 'selected' : '' ?>>Oçiq</option>
            <option value="draft" <?= $lesson['status'] === 'draft' ? 'selected' : '' ?>>Qoralama (öquvçilar körmaydi)</option>
        </select>
    </div>
    <div class="field"><label>Belgi</label>
        <div class="icon-pick">
            <?php foreach (LESSON_ICONS as $ico): ?>
                <label title="<?= $ico ?>"><input type="radio" name="icon" value="<?= $ico ?>" <?= $lesson['icon'] === $ico ? 'checked' : '' ?>><span><?= icon($ico, '', 18) ?></span></label>
            <?php endforeach; ?>
        </div>
    </div>
</div>
