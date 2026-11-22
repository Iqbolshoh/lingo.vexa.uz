<?php
$top3 = array_slice($leaders, 0, 3);
$rest = array_slice($leaders, 3);
?>
<div class="pg leaderboard-pg">
    <div class="container">
        <section class="pg-hero theme-gold">
            <div class="hero-split">
                <div>
                    <div class="crumbs">
                        <a href="/"><?= t('Bosh sahifa') ?></a> <span>/</span>
                        <?php if ($currentUser): ?><a href="/dashboard"><?= t('Mening sahifam') ?></a> <span>/</span><?php endif; ?>
                        <span><?= t('Reyting va Liga') ?></span>
                    </div>
                    <h1><?= icon('trophy', '', 32) ?> <?= t('O‘quvchilar Reytingi va Haftalik Liga') ?></h1>
                    <p><?= t('Darslarni bajaring, yangi so‘zlar o‘rganing va XP to‘plab yetakchilar safidan joy oling!') ?></p>
                    <?php if ($currentUser && $myRank !== null): ?>
                        <div class="xp-strip" style="margin-top:14px">
                            <span class="xp-chip" style="background:#fef3c7;color:#92400e;border-color:#fcd34d">
                                <?= icon('award', '', 18) ?>
                                <strong><?= t('Sizning o‘rningiz:') ?></strong> <?= t(':rank-o‘rin', ['rank' => $myRank]) ?>
                                <small>(<?= (int) $currentUser['xp'] ?> XP)</small>
                            </span>
                            <span class="xp-chip">
                                <?= icon('flame', '', 18) ?> <?= t(':n kunlik streak', ['n' => (int) $currentUser['streak_days']]) ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="hero-owl-ring">
                    <?= mascot('celebrate', 115) ?>
                </div>
            </div>
        </section>

        <!-- Top 3 Podium -->
        <?php if (!empty($top3)): ?>
            <div class="podium-section">
                <h2 class="podium-title"><?= icon('trophy', '', 20) ?> <?= t('Haftalik Shohsupa (Top 3)') ?></h2>
                <div class="podium-grid">
                    <!-- 2nd Place (Silver) -->
                    <?php if (isset($top3[1])): $s = $top3[1]; $isMe = $currentUser && (int)$currentUser['id'] === (int)$s['id']; ?>
                        <div class="podium-card podium-silver <?= $isMe ? 'is-me' : '' ?>">
                            <div class="podium-badge medal-silver">🥈 #2</div>
                            <div class="podium-avatar">
                                <?= icon('user', '', 36) ?>
                            </div>
                            <h3 class="podium-name"><?= e($s['display_name']) ?> <?= $isMe ? '<span class="you-tag">' . t('Siz') . '</span>' : '' ?></h3>
                            <div class="podium-stats">
                                <span class="pill-xp"><?= icon('star', '', 14) ?> <?= (int)$s['xp'] ?> XP</span>
                                <span class="pill-streak"><?= icon('flame', '', 14) ?> <?= (int)$s['streak_days'] ?> <?= t('kun') ?></span>
                            </div>
                            <div class="podium-pedestal step-2">2</div>
                        </div>
                    <?php endif; ?>

                    <!-- 1st Place (Gold Champion) -->
                    <?php if (isset($top3[0])): $g = $top3[0]; $isMe = $currentUser && (int)$currentUser['id'] === (int)$g['id']; ?>
                        <div class="podium-card podium-gold <?= $isMe ? 'is-me' : '' ?>">
                            <div class="podium-crown">👑</div>
                            <div class="podium-badge medal-gold">🥇 #1</div>
                            <div class="podium-avatar avatar-gold">
                                <?= icon('trophy', '', 40) ?>
                            </div>
                            <h3 class="podium-name"><?= e($g['display_name']) ?> <?= $isMe ? '<span class="you-tag">' . t('Siz') . '</span>' : '' ?></h3>
                            <div class="podium-stats">
                                <span class="pill-xp gold-xp"><?= icon('star', '', 14) ?> <?= (int)$g['xp'] ?> XP</span>
                                <span class="pill-streak"><?= icon('flame', '', 14) ?> <?= (int)$g['streak_days'] ?> <?= t('kun') ?></span>
                            </div>
                            <div class="podium-pedestal step-1">1</div>
                        </div>
                    <?php endif; ?>

                    <!-- 3rd Place (Bronze) -->
                    <?php if (isset($top3[2])): $b = $top3[2]; $isMe = $currentUser && (int)$currentUser['id'] === (int)$b['id']; ?>
                        <div class="podium-card podium-bronze <?= $isMe ? 'is-me' : '' ?>">
                            <div class="podium-badge medal-bronze">🥉 #3</div>
                            <div class="podium-avatar">
                                <?= icon('user', '', 36) ?>
                            </div>
                            <h3 class="podium-name"><?= e($b['display_name']) ?> <?= $isMe ? '<span class="you-tag">' . t('Siz') . '</span>' : '' ?></h3>
                            <div class="podium-stats">
                                <span class="pill-xp"><?= icon('star', '', 14) ?> <?= (int)$b['xp'] ?> XP</span>
                                <span class="pill-streak"><?= icon('flame', '', 14) ?> <?= (int)$b['streak_days'] ?> <?= t('kun') ?></span>
                            </div>
                            <div class="podium-pedestal step-3">3</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Ranking Table -->
        <div class="leaderboard-table-card">
            <div class="table-header-row">
                <h2><?= t('To‘liq reyting jadvali') ?></h2>
                <span class="table-meta"><?= t('Jami :n nafar o‘quvchi', ['n' => count($leaders)]) ?></span>
            </div>

            <div class="table-responsive">
                <table class="leaderboard-table">
                    <thead>
                        <tr>
                            <th style="width:70px"><?= t('O‘rin') ?></th>
                            <th><?= t('O‘quvchi') ?></th>
                            <th><?= t('Darajasi') ?></th>
                            <th><?= t('Ketma-ketlik') ?></th>
                            <th><?= t('Tugatilgan darslar') ?></th>
                            <th style="text-align:right"><?= t('Jami XP') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($leaders as $idx => $l): 
                            $rank = $idx + 1;
                            $isMe = $currentUser && (int)$currentUser['id'] === (int)$l['id'];
                        ?>
                            <tr class="<?= $isMe ? 'my-rank-row' : '' ?>">
                                <td>
                                    <?php if ($rank === 1): ?><span class="rank-badge rank-1">🥇 1</span>
                                    <?php elseif ($rank === 2): ?><span class="rank-badge rank-2">🥈 2</span>
                                    <?php elseif ($rank === 3): ?><span class="rank-badge rank-3">🥉 3</span>
                                    <?php else: ?><span class="rank-badge">#<?= $rank ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="user-cell">
                                        <span class="user-avatar-mini"><?= icon('user', '', 15) ?></span>
                                        <strong><?= e($l['display_name']) ?></strong>
                                        <?php if ($isMe): ?><span class="you-pill"><?= t('Siz') ?></span><?php endif; ?>
                                    </div>
                                </td>
                                <td><span class="level-badge"><?= e($l['language_level'] ?: 'A0') ?></span></td>
                                <td>
                                    <?php if ((int)$l['streak_days'] > 0): ?>
                                        <span class="streak-pill"><?= icon('flame', '', 14) ?> <?= (int)$l['streak_days'] ?> <?= t('kun') ?></span>
                                    <?php else: ?>
                                        <span class="muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="lessons-pill"><?= icon('check-circle', '', 14) ?> <?= (int)$l['completed_lessons'] ?> <?= t('dars') ?></span>
                                </td>
                                <td style="text-align:right">
                                    <span class="xp-bold"><?= icon('star', '', 15) ?> <?= (int)$l['xp'] ?> XP</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="leaderboard-bottom-tip">
                <div class="tip-owl"><?= mascot('thinking', 64) ?></div>
                <div class="tip-content">
                    <h4><?= t('Qanday qilib reytingda yuqoriga ko‘tarilish mumkin?') ?></h4>
                    <p><?= t('Har bir darsni tugatish, testlardan 80%+ olish, Flashcards orqali so‘z takrorlash va har kuni kirib streak saqlash sizga qo‘shimcha XP beradi!') ?></p>
                </div>
                <?php if ($currentUser): ?>
                    <a href="/courses" class="btn btn-primary btn-sm"><?= icon('play', '', 16) ?> <?= t('Dars boshlash') ?></a>
                <?php else: ?>
                    <a href="/register" class="btn btn-primary btn-sm"><?= icon('arrow-right', '', 16) ?> <?= t('Ro‘yxatdan o‘tish') ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if ($currentUser && $myRank !== null && $myRank <= 3): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.SoundFX !== 'undefined') window.SoundFX.match();
    if (typeof window.triggerConfetti === 'function') window.triggerConfetti({ particleCount: 60 });
});
</script>
<?php endif; ?>
