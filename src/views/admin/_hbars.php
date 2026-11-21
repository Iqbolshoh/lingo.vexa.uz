<?php /** @var array $rows [['label'=>, 'value'=>]] @var string $tone */ $max = max(1, max(array_column($rows, 'value') ?: [0])); $sum = max(1, array_sum(array_column($rows, 'value'))); ?>
<div class="hbars">
    <?php if (!array_filter(array_column($rows, 'value'))): ?><div class="empty small">Hali ma’lumot yöq.</div><?php endif; ?>
    <?php foreach ($rows as $r): if (!$r['value'] && count($rows) > 8) { continue; } ?>
        <div class="hbar">
            <span class="hbar-label"><?= e((string) $r['label']) ?></span>
            <span class="hbar-track"><i class="<?= $tone ?? '' ?>" style="width: <?= (int) round($r['value'] / $max * 100) ?>%"></i></span>
            <b><?= (int) $r['value'] ?></b><small><?= (int) round($r['value'] / $sum * 100) ?>%</small>
        </div>
    <?php endforeach; ?>
</div>
