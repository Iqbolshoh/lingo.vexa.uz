<div class="pg onb">
    <div class="container pg-narrow">
        <form method="POST" action="/onboarding/placement" id="quizStepper" class="ex-shell">
            <?= csrf_field() ?>
            <div class="ex-top"><?= mascot('thinking', 40, 'owl--mini', ['data-owl' => 'quiz']) ?><div class="bar"><i style="width:0%"></i></div><span class="ex-count">1 / <?= count($questions) ?></span></div>
            <div class="ex-body">
                <?php foreach ($questions as $i => $q): ?>
                    <div class="q-step <?= $i === 0 ? 'is-active' : '' ?>" data-type="choice">
                        <span class="ex-kind"><?= icon('target', '', 13) ?> <?= t('Daraja testi · bilmasangiz, taxmin qilmang') ?></span>
                        <h3 class="ex-prompt"><?= t('«:word» — nima degani?', ['word' => $q['word']]) ?></h3>
                        <?php if (!is_en()): ?><p class="ex-sub">What does it mean?</p><?php endif; ?>
                        <div class="ex-options">
                            <?php foreach ($q['options'] as $oi => $opt): ?>
                                <label class="ex-opt"><input type="radio" name="p_<?= $i ?>" value="<?= $oi ?>"><span class="key"><?= chr(65 + $oi) ?></span><span><?= e($opt) ?></span></label>
                            <?php endforeach; ?>
                            <label class="ex-opt"><input type="radio" name="p_<?= $i ?>" value="-1"><span class="key">?</span><span><?= t('Bilmayman') ?></span></label>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="ex-foot">
                <button type="button" class="btn btn-ghost" data-act="prev"><?= icon('arrow-left', '', 16) ?> <?= t('Orqaga') ?></button>
                <button type="button" class="btn btn-primary" data-act="next"><?= t('Keyingisi') ?> <?= icon('arrow-right', '', 16) ?></button>
                <button type="submit" class="btn btn-check" data-act="submit" hidden><?= icon('check-circle', '', 18) ?> <?= t('Natijani ko‘rish') ?></button>
            </div>
        </form>
        <p class="small muted" style="text-align:center;margin-top:12px"><?= t('Test baholanmaydi — u faqat qaysi darsdan boshlashingizni aniqlaydi.') ?></p>
    </div>
</div>
<script src="/assets/lesson.js?v=<?= filemtime(__DIR__ . '/../../public/assets/lesson.js') ?>" defer></script>
