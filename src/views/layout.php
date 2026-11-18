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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/style.css?v=<?= time() ?>">
</head>
<body>
    <header class="site-header">
        <div class="container header-inner">
            <a href="/" class="brand-logo">
                <span class="logo-mark"><img src="/assets/mascot-zukko.svg" alt="Zukko Mascot" width="38" height="38" style="vertical-align:middle; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.15));"></span>
                <span class="logo-text">
                    <span class="brand-name">Lingo<span class="highlight">Uz</span></span>
                    <span class="brand-sub">by Gulomov’s Academy</span>
                </span>
            </a>

            <nav class="main-nav" id="mainNav">
                <a href="/">Bosh sahifa</a>
                <a href="/#metodika">Qanday ishlaydi?</a>
                <a href="/#darslar">Darslar</a>
                <a href="/parent-tips">📖 Ota-onalarga</a>
                <a href="/#ariza" class="nav-highlight">Pilotga ariza</a>
                <?php if ($user): ?>
                    <a href="/dashboard" class="nav-pill">🎒 O‘quvchi Paneli</a>
                    <a href="/live">🎙️ Jonli Muloqot</a>
                    <a href="/certificate" class="nav-cert">🎓 Diplom</a>
                    <?php if (is_admin()): ?>
                        <a href="/admin" class="nav-admin">⚙️ Admin</a>
                    <?php endif; ?>
                <?php endif; ?>
            </nav>

            <div class="header-actions">
                <?php if ($user): ?>
                    <div class="user-chip">
                        <span class="user-avatar"><?= $user['role'] === 'admin' ? '👑' : '👦' ?></span>
                        <span class="user-name"><?= e($user['display_name']) ?></span>
                        <form action="/logout" method="POST" style="display:inline; margin-left:8px;">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn-logout" title="Chiqish">✕</button>
                        </form>
                    </div>
                <?php else: ?>
                    <a href="/login" class="btn btn-outline btn-sm">Kirish</a>
                    <a href="/register" class="btn btn-primary btn-sm">Ro‘yxatdan o‘tish</a>
                <?php endif; ?>
                <button class="menu-toggle" id="menuToggle" aria-label="Menyu">☰</button>
            </div>
        </div>
    </header>

    <?php if (has_flash('success') || has_flash('error') || has_flash('info')): ?>
        <div class="container flash-container">
            <?php if ($msg = get_flash('success')): ?>
                <div class="flash-alert flash-success">
                    <span class="flash-icon">🎉</span>
                    <span><?= e($msg) ?></span>
                </div>
            <?php endif; ?>
            <?php if ($msg = get_flash('error')): ?>
                <div class="flash-alert flash-error">
                    <span class="flash-icon">⚠️</span>
                    <span><?= e($msg) ?></span>
                </div>
            <?php endif; ?>
            <?php if ($msg = get_flash('info')): ?>
                <div class="flash-alert flash-info">
                    <span class="flash-icon">ℹ️</span>
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
                    <span class="logo-mark">🇺🇿</span>
                    <span class="brand-name">Lingo<span class="highlight">Uz</span></span>
                </div>
                <p class="footer-desc">
                    Gulomov’s Academy tashabbusi bilan AQShda yashovchi 7–11 yoshli o‘zbek bolalari uchun yaratilgan zamonaviy, interaktiv o‘zbek tili akademiyasi.
                </p>
                <div class="badge-safe">🛡️ Bolalar xavfsizligi va COPPA standartlariga mos</div>
            </div>

            <div class="footer-col">
                <h4>Tezkor havolalar</h4>
                <ul>
                    <li><a href="/">Bosh sahifa</a></li>
                    <li><a href="/#metodika">Metodika va qoidalar</a></li>
                    <li><a href="/#darslar">5 ta MVP darslari</a></li>
                    <li><a href="/#ariza">Bepul pilotga ariza</a></li>
                    <li><a href="/live">Zoom Speaking Room</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Xavfsizlik va Shartlar</h4>
                <ul>
                    <li><a href="/privacy">Maxfiylik siyosati (COPPA)</a></li>
                    <li><a href="/#faq">Ota-onalar uchun FAQ</a></li>
                    <li><a href="/login?as=admin">Admin Demo</a></li>
                    <li><a href="/login?as=student">O‘quvchi Demo (Alijon)</a></li>
                </ul>
            </div>

            <div class="footer-col contact-col">
                <h4>Aloqa va Pilot</h4>
                <p>📍 Toshkent — Nyu-York</p>
                <p>📧 gulomov@vexa.uz</p>
                <p>💬 Telegram: @gulomovs_academy</p>
                <div class="timezones-pill">🇺🇸 EST • CST • MST • PST</div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="container footer-bottom-inner">
                <p>© 2026 LingoUz by Gulomov’s Academy. Barcha huquqlar himoyalangan.</p>
                <p class="tech-tag">Laravel + SQLite | Versiya 1.0 MVP</p>
            </div>
        </div>
    </footer>

    <script src="/assets/app.js?v=<?= time() ?>"></script>
</body>
</html>
