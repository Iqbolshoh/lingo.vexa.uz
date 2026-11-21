<div class="adm-form">
    <div class="field"><label>Ism *</label><input type="text" name="display_name" required maxlength="80" value="<?= e($s['display_name']) ?>"></div>
    <div class="field"><label>Email *</label><input type="email" name="email" required maxlength="160" value="<?= e($s['email']) ?>"></div>
    <div class="field"><label>Yosh</label><select name="age_band"><?php foreach (AGE_LABELS as $k => $l): ?><option value="<?= $k ?>" <?= $s['age_band'] === $k ? 'selected' : '' ?>><?= $l ?></option><?php endforeach; ?></select></div>
    <div class="field"><label>Til darajasi</label><select name="language_level"><?php foreach (LEVELS as $l): ?><option <?= $s['language_level'] === $l ? 'selected' : '' ?>><?= $l ?></option><?php endforeach; ?></select></div>
    <div class="field full"><label>Vaqt zonasi</label><select name="timezone"><?php foreach (TIMEZONES as $k => $l): ?><option value="<?= $k ?>" <?= normalize_timezone($s['timezone']) === $k ? 'selected' : '' ?>><?= $l ?></option><?php endforeach; ?></select></div>
</div>
