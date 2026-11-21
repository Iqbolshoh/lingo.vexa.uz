<div class="adm-form">
    <div class="field"><label for="title">Dars nomi (o‘zbekcha) *</label><input type="text" id="title" name="title" required maxlength="160" value="<?= e($lesson['title']) ?>"></div>
    <div class="field"><label for="title_en">Dars nomi (inglizcha)</label><input type="text" id="title_en" name="title_en" maxlength="160" value="<?= e($lesson['title_en']) ?>"></div>
    <div class="field full"><label for="objective">Maqsad *</label><input type="text" id="objective" name="objective" required maxlength="600" value="<?= e($lesson['objective']) ?>" placeholder="Bola … ayta oladi"></div>
    <div class="field full"><label for="summary">Qisqacha mazmun</label><textarea id="summary" name="summary" maxlength="1200" style="min-height:70px"><?= e($lesson['summary']) ?></textarea></div>
    <div class="field"><label for="video_url">Video havolasi</label><input type="url" id="video_url" name="video_url" maxlength="500" value="<?= e($lesson['video_url']) ?>" placeholder="https://youtu.be/…"><div class="hint">YouTube, Vimeo yoki to‘g‘ridan-to‘g‘ri .mp4 havola. Bo‘sh bo‘lsa faqat mikro-sahna ko‘rinadi.</div></div>
    <div class="field"><label for="duration_min">Davomiyligi (daqiqa)</label><input type="number" id="duration_min" name="duration_min" min="1" max="120" value="<?= (int) $lesson['duration_min'] ?>"></div>
    <div class="field"><label for="reading_uz">O‘qish matni (o‘zbekcha)</label><textarea id="reading_uz" name="reading_uz" maxlength="4000" placeholder="Har bir gap yangi qatorda"><?= e($lesson['reading_uz']) ?></textarea><div class="hint">Har qator — alohida gap. O‘quvchi bosganda ovoz bilan o‘qiladi.</div></div>
    <div class="field"><label for="reading_en">Tarjimasi (inglizcha)</label><textarea id="reading_en" name="reading_en" maxlength="4000" placeholder="Same line order"><?= e($lesson['reading_en']) ?></textarea><div class="hint">Qatorlar o‘zbekcha matn bilan bir xil tartibda bo‘lsin.</div></div>
    <div class="field"><label for="culture_title">«Bilasizmi?» sarlavhasi</label><input type="text" id="culture_title" name="culture_title" maxlength="160" value="<?= e($lesson['culture_title']) ?>" placeholder="Masalan: Non — muqaddas"></div>
    <div class="field"><label for="passing_score">O‘tish bali (%)</label><input type="number" id="passing_score" name="passing_score" min="50" max="100" value="<?= (int) $lesson['passing_score'] ?>"></div>
    <div class="field full"><label for="culture_note">Madaniyat fakti</label><textarea id="culture_note" name="culture_note" maxlength="2000" placeholder="O‘zbekiston va Amerika madaniyatini solishtiruvchi qiziqarli fakt"><?= e($lesson['culture_note']) ?></textarea></div>
    <div class="field"><label for="status">Holati</label>
        <select id="status" name="status">
            <option value="published" <?= $lesson['status'] === 'published' ? 'selected' : '' ?>>Ochiq</option>
            <option value="draft" <?= $lesson['status'] === 'draft' ? 'selected' : '' ?>>Qoralama (o‘quvchilar ko‘rmaydi)</option>
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
