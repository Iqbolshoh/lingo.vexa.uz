<?php
$tabs = [
    'content' => ['Kontent', 'book-open', null],
    'vocab' => ['Lug‘at', 'message-square', count($vocab)],
    'questions' => ['Test savollari', 'check-circle', count($questions)],
    'results' => ['Natijalar', 'target', count($results)],
];
$base = '/admin/lessons/' . (int) $lesson['id'];
?>
<div class="container adm">
    <?php require __DIR__ . '/_side.php'; ?>
    <main class="adm-main">
        <div class="adm-head">
            <div>
                <div class="crumbs"><a href="/admin">Admin</a> / <a href="/admin/courses">Kurslar</a> / <a href="/admin/courses/<?= (int) $course['id'] ?>"><?= e($course['title']) ?></a> / <?= (int) $lesson['position'] ?>-dars</div>
                <h1><?= e($lesson['title']) ?></h1>
            </div>
            <div class="adm-actions">
                <span class="status-pill st-<?= e($lesson['status']) ?>"><?= $lesson['status'] === 'published' ? 'Oçiq' : 'Qoralama' ?></span>
                <a href="/lessons/<?= (int) $lesson['id'] ?>" target="_blank" class="btn btn-ghost btn-sm"><?= icon('play', '', 14) ?> Darsni köriş</a>
            </div>
        </div>

        <nav class="adm-tabs">
            <?php foreach ($tabs as $key => [$label, $ico, $n]): ?>
                <a href="<?= $base ?>?tab=<?= $key ?>" class="<?= $tab === $key ? 'is-active' : '' ?>"><?= icon($ico, '', 16) ?> <?= $label ?><?php if ($n !== null): ?> <span class="n"><?= $n ?></span><?php endif; ?></a>
            <?php endforeach; ?>
        </nav>

        <?php if ($tab === 'content'): ?>
            <form method="POST" action="<?= $base ?>" class="card">
                <?= csrf_field() ?>
                <?php require __DIR__ . '/_lesson_fields.php'; ?>
                <div class="form-foot">
                    <button type="submit" class="btn btn-primary"><?= icon('check-circle', '', 16) ?> Saqlaş</button>
                </div>
            </form>
            <form method="POST" action="<?= $base ?>/delete" class="card" style="margin-top:18px;border-color:#fecdd3" onsubmit="return confirm('Dars, uning lug‘ati, savollari va öquvçi natijalari öçiriladi. Davom etasizmi?')">
                <?= csrf_field() ?>
                <div class="row-between">
                    <div><strong style="color:#be123c">Darsni öçiriş</strong><div class="small muted">Yashirish uchun holatni «Qoralama» qilish kifoya.</div></div>
                    <button type="submit" class="btn btn-danger btn-sm">Öçiriş</button>
                </div>
            </form>

        <?php elseif ($tab === 'vocab'): ?>
            <div class="tbl-wrap">
                <table class="tbl">
                    <thead><tr><th class="col-index">#</th><th>Özbekça</th><th>Inglizcha</th><th>Talaffuz</th><th>Misol</th><th style="text-align:right">Amallar</th></tr></thead>
                    <tbody>
                    <?php if (!$vocab): ?><tr><td colspan="6" class="empty">Hali söz yöq — pastdagi forma orqali qöşing.</td></tr><?php endif; ?>
                    <?php foreach ($vocab as $i => $v): ?>
                        <tr>
                            <td class="col-index"><?= $i + 1 ?></td>
                            <td class="t-title"><button type="button" class="icon-btn" data-speak="<?= e($v['word_uz']) ?>" style="width:28px;height:28px;margin-right:6px;vertical-align:middle"><?= icon('volume-2', '', 13) ?></button><?= e($v['word_uz']) ?></td>
                            <td><?= e($v['word_en']) ?></td>
                            <td class="t-sub"><?= e($v['pronunciation']) ?></td>
                            <td class="t-sub"><?= e($v['example']) ?></td>
                            <td>
                                <div class="t-actions">
                                    <button type="button" class="icon-btn js-edit-vocab" title="Tahrirlaş"
                                        data-id="<?= (int) $v['id'] ?>" data-uz="<?= e($v['word_uz']) ?>" data-en="<?= e($v['word_en']) ?>"
                                        data-pr="<?= e($v['pronunciation']) ?>" data-ex="<?= e($v['example']) ?>"><?= icon('settings', '', 15) ?></button>
                                    <form method="POST" action="<?= $base ?>/vocab/<?= (int) $v['id'] ?>/delete" class="inline-form" onsubmit="return confirm('Söz öçirilsinmi?')"><?= csrf_field() ?><button class="icon-btn danger" title="Öçiriş">&times;</button></form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <form method="POST" action="<?= $base ?>/vocab" class="card" id="vocabForm" style="margin-top:18px" data-base="<?= $base ?>/vocab">
                <?= csrf_field() ?>
                <h3 class="card-title" id="vocabFormTitle"><?= icon('sparkles', '', 20) ?> Yangi söz qöşiş</h3>
                <div class="adm-form">
                    <div class="field"><label>Özbekça söz *</label><input type="text" name="word_uz" required maxlength="120" placeholder="Osh"></div>
                    <div class="field"><label>Inglizcha tarjima *</label><input type="text" name="word_en" required maxlength="160" placeholder="Plov"></div>
                    <div class="field"><label>Talaffuz (lotin, inglizcha o‘qilishi)</label><input type="text" name="pronunciation" maxlength="120" placeholder="Ohsh"></div>
                    <div class="field"><label>Misol gap</label><input type="text" name="example" maxlength="300" placeholder="Bugun tushlikka osh!"></div>
                </div>
                <div class="form-foot">
                    <button type="button" class="btn btn-ghost" id="vocabCancel" hidden>Bekor qiliş</button>
                    <button type="submit" class="btn btn-primary"><?= icon('check-circle', '', 16) ?> Saqlaş</button>
                </div>
            </form>

        <?php elseif ($tab === 'questions'): ?>
            <?php if (!$questions): ?><div class="card empty" style="margin-bottom:14px"><div class="e-ico"><?= icon('check-circle', '', 26) ?></div>Hali savol yöq. 5–7 ta turli savol qöşiş tavsiya etiladi.</div><?php endif; ?>
            <?php foreach ($questions as $i => $q): ?>
                <details class="q-admin">
                    <summary>
                        <span class="q-no"><?= $i + 1 ?></span>
                        <span class="q-text"><strong><?= e($q['prompt']) ?></strong><span class="t-sub"><?= e(QUESTION_TYPES[$q['type']] ?? $q['type']) ?> · javob: <?= e(in_array($q['type'], ['build', 'fill'], true) ? $q['answer_text'] : ($q['options'][$q['correct_index']] ?? '?')) ?></span></span>
                        <button form="qmove-<?= (int) $q['id'] ?>" name="dir" value="up" class="icon-btn" title="Yuqoriga" <?= $i === 0 ? 'disabled' : '' ?>>&uarr;</button>
                        <button form="qmove-<?= (int) $q['id'] ?>" name="dir" value="down" class="icon-btn" title="Pastga" <?= $i === count($questions) - 1 ? 'disabled' : '' ?>>&darr;</button>
                    </summary>
                    <div class="q-body">
                        <form method="POST" action="<?= $base ?>/questions/<?= (int) $q['id'] ?>">
                            <?= csrf_field() ?>
                            <?php require __DIR__ . '/_question_fields.php'; ?>
                            <div class="form-foot">
                                <button form="qdel-<?= (int) $q['id'] ?>" class="btn btn-danger btn-sm left">Öçiriş</button>
                                <button type="submit" class="btn btn-primary btn-sm"><?= icon('check-circle', '', 14) ?> Saqlaş</button>
                            </div>
                        </form>
                    </div>
                </details>
                <form id="qmove-<?= (int) $q['id'] ?>" method="POST" action="<?= $base ?>/questions/<?= (int) $q['id'] ?>/move" hidden><?= csrf_field() ?></form>
                <form id="qdel-<?= (int) $q['id'] ?>" method="POST" action="<?= $base ?>/questions/<?= (int) $q['id'] ?>/delete" onsubmit="return confirm('Savol öçirilsinmi?')" hidden><?= csrf_field() ?></form>
            <?php endforeach; ?>
            <details class="q-admin" <?= $questions ? '' : 'open' ?> style="border-style:dashed">
                <summary><span class="q-no">+</span><span class="q-text"><strong>Yangi savol qöşiş</strong><span class="t-sub">Variant, tinglash, gap tuzish yoki yozish</span></span></summary>
                <div class="q-body">
                    <form method="POST" action="<?= $base ?>/questions">
                        <?= csrf_field() ?>
                        <?php $q = ['type' => 'choice', 'correct_index' => 0, 'options' => []]; require __DIR__ . '/_question_fields.php'; ?>
                        <div class="form-foot"><button type="submit" class="btn btn-primary"><?= icon('check-circle', '', 16) ?> Savolni qöşiş</button></div>
                    </form>
                </div>
            </details>

        <?php else: ?>
            <div class="grid-2" style="align-items:start">
                <div class="tbl-wrap">
                    <table class="tbl">
                        <thead><tr><th class="col-index">#</th><th>Öquvçi</th><th>Eng yaxshi</th><th>Uriniş</th><th>Holat</th></tr></thead>
                        <tbody>
                        <?php if (!$results): ?><tr><td colspan="5" class="empty">Hali hech kim test topşirmagan.</td></tr><?php endif; ?>
                        <?php foreach ($results as $i => $r): ?>
                            <tr>
                                <td class="col-index"><?= $i + 1 ?></td>
                                <td><a class="t-title" href="/admin/students/<?= (int) $r['id'] ?>"><?= e($r['display_name']) ?></a></td>
                                <td><span class="heat heat-<?= mastery_label((int) $r['best_score'])[1] ?>"><?= (int) $r['best_score'] ?>%</span></td>
                                <td><?= (int) $r['attempts'] ?></td>
                                <td><span class="status-pill st-<?= e($r['status']) ?>"><?= $r['status'] === 'completed' ? 'O‘tdi' : 'Jarayonda' ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card">
                    <h3 class="card-title"><?= icon('target', '', 20) ?> Ko‘p xato qilingan savollar</h3>
                    <?php if (!$hardest): ?><div class="empty">Xatolar statistikasi hali yöq.</div><?php endif; ?>
                    <div class="stack-sm">
                        <?php foreach ($hardest as $h): ?>
                            <div><div class="row-between"><span class="small" style="font-weight:700;flex:1"><?= e($h['prompt']) ?></span><span class="heat heat-low"><?= $h['error_rate'] ?>%</span></div><div class="small muted"><?= $h['wrong'] ?> / <?= $h['total'] ?> javob xato</div></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>
</div>
<script>
document.querySelectorAll('.js-qtype').forEach(sel => sel.addEventListener('change', () => {
    sel.closest('.type-fields').dataset.type = sel.value;
}));
(function () {
    const form = document.getElementById('vocabForm');
    if (!form) return;
    const title = document.getElementById('vocabFormTitle');
    const cancel = document.getElementById('vocabCancel');
    const reset = () => { form.reset(); form.action = form.dataset.base; title.lastChild.textContent = ' Yangi söz qöşiş'; cancel.hidden = true; };
    document.querySelectorAll('.js-edit-vocab').forEach(btn => btn.addEventListener('click', () => {
        form.action = form.dataset.base + '/' + btn.dataset.id;
        form.word_uz.value = btn.dataset.uz; form.word_en.value = btn.dataset.en;
        form.pronunciation.value = btn.dataset.pr; form.example.value = btn.dataset.ex;
        title.lastChild.textContent = ' Sözni tahrirlaş: ' + btn.dataset.uz;
        cancel.hidden = false;
        form.scrollIntoView({ behavior: 'smooth', block: 'center' });
        form.word_uz.focus();
    }));
    cancel.addEventListener('click', reset);
})();
</script>
