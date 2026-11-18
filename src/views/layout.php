<?php
$user = auth_user();
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'LingoUz by Gulomov’s Academy') ?></title>
    <meta name="description" content="AQShda yashovchi 7–11 yoshli o‘zbek bolalari uchun o‘yinlashtirilgan, interaktiv o‘zbek tili maktabi.">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg?v=1">
    <link rel="icon" type="image/x-icon" href="/favicon.ico?v=1">
    <link rel="apple-touch-icon" href="/favicon.svg?v=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/style.css?v=<?= time() ?>">
</head>
<body class="registon-theme">
    <header class="site-header">
        <div class="container header-inner">
            <a href="/" class="brand-logo">
                <span class="logo-mark"><img src="/assets/mascot-lingo.svg" alt="Lingo" width="40" height="40" class="brand-mascot-img"></span>
                <span class="logo-text">
                    <span class="brand-name">Lingo<span class="highlight">Uz</span></span>
                    <span class="brand-sub">by Gulomov’s Academy</span>
                </span>
            </a>

            <nav class="main-nav" id="mainNav">
                <a href="/">Bosh sahifa</a>
                <a href="/#metodika">Metodika</a>
                <a href="/#darslar">Darslar</a>
                <a href="/parent-tips">Ota-onalarga</a>
                <a href="/#ariza" class="nav-highlight">Pilotga ariza</a>
                <?php if ($user): ?>
                    <a href="/dashboard" class="nav-pill"><?= icon('layers', '', 16) ?> O‘quvchi Paneli</a>
                    <a href="/live"><?= icon('video', '', 16) ?> Jonli Muloqot</a>
                    <a href="/certificate" class="nav-cert"><?= icon('award', '', 16) ?> Diplom</a>
                    <?php if (is_admin()): ?>
                        <a href="/admin" class="nav-admin"><?= icon('shield-check', '', 16) ?> Admin</a>
                    <?php endif; ?>
                <?php endif; ?>
            </nav>

            <div class="header-actions">
                <?php if ($user): ?>
                    <div class="user-chip">
                        <a href="/profile" class="user-profile-link" title="Profil sozlamalari">
                            <span class="user-avatar-icon"><?= icon('user', '', 16) ?></span>
                            <span class="user-name"><?= e($user['display_name']) ?></span>
                        </a>
                        <form action="/logout" method="POST" style="display:inline; margin-left:8px;">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn-logout" title="Chiqish"><?= icon('log-out', '', 14) ?></button>
                        </form>
                    </div>
                <?php else: ?>
                    <a href="/login" class="btn btn-outline btn-sm">Kirish</a>
                    <a href="/register" class="btn btn-primary btn-sm">Ro‘yxatdan o‘tish</a>
                <?php endif; ?>
                <button class="menu-toggle" id="menuToggle" aria-label="Menyu">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
            </div>
        </div>
    </header>

    <?php if (has_flash('success') || has_flash('error') || has_flash('info')): ?>
        <div class="container flash-container">
            <?php if ($msg = get_flash('success')): ?>
                <div class="flash-alert flash-success">
                    <span class="flash-icon"><?= icon('check-circle', '', 20) ?></span>
                    <span><?= e($msg) ?></span>
                </div>
            <?php endif; ?>
            <?php if ($msg = get_flash('error')): ?>
                <div class="flash-alert flash-error">
                    <span class="flash-icon"><?= icon('shield-check', '', 20) ?></span>
                    <span><?= e($msg) ?></span>
                </div>
            <?php endif; ?>
            <?php if ($msg = get_flash('info')): ?>
                <div class="flash-alert flash-info">
                    <span class="flash-icon"><?= icon('sparkles', '', 20) ?></span>
                    <span><?= e($msg) ?></span>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <main class="main-content">
        <?= $content ?>
    </main>

    <footer class="site-footer">
        <div class="container footer-inner">
            <div class="footer-col brand-col">
                <div class="footer-logo">
                    <img src="/assets/mascot-lingo.svg" alt="Lingo" width="34" height="34" style="vertical-align:middle;">
                    <span class="brand-name">Lingo<span class="highlight">Uz</span></span>
                </div>
                <p class="footer-desc">
                    Gulomov’s Academy tashabbusi bilan AQShda yashovchi 7–11 yoshli o‘zbek bolalari uchun yaratilgan zamonaviy, interaktiv o‘zbek tili akademiyasi.
                </p>
                <div class="badge-safe"><?= icon('shield-check', '', 16) ?> COPPA va bolalar xavfsizligi talablariga mos</div>
            </div>

            <div class="footer-col">
                <h4>Tezkor sahifalar</h4>
                <ul>
                    <li><a href="/">Bosh sahifa</a></li>
                    <li><a href="/#metodika">Ta’lim metodikasi</a></li>
                    <li><a href="/#darslar">5 ta darslik MVP rejasi</a></li>
                    <li><a href="/#ariza">Pilot dasturiga yozilish</a></li>
                    <li><a href="/live">Zoom Speaking Room</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Ota-onalar va Qoidalar</h4>
                <ul>
                    <li><a href="/parent-tips">Ota-onalar uchun qo‘llanma</a></li>
                    <li><a href="/privacy">Maxfiylik siyosati (COPPA)</a></li>
                    <li><a href="/#faq">Ko‘p beriladigan savollar</a></li>
                    <li><a href="/login">Tizimga kirish</a></li>
                </ul>
            </div>

            <div class="footer-col contact-col">
                <h4>Aloqa va Pilot</h4>
                <p><?= icon('map-pin', '', 14) ?> Toshkent — Nyu-York</p>
                <p>gulomov@vexa.uz</p>
                <p>Telegram: @gulomovs_academy</p>
                <div class="timezones-pill">🇺🇸 EST • CST • MST • PST</div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="container footer-bottom-inner">
                <p>© 2026 LingoUz by Gulomov’s Academy. Barcha huquqlar himoyalangan.</p>
                <p class="tech-tag">Registon Heritage Edition | Laravel + SQLite</p>
            </div>
        </div>
    </footer>

    <script src="/assets/app.js?v=<?= time() ?>"></script>
</body>
</html>
