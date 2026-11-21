<?php
$user = auth_user();
?>
<!DOCTYPE html>
<html lang="<?= locale() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'LingoUz by Gulomov’s Academy') ?></title>
    <meta name="description" content="<?= t('O‘zbek tilini o‘rganmoqchi bo‘lgan hamma uchun — bolalar, kattalar va sayyohlar. Ovozli lug‘at, mashqlar, madaniyat va jonli darslar. Learn Uzbek online.') ?>">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg?v=1">
    <link rel="icon" type="image/x-icon" href="/favicon.ico?v=1">
    <link rel="apple-touch-icon" href="/favicon.svg?v=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/style.css?v=<?= filemtime(__DIR__ . '/../../public/assets/style.css') ?>">
    <link rel="stylesheet" href="/assets/app-v3.css?v=<?= filemtime(__DIR__ . '/../../public/assets/app-v3.css') ?>">
    <link rel="stylesheet" href="/assets/mascot.css?v=<?= filemtime(__DIR__ . '/../../public/assets/mascot.css') ?>">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
</head>
<body class="registon-theme">
    <!-- Site Header -->
    <header class="site-header">
        <div class="container header-inner">
            <a href="/" class="brand-logo">
                <span class="logo-mark"><img src="/assets/mascot-owl.svg" alt="Boyo‘g‘li" width="40" height="46" class="brand-mascot-img"></span>
                <span class="logo-text">
                    <span class="brand-name">Lingo<span class="highlight">Uz</span></span>
                    <span class="brand-sub">by Gulomov’s Academy</span>
                </span>
            </a>

            <!-- Desktop Navigation -->
            <nav class="main-nav desktop-nav">
                <?php if ($user): ?>
                    <!-- Logged in (Student / Admin): Priority app navigation -->
                    <a href="/dashboard" class="nav-pill"><?= icon('layers', '', 16) ?> <?= t('Mening sahifam') ?></a>
                    <a href="/courses"><?= icon('book-open', '', 16) ?> <?= t('Kurslar') ?></a>
                    <a href="/live"><?= icon('video', '', 16) ?> <?= t('Jonli Muloqot') ?></a>
                    <?php if (is_admin()): ?>
                        <a href="/admin" class="nav-admin"><?= icon('shield-check', '', 16) ?> Admin</a>
                    <?php endif; ?>
                    <a href="/parent-tips"><?= icon('users', '', 16) ?> <?= t('Qo‘llanma') ?></a>
                <?php else: ?>
                    <!-- Guest Navigation -->
                    <a href="/"><?= t('Bosh sahifa') ?></a>
                    <a href="/#metodika"><?= t('Metodika') ?></a>
                    <a href="/#darslar"><?= t('Darslar') ?></a>
                    <a href="/parent-tips"><?= t('Qo‘llanma') ?></a>
                    <a href="/#ariza" class="nav-highlight"><?= t('Pilotga ariza') ?></a>
                <?php endif; ?>
            </nav>

            <!-- Header Actions -->
            <div class="header-actions">
                <div class="lang-switch" role="group" aria-label="Language">
                    <?php foreach (LOCALES as $code => $name): ?><a href="<?= e(lang_url($code)) ?>" class="<?= locale() === $code ? 'is-active' : '' ?>" hreflang="<?= $code ?>" title="<?= $name ?>"><?= strtoupper($code) ?></a><?php endforeach; ?>
                </div>
                                <?php if ($user): ?>
                    <div class="user-chip desktop-only">
                        <a href="/profile" class="user-profile-link" title="<?= t('Profil sozlamalari') ?>">
                            <span class="user-avatar-icon"><?= icon('user', '', 16) ?></span>
                            <span class="user-name"><?= e($user['display_name']) ?></span>
                        </a>
                        <form action="/logout" method="POST" class="inline-form">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn-header-logout" title="<?= t('Tizimdan chiqish') ?>">
                                <?= icon('log-out', '', 13) ?> <span><?= t('Chiqish') ?></span>
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <a href="/login" class="btn btn-outline btn-sm desktop-only"><?= t('Kirish') ?></a>
                    <a href="/register" class="btn btn-primary btn-sm desktop-only"><?= t('Ro‘yxatdan o‘tish') ?></a>
                <?php endif; ?>

                <!-- Responsive Menu Toggle Button -->
                <button class="menu-toggle" id="menuToggle" aria-label="<?= t('Menyuni ochish') ?>" type="button">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Slide-out Drawer Navigation Backdrop -->
    <div class="nav-drawer-backdrop" id="drawerBackdrop"></div>

    <!-- Mobile Off-Canvas Drawer -->
    <div class="nav-drawer" id="mobileDrawer">
        <div class="drawer-header">
            <div class="drawer-brand">
                <img src="/assets/mascot-owl.svg" alt="Boyo‘g‘li" width="36" height="41">
                <div>
                    <div class="brand-name" style="font-size:20px;">Lingo<span class="highlight">Uz</span></div>
                    <div class="brand-sub">Gulomov’s Academy</div>
                </div>
            </div>
            <button class="drawer-close-btn" id="drawerClose" type="button" aria-label="<?= t('Yopish') ?>">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <?php if ($user): ?>
            <!-- User Info Inside Drawer -->
            <div class="drawer-user-box">
                <div class="drawer-user-avatar">
                    <?= icon('user', '', 20) ?>
                </div>
                <div class="drawer-user-details">
                    <div class="drawer-user-name"><?= e($user['display_name']) ?></div>
                    <div class="drawer-user-role"><?= $user['role'] === 'admin' ? t('Administrator') : t('O‘quvchi') ?> • <a href="/profile"><?= t('Sozlamalar') ?></a></div>
                </div>
            </div>
        <?php endif; ?>

        <div class="drawer-nav-list">
            <?php if ($user): ?>
                <div class="drawer-section-label"><?= t('O‘quv Xonasi') ?></div>
                <a href="/dashboard" class="drawer-link active">
                    <span class="drawer-icon"><?= icon('layers', '', 18) ?></span>
                    <span><?= t('Mening sahifam') ?></span>
                </a>
                <a href="/courses" class="drawer-link">
                    <span class="drawer-icon"><?= icon('book-open', '', 18) ?></span>
                    <span><?= t('Kurslar va darslar') ?></span>
                </a>
                <a href="/live" class="drawer-link">
                    <span class="drawer-icon"><?= icon('video', '', 18) ?></span>
                    <span><?= t('Zoom Jonli Muloqot') ?></span>
                </a>
                <?php if (is_admin()): ?>
                    <a href="/admin" class="drawer-link admin-link">
                        <span class="drawer-icon"><?= icon('shield-check', '', 18) ?></span>
                        <span><?= t('Admin Boshqaruv Markazi') ?></span>
                    </a>
                <?php endif; ?>
                <a href="/profile" class="drawer-link">
                    <span class="drawer-icon"><?= icon('settings', '', 18) ?></span>
                    <span><?= t('Profil Sozlamalari') ?></span>
                </a>

                <div class="drawer-section-label" style="margin-top:16px;"><?= t('Akademiya') ?></div>
                <a href="/" class="drawer-link">
                    <span class="drawer-icon"><?= icon('registon', '', 18) ?></span>
                    <span><?= t('Bosh sahifa') ?></span>
                </a>
                <a href="/parent-tips" class="drawer-link">
                    <span class="drawer-icon"><?= icon('book-open', '', 18) ?></span>
                    <span><?= t('Ota-onalar Qo‘llanmasi') ?></span>
                </a>
            <?php else: ?>
                <div class="drawer-section-label"><?= t('Menyu') ?></div>
                <a href="/" class="drawer-link">
                    <span class="drawer-icon"><?= icon('registon', '', 18) ?></span>
                    <span><?= t('Bosh sahifa') ?></span>
                </a>
                <a href="/#metodika" class="drawer-link">
                    <span class="drawer-icon"><?= icon('sparkles', '', 18) ?></span>
                    <span><?= t('Ta’lim Metodikasi') ?></span>
                </a>
                <a href="/#darslar" class="drawer-link">
                    <span class="drawer-icon"><?= icon('book-open', '', 18) ?></span>
                    <span><?= t('Kurslar va darslar') ?></span>
                </a>
                <a href="/parent-tips" class="drawer-link">
                    <span class="drawer-icon"><?= icon('users', '', 18) ?></span>
                    <span><?= t('Ota-onalar Qo‘llanmasi') ?></span>
                </a>
                <a href="/#ariza" class="drawer-link highlight-link">
                    <span class="drawer-icon"><?= icon('arrow-right', '', 18) ?></span>
                    <span><?= t('Bepul Pilotga Ariza') ?></span>
                </a>
            <?php endif; ?>
        </div>

        <div class="drawer-footer">
            <?php if ($user): ?>
                <form action="/logout" method="POST">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger btn-block"><?= icon('log-out', '', 16) ?> <?= t('Tizimdan chiqish') ?></button>
                </form>
            <?php else: ?>
                <div class="drawer-auth-buttons">
                    <a href="/login" class="btn btn-outline btn-block"><?= t('Kirish') ?></a>
                    <a href="/register" class="btn btn-primary btn-block"><?= t('Ro‘yxatdan o‘tish') ?></a>
                </div>
            <?php endif; ?>
        </div>
    </div>

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
                    <img src="/assets/mascot-owl.svg" alt="Boyo‘g‘li" width="34" height="39" style="vertical-align:middle;">
                    <span class="brand-name">Lingo<span class="highlight">Uz</span></span>
                </div>
                <p class="footer-desc">
                    <?= t('Gulomov’s Academy tashabbusi: bolalar, kattalar va sayyohlar — o‘zbek tilini o‘rganmoqchi bo‘lgan hamma uchun zamonaviy, interaktiv onlayn akademiya.') ?>
                </p>
                <div class="badge-safe"><?= icon('shield-check', '', 16) ?> <?= t('Bolalar uchun ham xavfsiz (COPPA)') ?></div>
            </div>

            <div class="footer-col">
                <h4><?= t('Tezkor sahifalar') ?></h4>
                <ul>
                    <li><a href="/"><?= t('Bosh sahifa') ?></a></li>
                    <li><a href="/#metodika"><?= t('Ta’lim metodikasi') ?></a></li>
                    <li><a href="/#darslar"><?= t('Kurslar va darslar') ?></a></li>
                    <li><a href="/#madaniyat"><?= t('Madaniyat burchagi') ?></a></li>
                    <li><a href="/#ariza"><?= t('Pilot dasturiga yozilish') ?></a></li>
                    <li><a href="/live">Zoom Speaking Room</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4><?= t('Qo‘llanma va Qoidalar') ?></h4>
                <ul>
                    <li><a href="/parent-tips"><?= t('Samarali o‘rganish qo‘llanmasi') ?></a></li>
                    <li><a href="/privacy"><?= t('Maxfiylik siyosati (COPPA)') ?></a></li>
                    <li><a href="/#faq"><?= t('Ko‘p beriladigan savollar') ?></a></li>
                    <li><a href="/login"><?= t('Tizimga kirish') ?></a></li>
                </ul>
            </div>

            <div class="footer-col contact-col">
                <h4><?= t('Aloqa va Pilot') ?></h4>
                <p><?= icon('map-pin', '', 14) ?> <?= t('Toshkent — butun dunyo') ?></p>
                <p>gulomov@vexa.uz</p>
                <p>Telegram: @gulomovs_academy</p>
                <div class="timezones-pill"><?= t('Toshkent • London • Nyu-York • LA') ?></div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="container footer-bottom-inner">
                <p><?= t('© 2026 LingoUz by Gulomov’s Academy. Barcha huquqlar himoyalangan.') ?></p>
                <p class="tech-tag">Registon Heritage Edition | PHP + SQLite</p>
            </div>
        </div>
    </footer>

    <?php if (is_en()): ?><script>window.I18N = <?= json_encode(js_translations(), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;</script><?php endif; ?>
    <script src="/assets/app.js?v=<?= filemtime(__DIR__ . '/../../public/assets/app.js') ?>"></script>
</body>
</html>
