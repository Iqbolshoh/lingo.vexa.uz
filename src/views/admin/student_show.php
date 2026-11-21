<div class="container adm">
    <?php require __DIR__ . '/_side.php'; ?>
    <main class="adm-main">
        <div class="adm-head">
            <div>
                <div class="crumbs"><a href="/admin">Admin</a> / <a href="/admin/students">O‘quvchilar</a> / <?= e($student['display_name']) ?></div>
                <h1><?= e($student['display_name']) ?></h1>
                <div class="course-meta" style="margin-top:4px"><span><?= e($student['email']) ?></span><span><?= e($student['age_band']) ?> yosh · <?= e($student['language_level']) ?></span><span><?= e(TIMEZONES[normalize_timezone($student['timezone'])]) ?></span></div>
            </div>
        </div>

        <div class="stat-grid mt-0">
            <div class="stat-tile tone-gold"><span class="stat-ico"><?= icon('star', '', 22) ?></span><div style="flex:1"><strong><?= (int) $student['xp'] ?> XP</strong><span><?= $level['level'] ?>-daraja</span><div class="bar bar-gold"><i style="width: <?= $level['percent'] ?>%"></i></div></div></div>
            <div class="stat-tile tone-ruby"><span class="stat-ico"><?= icon('flame', '', 22) ?></span><div><strong><?= $streak ?> kun</strong><span>ketma-ket faollik</span></div></div>
            <div class="stat-tile tone-lapis"><span class="stat-ico"><?= icon('coins', '', 22) ?></span><div><strong><?= (int) $student['coins'] ?></strong><span>tanga</span></div></div>
            <div class="stat-tile tone-emerald"><span class="stat-ico"><?= icon('award', '', 22) ?></span><div><strong><?= count($badges) ?></strong><span>nishon</span></div></div>
        </div>

        <?php
            $sm = decode_list($student['motives_json'], ONB_MOTIVES);
            $sp = decode_list($student['places_json'], ONB_PLACES);
            $si = decode_list($student['interests_json'], ONB_INTERESTS);
        ?>
        <div class="card tone-gold">
            <h3 class="card-title"><span class="icon-wrap"><?= icon('heart', '', 20) ?></span> Tanishuv javoblari</h3>
            <?php if (!$student['motives_json']): ?>
                <p class="muted small">O‘quvchi hali tanishuv savollariga javob bermagan.</p>
            <?php else: ?>
                <div class="grid-2">
                    <div><div class="small muted" style="font-weight:700">Nega o‘rganyapti</div><div class="chip-cloud"><?php foreach ($sm as $k): ?><span class="tag tag-lapis"><?= ONB_MOTIVES[$k][0] ?></span><?php endforeach; ?><?= $sm ? '' : '—' ?></div></div>
                    <div><div class="small muted" style="font-weight:700">Qayerga bormoqchi</div><div class="chip-cloud"><?php foreach ($sp as $k): ?><span class="tag tag-emerald"><?= ONB_PLACES[$k][0] ?></span><?php endforeach; ?><?= $sp ? '' : '—' ?></div></div>
                    <div><div class="small muted" style="font-weight:700">Qiziqishlar</div><div class="chip-cloud"><?php foreach ($si as $k): ?><span class="tag tag-gold"><?= ONB_INTERESTS[$k][0] ?></span><?php endforeach; ?><?= $si ? '' : '—' ?></div></div>
                    <div><div class="small muted" style="font-weight:700">Daraja</div>
                        <div class="chip-cloud">
                            <span class="tag tag-ruby"><?= e(ONB_LEVELS[$student['self_level']][0] ?? '—') ?></span>
                            <?php if ($student['placement_score'] !== null): ?><span class="tag tag-lapis">Daraja testi: <?= (int) $student['placement_score'] ?>%</span><?php endif; ?>
                            <span class="tag tag-emerald">Kunlik maqsad: <?= e(ONB_GOALS[(int) $student['daily_goal_xp']][0] ?? '—') ?></span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php foreach ($courseStates as $cs): ?>
            <h2 class="section-h"><?= icon($cs['course']['icon'], '', 22) ?> <?= e($cs['course']['title']) ?> <small>O‘zlashtirish <?= $cs['mastery'] ?>% · <?= $cs['completed'] ?>/<?= $cs['total'] ?> dars</small></h2>
            <div class="tbl-wrap">
                <table class="tbl">
                    <thead><tr><th class="col-index">#</th><th>Dars</th><th>Holat</th><th>Eng yaxshi ball</th><th style="text-align:right">Amal</th></tr></thead>
                    <tbody>
                    <?php foreach ($cs['lessons'] as $i => $l): [$label, $tone] = mastery_label((int) $l['best_score']); ?>
                        <tr>
                            <td class="col-index"><?= $i + 1 ?></td>
                            <td class="t-title"><?= e($l['title']) ?></td>
                            <td><span class="status-pill st-<?= e($l['user_status']) ?>"><?= ['completed' => 'O‘zlashtirildi', 'available' => 'Ochiq', 'locked' => 'Qulflangan'][$l['user_status']] ?></span></td>
                            <td><span class="heat heat-<?= $tone ?>"><?= $l['best_score'] ? (int) $l['best_score'] . '%' : '—' ?></span> <span class="t-sub"><?= $label ?></span></td>
                            <td style="text-align:right">
                                <?php if ($l['user_status'] === 'locked'): ?>
                                    <form method="POST" action="/admin/unlock" class="inline-form"><?= csrf_field() ?>
                                        <input type="hidden" name="user_id" value="<?= (int) $student['id'] ?>"><input type="hidden" name="lesson_id" value="<?= (int) $l['id'] ?>">
                                        <button class="btn btn-ghost btn-xs"><?= icon('unlock', '', 13) ?> Qo‘lda ochish</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>

        <div class="grid-2" style="margin-top:22px;align-items:start">
            <div>
                <h2 class="section-h mt-0"><?= icon('check-circle', '', 22) ?> Test tarixi</h2>
                <div class="tbl-wrap">
                    <table class="tbl">
                        <thead><tr><th class="col-index">#</th><th>Dars</th><th>Natija</th><th>Sana</th></tr></thead>
                        <tbody>
                        <?php if (!$attempts): ?><tr><td colspan="4" class="empty">Hali test topshirmagan.</td></tr><?php endif; ?>
                        <?php foreach ($attempts as $i => $a): ?>
                            <tr><td class="col-index"><?= $i + 1 ?></td><td><?= e($a['lesson_title']) ?></td><td><span class="heat heat-<?= mastery_label((int) $a['percent'])[1] ?>"><?= (int) $a['percent'] ?>%</span></td><td class="t-sub"><?= e(substr((string) $a['created_at'], 0, 16)) ?></td></tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="stack-sm">
                <h2 class="section-h mt-0"><?= icon('settings', '', 22) ?> Boshqaruv</h2>
                <form method="POST" action="/admin/students/<?= (int) $student['id'] ?>/update" class="card">
                    <?= csrf_field() ?>
                    <?php $s = $student; require __DIR__ . '/_student_fields.php'; ?>
                    <div class="form-foot"><button class="btn btn-primary btn-sm">Ma’lumotlarni saqlash</button></div>
                </form>
                <form method="POST" action="/admin/students/<?= (int) $student['id'] ?>/password" class="card field">
                    <?= csrf_field() ?>
                    <label>Yangi parol o‘rnatish</label>
                    <div class="search-bar"><input type="text" name="password" minlength="6" required placeholder="Kamida 6 belgi" autocomplete="off"><button class="btn btn-primary btn-sm">Saqlash</button></div>
                </form>
                <form method="POST" action="/admin/students/<?= (int) $student['id'] ?>/reset-progress" class="card" onsubmit="return confirm('Barcha natijalar, XP va nishonlar o‘chiriladi. Davom etasizmi?')">
                    <?= csrf_field() ?>
                    <div class="row-between"><div><strong>Natijalarni nolga tushirish</strong><div class="small muted">Akkaunt qoladi, darslar boshidan boshlanadi.</div></div><button class="btn btn-ghost btn-sm"><?= icon('rotate-cw', '', 14) ?> Nolga</button></div>
                </form>
                <form method="POST" action="/admin/students/<?= (int) $student['id'] ?>/delete" class="card" style="border-color:#fecdd3" onsubmit="return confirm('O‘quvchi va uning barcha natijalari o‘chiriladi. Davom etasizmi?')">
                    <?= csrf_field() ?>
                    <div class="row-between"><div><strong style="color:#be123c">O‘quvchini o‘chirish</strong><div class="small muted">Natijalar ham o‘chadi.</div></div><button class="btn btn-danger btn-sm">O‘chirish</button></div>
                </form>
            </div>
        </div>
    </main>
</div>
