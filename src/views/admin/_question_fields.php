<?php /** @var array $q question row (or defaults) */ $qType = $q['type'] ?? 'choice'; ?>
<div class="adm-form type-fields" data-type="<?= e($qType) ?>">
    <div class="field"><label>Savol turi</label>
        <select name="type" class="js-qtype">
            <?php foreach (QUESTION_TYPES as $k => $label): ?><option value="<?= $k ?>" <?= $qType === $k ? 'selected' : '' ?>><?= $label ?></option><?php endforeach; ?>
        </select>
    </div>
    <div class="field" data-for="listen"><label>Ovozda aytiladigan matn (o‘zbekcha) *</label><input type="text" name="audio_text" maxlength="200" value="<?= e($q['audio_text'] ?? '') ?>" placeholder="Rahmat"></div>
    <div class="field" data-for="choice build fill"></div>
    <div class="field full"><label>Savol / ko‘rsatma (o‘zbekcha) *</label><input type="text" name="prompt" required maxlength="400" value="<?= e($q['prompt'] ?? '') ?>" placeholder="Masalan: «Thank you» o‘zbekcha qanday?"></div>
    <div class="field full"><label>Inglizcha izoh <span class="muted" data-for="build">(gap tuzishda — tarjima qilinadigan gap)</span></label><input type="text" name="prompt_en" maxlength="400" value="<?= e($q['prompt_en'] ?? '') ?>"></div>
    <div class="field full" data-for="choice listen build"><label><span data-for="choice listen">Variantlar (har biri yangi qatorda) *</span><span data-for="build">Chalg‘ituvchi so‘zlar (ixtiyoriy, har biri yangi qatorda)</span></label><textarea name="options" style="min-height:90px"><?= e(implode("\n", $q['options'] ?? [])) ?></textarea></div>
    <div class="field" data-for="choice listen"><label>To‘g‘ri variant raqami *</label><input type="number" name="correct_index" min="1" max="10" value="<?= (int) ($q['correct_index'] ?? 0) + 1 ?>"><div class="hint">1 — birinchi qator, 2 — ikkinchi…</div></div>
    <div class="field" data-for="build fill"><label>To‘g‘ri javob (o‘zbekcha) *</label><input type="text" name="answer_text" maxlength="300" value="<?= e($q['answer_text'] ?? '') ?>" placeholder="Mening ismim Ali"><div class="hint">Katta-kichik harf, tinish belgisi va o‘/oʻ farqi hisobga olinmaydi.</div></div>
    <div class="field full"><label>Izoh (natijada ko‘rsatiladi)</label><input type="text" name="explanation" maxlength="600" value="<?= e($q['explanation'] ?? '') ?>"></div>
</div>
