<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/telegram.php';

$title = $argv[1] ?? 'Tizim yangilandi va yangi funksiyalar qo‘shildi';
$ver = $argv[2] ?? 'v1.2.6';

// Read last git commit info if available
$commitHash = null;
$commitMsg = null;
try {
    $commitHash = trim((string) shell_exec('cd /var/www/startups/lingo && git log -1 --format="%h" 2>/dev/null'));
    $commitMsg = trim((string) shell_exec('cd /var/www/startups/lingo && git log -1 --format="%s" 2>/dev/null'));
} catch (\Throwable $e) {}

$changes = [];
if ($commitMsg) {
    $changes[] = $commitMsg;
}
$changes[] = 'Telegram bot menyu tugmalari va til sozlamalari yangilandi';
$changes[] = 'Dasturchilar guruhi bildirishnomalari (yangi o‘quvchilar, arizalar, relizlar)';
$changes[] = 'Interaktiv ovoz effektlari va vizual animatsiyalar to‘liq sozlandi';

echo "Guruhga yangilanish yuborilmoqda: {$title}...\n";
$ok = telegram_notify_release($title, $ver, $changes, 'Antigravity / Lead Developer', $commitHash);

if ($ok) {
    echo "✅ Muvaffaqiyatli yetkazildi!\n";
} else {
    echo "❌ Xatolik yuz berdi.\n";
    exit(1);
}
