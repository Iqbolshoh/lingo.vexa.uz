<?php
$bars = function (array $daily): string {
    $max = max(1, max($daily));
    $html = '<div class="spark">';
    foreach ($daily as $d => $v) {
        $html .= '<div style="height:' . max(3, (int) round($v / $max * 100)) . '%" data-v="' . $v . '" title="' . e($d) . ': ' . $v . '"></div>';
    }
    $html .= '</div><div class="spark-labels">';
    foreach (array_keys($daily) as $i => $d) {
        $html .= '<span>' . ($i % 3 === 0 ? (int) substr($d, 8) : '') . '</span>';
    }
    return $html . '</div>';
};
$pct = fn (int $a, int $b) => $b > 0 ? (int) round($a / $b * 100) : 0;
?>
<div class="container adm">
    <?php require __DIR__ . '/_side.php'; ?>
    <main class="adm-main">
        <div class="adm-head">
            <div><div class="crumbs"><a href="/admin">Admin</a> / Statistika</div><h1>Statistika va tahlil</h1></div>
            <a href="/admin/export/students" class="btn btn-ghost btn-sm"><?= icon('download', '', 14) ?> Öquvçilar CSV</a>
        </div>

        <div class="stat-grid mt-0">
            <div class="stat-tile tone-lapis"><span class="stat-ico"><?= icon('users', '', 22) ?></span><div><strong><?= $kpi['students'] ?></strong><span>öquvçi · +<?= $kpi['new7'] ?> (7 kun) · +<?= $kpi['new30'] ?> (30 kun)</span></div></div>
            <div class="stat-tile tone-emerald"><span class="stat-ico"><?= icon('flame', '', 22) ?></span><div><strong><?= $kpi['dau'] ?> / <?= $kpi['wau'] ?> / <?= $kpi['mau'] ?></strong><span>faol: bugun / 7 kun / 30 kun</span></div></div>
            <div class="stat-tile tone-gold"><span class="stat-ico"><?= icon('rotate-cw', '', 22) ?></span><div><strong><?= $pct($kpi['returning'], $kpi['students']) ?>%</strong><span>qaytib kelganlar (2+ kun faol)</span></div></div>
            <div class="stat-tile tone-ruby"><span class="stat-ico"><?= icon('heart', '', 22) ?></span><div><strong><?= $pct($kpi['onboarded'], $kpi['students']) ?>%</strong><span>tanişuvni to‘ldirgan · test örtaça <?= $kpi['placement'] ?>%</span></div></div>
            <div class="stat-tile tone-lapis"><span class="stat-ico"><?= icon('check-circle', '', 22) ?></span><div><strong><?= $kpi['attempts'] ?></strong><span>test urinişi · ötiş <?= $kpi['pass_rate'] ?>%</span></div></div>
            <div class="stat-tile tone-emerald"><span class="stat-ico"><?= icon('book-open', '', 22) ?></span><div><strong><?= $kpi['completed'] ?></strong><span>özlaştirilgan dars (jami)</span></div></div>
            <div class="stat-tile tone-gold"><span class="stat-ico"><?= icon('star', '', 22) ?></span><div><strong><?= $kpi['avg_xp'] ?> XP</strong><span>öquvçiga örtaça</span></div></div>
            <div class="stat-tile tone-ruby"><span class="stat-ico"><?= icon('target', '', 22) ?></span><div><strong><?= $kpi['students'] ? round($kpi['completed'] / $kpi['students'], 1) : 0 ?></strong><span>öquvçiga örtaça dars</span></div></div>
        </div>

        <div class="grid-2">
            <div class="card"><h3 class="card-title"><?= icon('users', '', 20) ?> Ro‘yxatdan ötişlar (30 kun)</h3><?= $bars($signups) ?></div>
            <div class="card"><h3 class="card-title"><?= icon('flame', '', 20) ?> Kunlik faol öquvçilar (30 kun)</h3><?= $bars($active) ?></div>
        </div>

        <h2 class="section-h"><?= icon('heart', '', 22) ?> Öquvçilarni tanish <small>Tanişuv (onboarding) javoblari</small></h2>
        <div class="grid-2">
            <div class="card"><h3 class="card-title"><?= icon('target', '', 20) ?> Nega örganyapti?</h3><?php $rows = $motives; $tone = ''; require __DIR__ . '/_hbars.php'; ?></div>
            <div class="card"><h3 class="card-title"><?= icon('map-pin', '', 20) ?> Qayerga bormoqçi?</h3><?php $rows = $places; $tone = 'h-emerald'; require __DIR__ . '/_hbars.php'; ?></div>
            <div class="card"><h3 class="card-title"><?= icon('palette', '', 20) ?> Qiziqişlar</h3><?php $rows = $interests; $tone = 'h-gold'; require __DIR__ . '/_hbars.php'; ?></div>
            <div class="card"><h3 class="card-title"><?= icon('layers', '', 20) ?> Öz bahosi böyiça daraja</h3><?php $rows = $selfLevels; $tone = 'h-ruby'; require __DIR__ . '/_hbars.php'; ?>
                <h3 class="card-title" style="margin-top:18px"><?= icon('flame', '', 20) ?> Kunlik maqsad</h3><?php $rows = $goals; $tone = 'h-emerald'; require __DIR__ . '/_hbars.php'; ?></div>
        </div>

        <h2 class="section-h"><?= icon('users', '', 22) ?> Auditoriya</h2>
        <div class="grid-3">
            <div class="card"><h3 class="card-title"><?= icon('user', '', 20) ?> Yoş</h3><?php $rows = $ages; $tone = ''; require __DIR__ . '/_hbars.php'; ?></div>
            <div class="card"><h3 class="card-title"><?= icon('target', '', 20) ?> Til darajasi</h3><?php $rows = $levels; $tone = 'h-gold'; require __DIR__ . '/_hbars.php'; ?></div>
            <div class="card"><h3 class="card-title"><?= icon('map-pin', '', 20) ?> Vaqt zonasi</h3><?php $rows = $zones; $tone = 'h-emerald'; require __DIR__ . '/_hbars.php'; ?></div>
        </div>

        <h2 class="section-h"><?= icon('layers', '', 22) ?> Darslar voronkasi <small>Har bir darsni boşlagan va özlaştirganlar</small></h2>
        <div class="grid-3">
            <?php foreach ($funnels as $f): $top = max(1, (int) ($f['rows'][0]['started'] ?? 0)); ?>
                <div class="card">
                    <h3 class="card-title"><?= icon($f['course']['icon'], '', 20) ?> <?= e($f['course']['title']) ?></h3>
                    <div class="hbars">
                        <?php foreach ($f['rows'] as $r): ?>
                            <div class="hbar" title="<?= e($r['title']) ?>">
                                <span class="hbar-label"><?= (int) $r['position'] ?>. <?= e(mb_strimwidth($r['title'], 0, 22, '…')) ?></span>
                                <span class="hbar-track"><i class="h-emerald" style="width: <?= min(100, (int) round($r['done'] / $top * 100)) ?>%"></i></span>
                                <b><?= (int) $r['done'] ?></b><small>/<?= (int) $r['started'] ?></small>
                            </div>
                        <?php endforeach; ?>
                        <?php if (!$f['rows']): ?><div class="empty small">Darslar yöq.</div><?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="grid-2" style="margin-top:18px">
            <div class="card">
                <h3 class="card-title"><?= icon('check-circle', '', 20) ?> Maşq turlari böyiça aniqlik</h3>
                <?php $rows = []; foreach ($types as $t => $v) { $rows[] = ['label' => QUESTION_TYPES[$t] . ' (' . $v['all'] . ')', 'value' => $v['all'] ? (int) round($v['ok'] / $v['all'] * 100) : 0]; } ?>
                <div class="hbars">
                    <?php foreach ($rows as $r): ?>
                        <div class="hbar"><span class="hbar-label"><?= e($r['label']) ?></span><span class="hbar-track"><i class="<?= $r['value'] >= 80 ? 'h-emerald' : ($r['value'] >= 50 ? 'h-gold' : 'h-ruby') ?>" style="width: <?= $r['value'] ?>%"></i></span><b><?= $r['value'] ?>%</b><small></small></div>
                    <?php endforeach; ?>
                </div>
                <p class="small muted" style="margin-top:8px">Past foiz — o‘sha turdagi savollar qiyin yoki tuşunarsiz: matnini qayta ko‘rib chiqing.</p>
            </div>
            <div class="card">
                <h3 class="card-title"><?= icon('clock', '', 20) ?> Qaysi soatda öqişadi? (Toshkent vaqti)</h3>
                <?php $hmax = max(1, max($hours)); ?>
                <div class="spark"><?php foreach ($hours as $h => $v): ?><div style="height: <?= max(3, (int) round($v / $hmax * 100)) ?>%" data-v="<?= $v ?>" title="<?= sprintf('%02d:00', $h) ?> — <?= $v ?> ta test"></div><?php endforeach; ?></div>
                <div class="spark-labels"><?php foreach ($hours as $h => $v): ?><span><?= $h % 3 === 0 ? $h : '' ?></span><?php endforeach; ?></div>
                <p class="small muted" style="margin-top:8px">Jonli darslar va eslatmalar uchun eng qulay vaqtni tanlashga yordam beradi.</p>
            </div>
        </div>

        <div class="grid-2" style="margin-top:18px;align-items:start">
            <div>
                <h2 class="section-h mt-0"><?= icon('trophy', '', 22) ?> Reyting (XP)</h2>
                <div class="tbl-wrap">
                    <table class="tbl">
                        <thead><tr><th class="col-index">#</th><th>Öquvçi</th><th>XP</th><th>Daraja</th><th>Ketma-ket</th><th>Tanga</th></tr></thead>
                        <tbody>
                        <?php if (!$leaders): ?><tr><td colspan="6" class="empty">Öquvçi yöq.</td></tr><?php endif; ?>
                        <?php foreach ($leaders as $i => $u): ?>
                            <tr>
                                <td class="col-index"><?= $i + 1 ?></td>
                                <td><a class="t-title" href="/admin/students/<?= (int) $u['id'] ?>"><?= e($u['display_name']) ?></a></td>
                                <td><strong><?= (int) $u['xp'] ?></strong></td>
                                <td><?= xp_level((int) $u['xp'])['level'] ?></td>
                                <td><?= current_streak($u) ?> kun</td>
                                <td><?= (int) $u['coins'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div>
                <h2 class="section-h mt-0"><?= icon('award', '', 22) ?> Nişonlar</h2>
                <div class="card">
                    <div class="hbars">
                        <?php $bmax = max(1, max(array_column($badgeStats, 'n') ?: [0])); foreach ($badgeStats as $b): ?>
                            <div class="hbar"><span class="hbar-label"><?= icon($b['icon'], '', 14) ?> <?= e($b['title']) ?></span><span class="hbar-track"><i class="h-gold" style="width: <?= (int) round($b['n'] / $bmax * 100) ?>%"></i></span><b><?= (int) $b['n'] ?></b><small></small></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
