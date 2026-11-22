<?php
$user = auth_user();
?>
<!DOCTYPE html>
<html lang="<?= locale() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'LingoUz') ?></title>
    <meta name="description" content="<?= t('O‘zbek tilini o‘rganmoqchi bo‘lgan hamma uchun — bolalar, kattalar va sayyohlar. Ovozli lug‘at, mashqlar, madaniyat va jonli darslar. Learn Uzbek online.') ?>">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg?v=<?= filemtime(__DIR__ . '/../../public/favicon.svg') ?>">
    <link rel="icon" type="image/x-icon" href="/favicon.ico?v=<?= filemtime(__DIR__ . '/../../public/favicon.ico') ?>">
    <link rel="apple-touch-icon" href="/assets/apple-touch-icon.png?v=<?= filemtime(__DIR__ . '/../../public/assets/apple-touch-icon.png') ?>">
    <link rel="manifest" href="/manifest.webmanifest?v=<?= filemtime(__DIR__ . '/../../public/manifest.webmanifest') ?>">
    <meta name="theme-color" content="#0284c7">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="LingoUz">
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
                <span class="logo-mark"><img src="/assets/mascot-owl.svg" alt="Lingo" width="40" height="46" class="brand-mascot-img"></span>
                <span class="logo-text">
                    <span class="brand-name">Lingo<span class="highlight">Uz</span></span>
                </span>
            </a>

            <!-- Desktop Navigation -->
            <nav class="main-nav desktop-nav">
                <?php if ($user): ?>
                    <!-- Logged in Student: Priority learning navigation (5 core tabs) -->
                    <a href="/dashboard" class="<?= is_nav_active('/dashboard') ? 'is-active' : '' ?>"><?= icon('layers', '', 16) ?> <?= t('Mening sahifam') ?></a>
                    <a href="/courses" class="<?= is_nav_active(['/courses', '/lessons']) ? 'is-active' : '' ?>"><?= icon('book-open', '', 16) ?> <?= t('Kurslar') ?></a>
                    <a href="/flashcards" class="<?= is_nav_active('/flashcards') ? 'is-active' : '' ?>"><?= icon('layers', '', 16) ?> <?= t('Flashcards') ?></a>
                    <a href="/leaderboard" class="<?= is_nav_active('/leaderboard') ? 'is-active' : '' ?>"><?= icon('trophy', '', 16) ?> <?= t('Reyting') ?></a>
                    <a href="/live" class="<?= is_nav_active('/live') ? 'is-active' : '' ?>"><?= icon('video', '', 16) ?> <?= t('Jonli Muloqot') ?></a>
                <?php else: ?>
                    <!-- Guest Navigation: streamlined core links without duplicates -->
                    <a href="/" class="<?= is_nav_active('/') ? 'is-active' : '' ?>"><?= icon('registon', '', 16) ?> <?= t('Bosh sahifa') ?></a>
                    <a href="/courses" class="<?= is_nav_active(['/courses', '/lessons']) ? 'is-active' : '' ?>"><?= icon('book-open', '', 16) ?> <?= t('Darslar') ?></a>
                    <a href="/flashcards" class="<?= is_nav_active('/flashcards') ? 'is-active' : '' ?>"><?= icon('layers', '', 16) ?> <?= t('Flashcards') ?></a>
                    <a href="/leaderboard" class="<?= is_nav_active('/leaderboard') ? 'is-active' : '' ?>"><?= icon('trophy', '', 16) ?> <?= t('Reyting') ?></a>
                    <a href="/parent-tips" class="<?= is_nav_active('/parent-tips') ? 'is-active' : '' ?>"><?= icon('users', '', 16) ?> <?= t('Qo‘llanma') ?></a>
                <?php endif; ?>
            </nav>

            <!-- Header Actions -->
            <div class="header-actions">
                <button class="pwa-install-pill" id="pwaHeaderInstallBtn" type="button" title="<?= t('LingoUz mobil ilovasini yuklash') ?>">
                    <span class="pwa-pill-icon">📥</span> <span><?= t('Yuklash') ?></span>
                </button>
                <div class="lang-switch" role="group" aria-label="Language">
                    <?php foreach (LOCALES as $code => $name): ?><a href="<?= e(lang_url($code)) ?>" class="<?= locale() === $code ? 'is-active' : '' ?>" hreflang="<?= $code ?>" title="<?= $name ?>"><?= strtoupper($code) ?></a><?php endforeach; ?>
                </div>
                <?php if ($user): ?>
                    <div class="user-chip desktop-only">
                        <?php if (is_admin()): ?>
                            <a href="/admin" class="btn-chip-admin <?= is_nav_active('/admin') ? 'is-active' : '' ?>" title="<?= t('Admin Boshqaruv Markazi') ?>">
                                <?= icon('shield-check', '', 14) ?> <span>Admin</span>
                            </a>
                        <?php endif; ?>
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

    <!-- Smart Mobile App Install Banner -->
    <div class="mobile-app-banner" id="mobileAppBanner">
        <div class="app-banner-left">
            <img src="/assets/mascot-owl.svg" alt="LingoUz" width="34" height="39" class="app-banner-icon">
            <div class="app-banner-text">
                <div class="app-banner-title"><?= t('LingoUz Ilovasi') ?></div>
                <div class="app-banner-sub"><?= t('Tez va qulay mobil ilova') ?></div>
            </div>
        </div>
        <div class="app-banner-right">
            <button type="button" class="btn-app-banner-install" id="pwaBannerInstallBtn">
                <?= icon('download', '', 13) ?> <span><?= t('Yuklash') ?></span>
            </button>
            <button type="button" class="btn-app-banner-close" id="pwaBannerClose" aria-label="<?= t('Yopish') ?>" title="<?= t('Yopish') ?>">&times;</button>
        </div>
    </div>

    <!-- Mobile Slide-out Drawer Navigation Backdrop -->
    <div class="nav-drawer-backdrop" id="drawerBackdrop"></div>

    <!-- Mobile Off-Canvas Drawer -->
    <div class="nav-drawer" id="mobileDrawer">
        <div class="drawer-header">
            <div class="drawer-brand">
                <img src="/assets/mascot-owl.svg" alt="Lingo" width="36" height="41">
                <div>
                    <div class="brand-name" style="font-size:20px;">Lingo<span class="highlight">Uz</span></div>
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
                <a href="/dashboard" class="drawer-link <?= is_nav_active('/dashboard') ? 'active' : '' ?>">
                    <span class="drawer-icon"><?= icon('layers', '', 18) ?></span>
                    <span><?= t('Mening sahifam') ?></span>
                </a>
                <a href="/courses" class="drawer-link <?= is_nav_active(['/courses', '/lessons']) ? 'active' : '' ?>">
                    <span class="drawer-icon"><?= icon('book-open', '', 18) ?></span>
                    <span><?= t('Kurslar va darslar') ?></span>
                </a>
                <a href="/flashcards" class="drawer-link <?= is_nav_active('/flashcards') ? 'active' : '' ?>">
                    <span class="drawer-icon"><?= icon('layers', '', 18) ?></span>
                    <span><?= t('Aqlli Flashcards') ?></span>
                </a>
                <a href="/leaderboard" class="drawer-link <?= is_nav_active('/leaderboard') ? 'active' : '' ?>">
                    <span class="drawer-icon"><?= icon('trophy', '', 18) ?></span>
                    <span><?= t('Reyting va Liga') ?></span>
                </a>
                <a href="/live" class="drawer-link <?= is_nav_active('/live') ? 'active' : '' ?>">
                    <span class="drawer-icon"><?= icon('video', '', 18) ?></span>
                    <span><?= t('Zoom Jonli Muloqot') ?></span>
                </a>
                <?php if (is_admin()): ?>
                    <a href="/admin" class="drawer-link admin-link <?= is_nav_active('/admin') ? 'active' : '' ?>">
                        <span class="drawer-icon"><?= icon('shield-check', '', 18) ?></span>
                        <span><?= t('Admin Boshqaruv Markazi') ?></span>
                    </a>
                <?php endif; ?>
                <a href="/profile" class="drawer-link <?= is_nav_active('/profile') ? 'active' : '' ?>">
                    <span class="drawer-icon"><?= icon('settings', '', 18) ?></span>
                    <span><?= t('Profil Sozlamalari') ?></span>
                </a>

                <div class="drawer-section-label" style="margin-top:16px;"><?= t('Akademiya') ?></div>
                <a href="/" class="drawer-link <?= is_nav_active('/') ? 'active' : '' ?>">
                    <span class="drawer-icon"><?= icon('registon', '', 18) ?></span>
                    <span><?= t('Bosh sahifa') ?></span>
                </a>
                <a href="/parent-tips" class="drawer-link <?= is_nav_active('/parent-tips') ? 'active' : '' ?>">
                    <span class="drawer-icon"><?= icon('book-open', '', 18) ?></span>
                    <span><?= t('Ota-onalar Qo‘llanmasi') ?></span>
                </a>
            <?php else: ?>
                <div class="drawer-section-label"><?= t('Menyu') ?></div>
                <a href="/" class="drawer-link <?= is_nav_active('/') ? 'active' : '' ?>">
                    <span class="drawer-icon"><?= icon('registon', '', 18) ?></span>
                    <span><?= t('Bosh sahifa') ?></span>
                </a>
                <a href="/leaderboard" class="drawer-link <?= is_nav_active('/leaderboard') ? 'active' : '' ?>">
                    <span class="drawer-icon"><?= icon('trophy', '', 18) ?></span>
                    <span><?= t('Reyting va Liga') ?></span>
                </a>
                <a href="/flashcards" class="drawer-link <?= is_nav_active('/flashcards') ? 'active' : '' ?>">
                    <span class="drawer-icon"><?= icon('layers', '', 18) ?></span>
                    <span><?= t('Aqlli Flashcards') ?></span>
                </a>
                <a href="/#metodika" class="drawer-link">
                    <span class="drawer-icon"><?= icon('sparkles', '', 18) ?></span>
                    <span><?= t('Ta’lim Metodikasi') ?></span>
                </a>
                <a href="/courses" class="drawer-link <?= is_nav_active(['/courses', '/lessons']) ? 'active' : '' ?>">
                    <span class="drawer-icon"><?= icon('book-open', '', 18) ?></span>
                    <span><?= t('Kurslar va darslar') ?></span>
                </a>
                <a href="/parent-tips" class="drawer-link <?= is_nav_active('/parent-tips') ? 'active' : '' ?>">
                    <span class="drawer-icon"><?= icon('users', '', 18) ?></span>
                    <span><?= t('Ota-onalar Qo‘llanmasi') ?></span>
                </a>
                <a href="/register" class="drawer-link highlight-link <?= is_nav_active('/register') ? 'active' : '' ?>">
                    <span class="drawer-icon"><?= icon('arrow-right', '', 18) ?></span>
                    <span><?= t('Ro‘yxatdan o‘tish') ?></span>
                </a>
            <?php endif; ?>

            <!-- PWA Install Button inside Drawer -->
            <div class="drawer-pwa-wrap" id="drawerPwaWrap" style="margin-top:18px;padding-top:14px;border-top:1px solid rgba(0,0,0,.08);">
                <button type="button" class="btn btn-primary btn-block pwa-drawer-install-btn" id="pwaDrawerBtn">
                    📥 <?= t('LingoUz ilovasini yuklash (PWA)') ?>
                </button>
            </div>
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

    <!-- PWA Install Guide Modal (iOS & Android) -->
    <div class="pwa-guide-backdrop" id="pwaGuideBackdrop" style="display:none;"></div>
    <div class="pwa-guide-modal" id="pwaGuideModal" style="display:none;" role="dialog" aria-modal="true" aria-labelledby="pwaModalTitle">
        <div class="pwa-guide-header">
            <div class="pwa-guide-title-box">
                <span class="pwa-guide-icon">📱</span>
                <h3 class="pwa-guide-title" id="pwaModalTitle"><?= t('LingoUz ilovasini o‘rnatish') ?></h3>
            </div>
            <button class="pwa-guide-close" id="pwaGuideClose" type="button" aria-label="<?= t('Yopish') ?>">&times;</button>
        </div>
        <div class="pwa-guide-body" id="pwaGuideBody"></div>
        <div class="pwa-guide-footer">
            <button type="button" class="btn btn-primary btn-block" id="pwaGuideOkBtn"><?= t('Tushunarli') ?></button>
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
                <a href="/" class="footer-logo">
                    <img src="/assets/mascot-owl.svg" alt="Lingo" width="34" height="39" style="vertical-align:middle;">
                    <span class="brand-name">Lingo<span class="highlight">Uz</span></span>
                </a>
                <p class="footer-desc">
                    <?= t('O‘zbek tilini o‘rganmoqchi bo‘lgan hamma uchun zamonaviy, interaktiv onlayn ta’lim platformasi.') ?>
                </p>
            </div>

            <div class="footer-col">
                <h4><?= t('Tezkor sahifalar') ?></h4>
                <ul>
                    <li><a href="/"><?= t('Bosh sahifa') ?></a></li>
                    <li><a href="/#metodika"><?= t('Ta’lim metodikasi') ?></a></li>
                    <li><a href="/#darslar"><?= t('Kurslar va darslar') ?></a></li>
                    <li><a href="/#madaniyat"><?= t('Madaniyat burchagi') ?></a></li>
                    <li><a href="/register"><?= t('Ro‘yxatdan o‘tish') ?></a></li>
                    <li><a href="/live">Zoom Speaking Room</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4><?= t('Qo‘llanma va Qoidalar') ?></h4>
                <ul>
                    <li><a href="/parent-tips"><?= t('Samarali o‘rganish qo‘llanmasi') ?></a></li>
                    <li><a href="/privacy"><?= t('Maxfiylik siyosati') ?></a></li>
                    <li><a href="/#faq"><?= t('Ko‘p beriladigan savollar') ?></a></li>
                    <li><a href="/login"><?= t('Tizimga kirish') ?></a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="container footer-bottom-inner">
                <p><?= t('© 2026 LingoUz. Barcha huquqlar himoyalangan.') ?></p>
            </div>
        </div>
    </footer>

    <?php if (is_en()): ?><script>window.I18N = <?= json_encode(js_translations(), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;</script><?php endif; ?>
    <script src="/assets/app.js?v=<?= filemtime(__DIR__ . '/../../public/assets/app.js') ?>"></script>
    <script>
    // PWA Service Worker & Install Prompt Flow
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js').catch(err => {
                console.warn('[LingoUz PWA] SW registration failed:', err);
            });
        });
    }

    let deferredPrompt = null;
    const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;

    // Standalone ilova rejimida ishlayotgan bo'lsa, o'rnatish tugmalarini yashiramiz
    if (isStandalone) {
        const headerBtn = document.getElementById('pwaHeaderInstallBtn');
        if (headerBtn) headerBtn.style.display = 'none';
        const drawerWrap = document.getElementById('drawerPwaWrap');
        if (drawerWrap) drawerWrap.style.display = 'none';
        const banner = document.getElementById('mobileAppBanner');
        if (banner) banner.style.display = 'none';
    }

    // Mobil banner yopilgan bo'lsa sessiyada eslab qolish
    try {
        if (sessionStorage.getItem('pwa_banner_dismissed') === '1') {
            const banner = document.getElementById('mobileAppBanner');
            if (banner) banner.style.display = 'none';
        }
    } catch (e) {}

    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
    });

    window.addEventListener('appinstalled', () => {
        deferredPrompt = null;
        const headerBtn = document.getElementById('pwaHeaderInstallBtn');
        if (headerBtn) headerBtn.style.display = 'none';
        const drawerWrap = document.getElementById('drawerPwaWrap');
        if (drawerWrap) drawerWrap.style.display = 'none';
        hidePwaModal();
    });

    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    const isInApp = /Telegram|FBAN|FBAV|Instagram|WhatsApp|Line/i.test(navigator.userAgent);

    function showPwaModal() {
        const modal = document.getElementById('pwaGuideModal');
        const backdrop = document.getElementById('pwaGuideBackdrop');
        const body = document.getElementById('pwaGuideBody');
        const title = document.getElementById('pwaModalTitle');
        if (!modal || !backdrop || !body) return;

        const isEn = document.documentElement.lang === 'en';

        if (isIOS) {
            if (title) title.textContent = isEn ? 'Install LingoUz App (iPhone / iPad)' : 'LingoUz ilovasini o‘rnatish (iPhone / iPad)';
            body.innerHTML = isEn ? `
                <div class="pwa-step-item">
                    <span class="pwa-step-num">1</span>
                    <span class="pwa-step-text">Tap the <strong>Share</strong> button <span class="pwa-step-badge">⎋ / 📤</span> at the bottom of Safari.</span>
                </div>
                <div class="pwa-step-item">
                    <span class="pwa-step-num">2</span>
                    <span class="pwa-step-text">Scroll down and select <strong>"Add to Home Screen"</strong> <span class="pwa-step-badge">➕</span>.</span>
                </div>
                <div class="pwa-step-item">
                    <span class="pwa-step-num">3</span>
                    <span class="pwa-step-text">Tap <strong>"Add"</strong> in the top right corner.</span>
                </div>
                <div class="pwa-guide-note">
                    ✨ The LingoUz app icon will appear on your home screen and work smoothly even on slow internet!
                </div>
            ` : `
                <div class="pwa-step-item">
                    <span class="pwa-step-num">1</span>
                    <span class="pwa-step-text">Safari brauzerining pastki panelidagi <strong>Ulashish ("Share")</strong> tugmasini bosing: <span class="pwa-step-badge">⎋ / 📤</span></span>
                </div>
                <div class="pwa-step-item">
                    <span class="pwa-step-num">2</span>
                    <span class="pwa-step-text">Menyuni pastga surib, <strong>"Bosh ekranga qo‘shish" (Add to Home Screen)</strong> tugmasini bosing: <span class="pwa-step-badge">➕</span></span>
                </div>
                <div class="pwa-step-item">
                    <span class="pwa-step-num">3</span>
                    <span class="pwa-step-text">Yuqori o‘ng burchakdagi <strong>"Qo‘shish" (Add)</strong> tugmasini bosing.</span>
                </div>
                <div class="pwa-guide-note">
                    ✨ LingoUz belgisi telefoningiz bosh ekranida paydo bo‘ladi va internet sekin bo‘lganda ham tez ishlaydi!
                </div>
            `;
        } else {
            if (title) title.textContent = isEn ? 'Install LingoUz App' : 'LingoUz mobil ilovasini o‘rnatish';
            let inAppWarning = '';
            if (isInApp) {
                inAppWarning = isEn
                    ? '<div class="pwa-guide-note" style="margin-top:0;margin-bottom:12px;">⚠️ Opened inside an in-app browser. Tap the <strong>3 dots (⋮)</strong> at the top right and select <strong>"Open in Chrome / Browser"</strong> first.</div>'
                    : '<div class="pwa-guide-note" style="margin-top:0;margin-bottom:12px;">⚠️ Ilova ichida ochilgan. Avval yuqori o‘ngdagi <strong>3 nuqta (⋮)</strong> ni bosib, <strong>"Brauzerda ochish" (Open in Chrome)</strong> ni tanlang.</div>';
            }
            body.innerHTML = `
                ${inAppWarning}
                <div class="pwa-step-item">
                    <span class="pwa-step-num">1</span>
                    <span class="pwa-step-text">${isEn ? 'Tap the <strong>3 dots (⋮)</strong> menu in your browser at the top right.' : 'Brauzeringizning (Chrome) yuqori o‘ng burchagidagi <strong>3 nuqta (⋮)</strong> menyusini bosing.'}</span>
                </div>
                <div class="pwa-step-item">
                    <span class="pwa-step-num">2</span>
                    <span class="pwa-step-text">${isEn ? 'Choose <strong>"Install app"</strong> or <strong>"Add to Home screen"</strong>.' : 'Menyudan <strong>"Ilovani o‘rnatish" (Установить приложение)</strong> yoki <strong>"Bosh ekranga qo‘shish"</strong> ni tanlang.'}</span>
                </div>
                <div class="pwa-step-item">
                    <span class="pwa-step-num">3</span>
                    <span class="pwa-step-text">${isEn ? 'Confirm the prompt by tapping <strong>Install</strong>.' : 'Tasdiqlash oynasida <strong>O‘rnatish</strong> tugmasini bosing.'}</span>
                </div>
            `;
        }

        backdrop.style.display = 'block';
        modal.style.display = 'block';
    }

    function hidePwaModal() {
        const modal = document.getElementById('pwaGuideModal');
        const backdrop = document.getElementById('pwaGuideBackdrop');
        if (modal) modal.style.display = 'none';
        if (backdrop) backdrop.style.display = 'none';
    }

    const triggerPwaInstall = async () => {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            try {
                const choice = await deferredPrompt.userChoice;
                if (choice && choice.outcome === 'accepted') {
                    const headerBtn = document.getElementById('pwaHeaderInstallBtn');
                    if (headerBtn) headerBtn.style.display = 'none';
                    const drawerWrap = document.getElementById('drawerPwaWrap');
                    if (drawerWrap) drawerWrap.style.display = 'none';
                    const banner = document.getElementById('mobileAppBanner');
                    if (banner) banner.style.display = 'none';
                }
            } catch (err) {}
            deferredPrompt = null;
        } else {
            showPwaModal();
        }
    };

    const pwaHBtn = document.getElementById('pwaHeaderInstallBtn');
    if (pwaHBtn) pwaHBtn.addEventListener('click', triggerPwaInstall);

    const pwaDBtn = document.getElementById('pwaDrawerBtn');
    if (pwaDBtn) pwaDBtn.addEventListener('click', triggerPwaInstall);

    const pwaBannerBtn = document.getElementById('pwaBannerInstallBtn');
    if (pwaBannerBtn) pwaBannerBtn.addEventListener('click', triggerPwaInstall);

    const pwaBannerCloseBtn = document.getElementById('pwaBannerClose');
    if (pwaBannerCloseBtn) {
        pwaBannerCloseBtn.addEventListener('click', () => {
            const banner = document.getElementById('mobileAppBanner');
            if (banner) banner.style.display = 'none';
            try { sessionStorage.setItem('pwa_banner_dismissed', '1'); } catch (e) {}
        });
    }

    const pwaCloseBtn = document.getElementById('pwaGuideClose');
    if (pwaCloseBtn) pwaCloseBtn.addEventListener('click', hidePwaModal);

    const pwaOkBtn = document.getElementById('pwaGuideOkBtn');
    if (pwaOkBtn) pwaOkBtn.addEventListener('click', hidePwaModal);

    const pwaBackdrop = document.getElementById('pwaGuideBackdrop');
    if (pwaBackdrop) pwaBackdrop.addEventListener('click', hidePwaModal);
    </script>
</body>
</html>
