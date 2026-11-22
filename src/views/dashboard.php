<?php
$totalDone = array_sum(array_column($courseStates, 'completed'));
$totalLessons = array_sum(array_column($courseStates, 'total'));
$overall = $totalLessons > 0 ? (int) round($totalDone / $totalLessons * 100) : 0;
$earned = array_filter($badges, fn ($b) => $b['earned_at']);
$goalXp = max(1, (int) $user['daily_goal_xp']);
$goalPct = min(100, (int) round($todayXp / $goalXp * 100));
?>
<div class="pg">
    <div class="container">
        <section class="pg-hero">
            <div class="hero-split">
                <div>
                    <span class="hero-chip"><?= icon('registon', '', 14) ?> <?= t('O‘quvchi fazosi') ?></span>
                    <h1><?= t('Assalomu alaykum') ?>, <span style="color:#fde68a"><?= e($user['display_name']) ?></span>!</h1>
                    <p>
                        <?php if ($continue): ?>
                            <?= t('Bugun «:title» darsini davom ettiramiz. Har kuni 5 daqiqa — va o‘zbek tili sizniki!', ['title' => loc($continue, 'title')]) ?>
                        <?php else: ?>
                            <?= t('Barcha ochiq darslarni tugatdingiz. Ajoyib! Diplomingizni yuklab oling yoki darslarni takrorlang.') ?>
                        <?php endif; ?>
                    </p>
                    <div class="xp-strip">
                        <span class="xp-chip"><span class="ico-flame"><?= icon('flame', '', 18) ?></span> <?= t(':n kun', ['n' => $streak]) ?> <small><?= t('ketma-ket') ?></small></span>
                        <span class="xp-chip"><span class="ico-star"><?= icon('star', '', 18) ?></span> <?= (int) $user['xp'] ?> XP <small><?= t(':n-daraja', ['n' => $level['level']]) ?></small></span>
                        <span class="xp-chip"><span class="ico-coins"><?= icon('coins', '', 18) ?></span> <?= (int) $user['coins'] ?> <small><?= t('tanga') ?></small></span>
                    </div>
                    <div class="dashboard-cta-row" style="margin-top:20px;display:flex;gap:10px;flex-wrap:wrap">
                        <?php if ($continue): ?>
                            <a href="/lessons/<?= (int) $continue['id'] ?>" class="btn btn-gold btn-lg"><?= icon('play', '', 18) ?> <?= t('Darsni davom ettirish') ?></a>
                        <?php endif; ?>
                        <a href="/flashcards" class="btn btn-primary btn-lg"><?= icon('layers', '', 18) ?> <?= t('Aqlli Flashcards') ?></a>
                        <a href="/leaderboard" class="btn btn-glass btn-lg"><?= icon('trophy', '', 18) ?> <?= t('Reyting va Liga') ?></a>
                        <a href="/courses" class="btn btn-ghost btn-lg" style="color:#fff;border-color:rgba(255,255,255,.2)"><?= icon('book-open', '', 18) ?> <?= t('Barcha kurslar') ?></a>
                    </div>
                </div>
                <div class="hero-owl-ring">
                    <?= mascot($continue ? 'welcome' : 'celebrate', 110) ?>
                    <div class="ring" style="--p: <?= $overall ?>">
                        <div><strong><?= $overall ?>%</strong><span><?= t('umumiy yo‘l') ?></span></div>
                    </div>
                </div>
            </div>
        </section>

        <?php if (!empty($telegramLinked)): ?>
            <!-- Telegram Bot Connected Badge -->
            <div class="tg-connected-box">
                <div class="tg-conn-left">
                    <span class="tg-conn-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.75-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z" fill="#0284c7"/></svg>
                    </span>
                    <div>
                        <div class="tg-conn-status">
                            <span class="tg-conn-dot"></span>
                            <strong><?= t('Telegram bot ulangan') ?>:</strong> <span class="tg-conn-handle">@<?= e($telegramUsername ?: $telegramFirstName) ?></span>
                        </div>
                        <div class="tg-conn-desc"><?= t('Har kuni 1 ta yangi so‘z, mini-viktorina va streak eslatmalari avtomatik yuboriladi.') ?></div>
                    </div>
                </div>
                <div class="tg-conn-actions">
                    <a href="https://t.me/<?= telegram_bot_username() ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-tg btn-sm">
                        <?= t('Botni ochish') ?> ↗
                    </a>
                    <form method="POST" action="/api/telegram/unlink" style="display:inline" onsubmit="return confirm('<?= t('Rostdan ham Telegram botni hisobingizdan uzmoqchimisiz?') ?>')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-ghost btn-sm tg-unlink-btn"><?= t('Uzish') ?></button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <!-- Algor.uz style 1-Click Telegram Bot Connection Banner -->
            <div class="tg-banner" id="tgBanner">
                <div class="tg-banner-glow"></div>
                <div class="tg-banner-inner">
                    <div class="tg-banner-badge-row">
                        <span class="tg-chip-bot">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.75-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z" fill="#ffffff"/></svg>
                            <span><?= t('Rasmiy Telegram Bot') ?></span>
                        </span>
                        <span class="tg-chip-bonus">🎁 <?= t('+20 XP va 5 tanga sovg‘a!') ?></span>
                    </div>
                    <div class="tg-banner-body">
                        <div class="tg-banner-text">
                            <h2><?= t('Telegram botimizga ulaning va har kuni 1 ta yangi so‘z o‘rganing!') ?></h2>
                            <p><?= t('Darslarni tashlab qo‘ymaslik uchun kundalik streak eslatmalari, mini-viktorinalar va ovozli yangi so‘zlar to‘g‘ridan-to‘g‘ri Telegramingizga keladi.') ?></p>
                            <div class="tg-features">
                                <span class="tg-feat-item">🔥 <strong><?= t('Streak eslatmasi') ?></strong></span>
                                <span class="tg-feat-item">📖 <strong><?= t('Kunlik 1 ta yangi so‘z') ?></strong></span>
                                <span class="tg-feat-item">🎯 <strong><?= t('Interaktiv mini-testlar') ?></strong></span>
                                <span class="tg-feat-item">⚡ <strong><?= t('1 bosishda ulanish') ?></strong></span>
                            </div>
                        </div>
                        <div class="tg-banner-action">
                            <a href="<?= e($telegramLinkUrl) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-telegram-cta" id="btnTgConnect">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.75-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z" fill="#0284c7"/></svg>
                                <span><?= t('Botni ulash (1 bosishda)') ?> ↗</span>
                            </a>
                            <div class="tg-btn-subtext"><?= t('Telegram ochiladi va «Start»ni bosish kifoya') ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
            (function() {
                var btn = document.getElementById('btnTgConnect');
                if (!btn) return;
                var pollTimer = setInterval(function() {
                    fetch('/api/telegram/status', { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
                    .then(function(res) { return res.json(); })
                    .then(function(data) {
                        if (data && data.linked) {
                            clearInterval(pollTimer);
                            var banner = document.getElementById('tgBanner');
                            if (banner) {
                                banner.innerHTML = '<div style="padding:28px 24px;text-align:center;background:linear-gradient(135deg,#059669,#10b981);color:#fff;border-radius:18px;box-shadow:0 12px 30px rgba(5,150,105,0.3);">' +
                                    '<div style="font-size:36px;line-height:1;margin-bottom:10px">🎉</div>' +
                                    '<h3 style="font-size:22px;font-weight:800;margin:0 0 6px">Tabriklaymiz! Telegram botingiz ulandi!</h3>' +
                                    '<p style="margin:0 0 16px;opacity:.95;font-size:15px">Hisobingizga <b>+20 XP</b> va <b>5 ta tanga</b> qo‘shildi. (@' + (data.username || data.first_name || 'foydalanuvchi') + ')</p>' +
                                    '<a href="/dashboard" class="btn btn-gold btn-sm" style="font-weight:700">Sahifani yangilash</a>' +
                                '</div>';
                                setTimeout(function() { window.location.reload(); }, 2200);
                            }
                        }
                    })
                    .catch(function() {});
                }, 3000);
            })();
            </script>
        <?php endif; ?>

        <div class="stat-grid">
            <div class="stat-tile tone-lapis">
                <span class="stat-ico"><?= icon('check-circle', '', 22) ?></span>
                <div><strong><?= $totalDone ?> / <?= $totalLessons ?></strong><span><?= t('o‘zlashtirilgan darslar') ?></span></div>
            </div>
            <div class="stat-tile tone-emerald">
                <span class="stat-ico"><?= icon('book-open', '', 22) ?></span>
                <div><strong><?= $wordsLearned ?></strong><span><?= t('o‘rganilgan so‘zlar') ?></span></div>
            </div>
            <div class="stat-tile tone-gold">
                <span class="stat-ico"><?= icon('star', '', 22) ?></span>
                <div style="flex:1">
                    <strong><?= t(':n-daraja', ['n' => $level['level']]) ?></strong><span><?= $level['into'] ?> / <?= $level['need'] ?> XP</span>
                    <div class="bar bar-gold"><i style="width: <?= $level['percent'] ?>%"></i></div>
                </div>
            </div>
            <div class="stat-tile tone-ruby">
                <span class="stat-ico"><?= icon('flame', '', 22) ?></span>
                <div style="flex:1">
                    <strong><?= $todayXp ?> / <?= $goalXp ?> XP</strong><span><?= $goalPct >= 100 ? t('Bugungi maqsad bajarildi!') : t('bugungi maqsad') ?></span>
                    <div class="bar bar-emerald"><i style="width: <?= $goalPct ?>%"></i></div>
                </div>
            </div>
        </div>

        <div class="layout-main-side">
            <div>
                <?php if ($recommended): ?>
                    <div class="card for-you tone-gold">
                        <?= mascot('learning', 72, 'owl--mini') ?>
                        <div style="flex:1;min-width:200px">
                            <div class="small" style="font-weight:800;letter-spacing:.6px;color:#b45309;text-transform:uppercase"><?= t('Siz uchun tavsiya') ?></div>
                            <strong style="font-family:var(--font-heading);font-size:20px;color:var(--ink)"><?= e(loc($recommended, 'title')) ?></strong>
                            <?php if ($interests): ?>
                                <div class="chip-cloud" style="margin-top:6px"><?php foreach (array_slice($interests, 0, 4) as $k): ?><span class="tag tag-gold"><?= t(ONB_INTERESTS[$k][0]) ?></span><?php endforeach; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="adm-actions">
                            <a href="/courses/<?= (int) $recommended['id'] ?>" class="btn btn-primary btn-sm"><?= icon('play', '', 14) ?> <?= t('Ochish') ?></a>
                            <a href="/onboarding" class="btn btn-ghost btn-sm"><?= t('Qiziqishlarim') ?></a>
                        </div>
                    </div>
                <?php endif; ?>
                <h2 class="section-h"><?= icon('layers', '', 24) ?> <?= t('Mening kurslarim') ?> <small><?= t('Keyingi dars uchun testdan 80%+ kerak') ?></small></h2>
                <div class="course-grid">
                    <?php foreach ($courseStates as $cs): $c = $cs['course']; ?>
                        <?php require __DIR__ . '/partials/course_card.php'; ?>
                    <?php endforeach; ?>
                </div>

                <h2 class="section-h"><?= icon('target', '', 24) ?> <?= t('So‘nggi natijalar') ?></h2>
                <div class="card">
                    <?php if (!$recent): ?>
                        <div class="empty">
                            <?= mascot('sleeping', 90) ?>
                            <?= t('Hali test topshirilmagan. Birinchi darsni boshlang — natijalar shu yerda ko‘rinadi!') ?>
                        </div>
                    <?php else: ?>
                        <div class="review-list">
                            <?php foreach ($recent as $r): ?>
                                <a class="review-item <?= $r['passed'] ? '' : 'is-wrong' ?>" href="/lessons/<?= (int) $r['lesson_id'] ?>/result?attempt=<?= (int) $r['id'] ?>" style="color:inherit">
                                    <span class="mark"><?= $r['passed'] ? icon('check-circle', '', 18) : icon('rotate-cw', '', 16) ?></span>
                                    <div class="row-between">
                                        <div>
                                            <h4><?= e(loc($r, 'lesson_title')) ?></h4>
                                            <div class="small muted"><?= e(substr((string) $r['created_at'], 0, 16)) ?> · +<?= (int) $r['xp_earned'] ?> XP</div>
                                        </div>
                                        <span class="heat heat-<?= mastery_label((int) $r['percent'])[1] ?>"><?= (int) $r['percent'] ?>%</span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <aside class="stack-sm">
                <!-- Top 5 Leaderboard Widget -->
                <div class="card tone-gold leaderboard-widget">
                    <div class="row-between" style="align-items:center;margin-bottom:12px">
                        <h3 class="card-title" style="margin:0"><span class="icon-wrap"><?= icon('trophy', '', 20) ?></span> <?= t('Haftalik Liga (Top 5)') ?></h3>
                        <a href="/leaderboard" class="small" style="font-weight:700;color:var(--gold-dark);"><?= t('Barchasi →') ?></a>
                    </div>
                    <?php if (!empty($topLeaders)): ?>
                        <div class="mini-leader-list">
                            <?php foreach ($topLeaders as $i => $lead): 
                                $isMe = (int)$lead['id'] === (int)$user['id'];
                                $rankIco = match($i) {
                                    0 => '🥇',
                                    1 => '🥈',
                                    2 => '🥉',
                                    default => '#' . ($i + 1)
                                };
                            ?>
                                <div class="mini-leader-row <?= $isMe ? 'is-me' : '' ?>">
                                    <span class="mini-rank"><?= $rankIco ?></span>
                                    <span class="mini-name"><?= e($lead['display_name']) ?> <?= $isMe ? '<small class="you-pill">' . t('Siz') . '</small>' : '' ?></span>
                                    <span class="mini-xp"><?= (int)$lead['xp'] ?> XP</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <div class="widget-user-rank" style="margin-top:12px;padding-top:10px;border-top:1px dashed rgba(0,0,0,.1);display:flex;justify-content:space-between;align-items:center;">
                        <span class="small muted"><?= t('Sizning o‘rningiz:') ?> <strong>#<?= $myRank ?></strong></span>
                        <a href="/leaderboard" class="btn btn-primary btn-xs"><?= t('Reyting jadvali') ?></a>
                    </div>
                </div>

                <!-- Smart Flashcards Widget -->
                <div class="card tone-teal" style="background:linear-gradient(135deg, #f0fdfa, #ccfbf1);border-color:#5eead4;">
                    <div style="display:flex;gap:12px;align-items:center">
                        <?= mascot('smart', 54, 'owl--mini') ?>
                        <div>
                            <strong style="display:block;color:#0f766e;font-size:16px"><?= t('Aqlli Flashcards') ?></strong>
                            <p class="small" style="color:#115e59;margin:2px 0 0"><?= t('So‘zlarni 3D kartochkalarda tezkor takrorlang va +2 XP to‘plang!') ?></p>
                        </div>
                    </div>
                    <a href="/flashcards" class="btn btn-emerald btn-sm btn-block" style="margin-top:12px;background:#0d9488;border-color:#0f766e">
                        <?= icon('layers', '', 16) ?> <?= t('Mashqni boshlash') ?>
                    </a>
                </div>

                <div class="card tone-gold">
                    <h3 class="card-title"><span class="icon-wrap"><?= icon('trophy', '', 20) ?></span> <?= t('Nishonlar') ?></h3>
                    <div class="bdg-grid">
                        <?php foreach ($badges as $b): ?>
                            <div class="bdg-item <?= $b['earned_at'] ? '' : 'is-off' ?>" title="<?= e(t($b['description'])) ?>">
                                <div class="b-ico"><?= icon($b['icon'], '', 20) ?></div>
                                <strong><?= e(t($b['title'])) ?></strong>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="card tone-lapis">
                    <h3 class="card-title"><span class="icon-wrap"><?= icon('video', '', 20) ?></span> <?= t('Jonli muloqot') ?></h3>
                    <?php if ($liveSession): ?>
                        <strong style="display:block;color:var(--ink)"><?= e($liveSession['title']) ?></strong>
                        <p class="small muted" style="margin:4px 0 10px"><?= e(uz_date($liveSession['starts_at'], normalize_timezone($user['timezone']))) ?></p>
                        <div class="course-meta" style="margin-bottom:12px">
                            <?php foreach (live_time_labels($liveSession['starts_at']) as $zone => $time): ?>
                                <span class="tag tag-lapis"><?= e($time) ?> <?= e($zone) ?></span>
                            <?php endforeach; ?>
                        </div>
                        <a href="/live" class="btn btn-primary btn-sm btn-block"><?= icon('video', '', 16) ?> <?= t('Xonaga o‘tish') ?></a>
                    <?php else: ?>
                        <p class="small muted"><?= t('Yaqin kunlarda jonli dars rejalashtirilmagan. Ustoz yangi vaqt e’lon qilganda shu yerda ko‘rinadi.') ?></p>
                    <?php endif; ?>
                </div>

                <div class="card tone-emerald">
                    <h3 class="card-title"><span class="icon-wrap"><?= icon('heart', '', 20) ?></span> <?= t('Kunlik maslahat') ?></h3>
                    <p class="small muted"><?= t('Har kuni 5 daqiqa yetarli: o‘qish bo‘limidagi gaplarni baland ovozda takrorlang. Farzandingiz bilan o‘qisangiz — navbatma-navbat ayting.') ?></p>
                    <a href="/parent-tips" class="btn btn-ghost btn-sm btn-block" style="margin-top:10px"><?= t('Samarali o‘rganish qo‘llanmasi') ?></a>
                </div>
            </aside>
        </div>
    </div>
</div>
