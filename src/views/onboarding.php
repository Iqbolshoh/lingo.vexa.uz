<?php
$selfLevel = $user['self_level'] ?: 'none';
$goal = (int) ($user['daily_goal_xp'] ?: 50);
$steps = [
    [t('Nima uchun o‘zbek tilini o‘rganmoqchisiz?'), t('Bir nechtasini tanlashingiz mumkin. Shunga qarab darslarni moslayman!'), 'welcome'],
    [t('Qayerlarga borishni orzu qilasiz?'), t('O‘zbekistonda har bir shahar — alohida ertak.'), 'thinking'],
    [t('Nimalarga qiziqasiz?'), t('Qiziqishlaringizga mos so‘z va hikoyalar tanlayman.'), 'learning'],
    [t('O‘zbek tilini qay darajada bilasiz?'), t('Rostini ayting — kerak bo‘lsa, qisqa test bilan tekshiramiz.'), 'thinking'],
    [t('Kuniga qancha vaqt ajratasiz?'), t('Har kuni oz-ozdan — eng to‘g‘ri yo‘l. Keyin o‘zgartirsa bo‘ladi.'), 'encourage'],
];
?>
<div class="pg onb">
    <div class="container pg-narrow">
        <form method="POST" action="/onboarding" id="onbForm" class="onb-card">
            <?= csrf_field() ?>
            <div class="onb-top">
                <div class="bar bar-emerald"><i style="width:0%"></i></div>
                <span class="ex-count">1 / <?= count($steps) ?></span>
            </div>

            <?php foreach ($steps as $i => [$h, $sub, $owlState]): ?>
                <section class="q-step onb-step <?= $i === 0 ? 'is-active' : '' ?>" data-owl-state="<?= $owlState ?>">
                    <div class="onb-head">
                        <?= mascot($owlState, 96, 'onb-owl') ?>
                        <div class="onb-bubble"><h1><?= $h ?></h1><p><?= $sub ?></p></div>
                    </div>

                    <?php if ($i === 0): ?>
                        <div class="pick-grid">
                            <?php foreach (ONB_MOTIVES as $k => [$label, $desc, $ico]): ?>
                                <label class="pick"><input type="checkbox" name="motives[]" value="<?= $k ?>" <?= in_array($k, $motives, true) ? 'checked' : '' ?>>
                                    <span class="pick-ico"><?= icon($ico, '', 22) ?></span><span><strong><?= t($label) ?></strong><small><?= t($desc) ?></small></span></label>
                            <?php endforeach; ?>
                        </div>
                    <?php elseif ($i === 1): ?>
                        <div class="pick-grid">
                            <?php foreach (ONB_PLACES as $k => [$label, $desc]): ?>
                                <label class="pick"><input type="checkbox" name="places[]" value="<?= $k ?>" <?= in_array($k, $places, true) ? 'checked' : '' ?>>
                                    <span class="pick-ico"><?= icon($k === 'notsure' ? 'sparkles' : 'map-pin', '', 22) ?></span><span><strong><?= t($label) ?></strong><small><?= t($desc) ?></small></span></label>
                            <?php endforeach; ?>
                        </div>
                    <?php elseif ($i === 2): ?>
                        <div class="pick-grid">
                            <?php foreach (ONB_INTERESTS as $k => [$label, $desc, $ico]): ?>
                                <label class="pick"><input type="checkbox" name="interests[]" value="<?= $k ?>" <?= in_array($k, $interests, true) ? 'checked' : '' ?>>
                                    <span class="pick-ico"><?= icon($ico, '', 22) ?></span><span><strong><?= t($label) ?></strong><small><?= t($desc) ?></small></span></label>
                            <?php endforeach; ?>
                        </div>
                    <?php elseif ($i === 3): ?>
                        <div class="pick-list">
                            <?php $bars = ['none' => 1, 'words' => 2, 'basic' => 3, 'good' => 4]; foreach (ONB_LEVELS as $k => [$label, $desc, $cefr]): ?>
                                <label class="pick pick-row"><input type="radio" name="self_level" value="<?= $k ?>" <?= $selfLevel === $k ? 'checked' : '' ?>>
                                    <span class="lvl-bars"><?php for ($b = 1; $b <= 4; $b++): ?><i class="<?= $b <= $bars[$k] ? 'on' : '' ?>"></i><?php endfor; ?></span>
                                    <span><strong><?= t($label) ?></strong><small><?= t($desc) ?></small></span><span class="tag tag-lapis" style="margin-left:auto"><?= $cefr ?></span></label>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="pick-list">
                            <?php foreach (ONB_GOALS as $xp => [$time, $label, $xpLabel]): ?>
                                <label class="pick pick-row"><input type="radio" name="daily_goal" value="<?= $xp ?>" <?= $goal === $xp ? 'checked' : '' ?>>
                                    <span class="pick-ico"><?= icon('flame', '', 22) ?></span>
                                    <span><strong><?= t(':time kuniga', ['time' => t($time)]) ?></strong><small><?= t($label) ?></small></span><span class="tag tag-gold" style="margin-left:auto"><?= $xpLabel ?></span></label>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>
            <?php endforeach; ?>

            <div class="ex-foot">
                <button type="button" class="btn btn-ghost" data-act="prev"><?= icon('arrow-left', '', 16) ?> <?= t('Orqaga') ?></button>
                <button type="submit" form="skipForm" class="btn-link-muted"><?= t('Keyinroq') ?></button>
                <button type="button" class="btn btn-check" data-act="next"><?= t('Davom etish') ?> <?= icon('arrow-right', '', 16) ?></button>
                <button type="submit" class="btn btn-check" data-act="submit" hidden><?= icon('check-circle', '', 18) ?> <?= t('Tayyor!') ?></button>
            </div>
        </form>
        <form method="POST" action="/onboarding/skip" id="skipForm" hidden><?= csrf_field() ?></form>
    </div>
</div>
<script>
(function () {
    const form = document.getElementById('onbForm');
    const steps = [...form.querySelectorAll('.onb-step')];
    const fill = form.querySelector('.onb-top .bar > i');
    const count = form.querySelector('.ex-count');
    const prev = form.querySelector('[data-act=prev]');
    const next = form.querySelector('[data-act=next]');
    const submit = form.querySelector('[data-act=submit]');
    let at = 0;
    const answered = (s) => !!s.querySelector('input:checked');
    const show = () => {
        steps.forEach((s, i) => s.classList.toggle('is-active', i === at));
        fill.style.width = Math.round((at + 1) / steps.length * 100) + '%';
        count.textContent = `${at + 1} / ${steps.length}`;
        prev.style.visibility = at === 0 ? 'hidden' : 'visible';
        const last = at === steps.length - 1;
        next.hidden = last;
        submit.hidden = !last;
        next.disabled = !answered(steps[at]);
        submit.disabled = !answered(steps[at]);
    };
    form.addEventListener('change', (e) => {
        const owl = steps[at].querySelector('.owl');
        if (window.Owl && owl) Owl.play(owl, e.target.checked ? 'success' : steps[at].dataset.owlState, 900);
        if (typeof SoundFX !== 'undefined') SoundFX.click();
        show();
    });
    prev.addEventListener('click', () => { if (at > 0) { at--; show(); } });
    next.addEventListener('click', () => { if (at < steps.length - 1) { at++; show(); window.scrollTo({ top: 0, behavior: 'smooth' }); } });
    show();
})();
</script>
