<div class="container adm">
    <?php require __DIR__ . '/_side.php'; ?>
    <main class="adm-main">
        <div class="adm-head"><div><div class="crumbs"><a href="/admin">Admin</a> / Sozlamalar</div><h1>Sozlamalar</h1></div></div>
        <div class="grid-2" style="align-items:start">
            <form method="POST" action="/admin/settings/password" class="card">
                <?= csrf_field() ?>
                <h3 class="card-title"><?= icon('lock', '', 20) ?> Parolni almaştiriş</h3>
                <div class="adm-form" style="grid-template-columns:1fr">
                    <div class="field"><label>Joriy parol</label><input type="password" name="current_password" required autocomplete="current-password"></div>
                    <div class="field"><label>Yangi parol</label><input type="password" name="new_password" required minlength="8" autocomplete="new-password"><div class="hint">Kamida 8 belgi. Demo parolni (admin123) albatta almaştiring.</div></div>
                </div>
                <div class="form-foot"><button class="btn btn-primary">Saqlaş</button></div>
            </form>
            <div class="stack-sm">
                <div class="tbl-wrap">
                    <table class="tbl">
                        <thead><tr><th class="col-index">#</th><th>Administrator</th><th>Qöşilgan</th></tr></thead>
                        <tbody>
                        <?php foreach ($admins as $i => $a): ?>
                            <tr><td class="col-index"><?= $i + 1 ?></td><td><span class="t-title"><?= e($a['display_name']) ?></span><?= (int) $a['id'] === (int) $me['id'] ? ' <span class="tag tag-emerald">siz</span>' : '' ?><div class="t-sub"><?= e($a['email']) ?></div></td><td class="t-sub"><?= e(substr((string) $a['created_at'], 0, 10)) ?></td></tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <form method="POST" action="/admin/settings/admins" class="card">
                    <?= csrf_field() ?>
                    <h3 class="card-title"><?= icon('shield-check', '', 20) ?> Yangi administrator / ustoz</h3>
                    <div class="adm-form" style="grid-template-columns:1fr">
                        <div class="field"><label>Ism</label><input type="text" name="display_name" required maxlength="80"></div>
                        <div class="field"><label>Email</label><input type="email" name="email" required maxlength="160"></div>
                        <div class="field"><label>Parol</label><input type="text" name="password" required minlength="8" autocomplete="off"></div>
                    </div>
                    <div class="form-foot"><button class="btn btn-primary"><?= icon('check-circle', '', 16) ?> Qöşiş</button></div>
                </form>
            </div>
        </div>

        <div style="margin-top:24px">
            <div class="card">
                <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
                    <div>
                        <h3 class="card-title" style="margin:0">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" style="vertical-align:text-bottom"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.75-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z" fill="#0284c7"/></svg>
                            Telegram Bot Sozlamalari (Kunlik söz & Streak eslatmasi)
                        </h3>
                        <div class="hint" style="margin-top:4px">O‘quvchilarga har kuni 1 ta yangi so‘z, mini-viktorina va streak eslatmalarini yuboruvchi rasmiy bot.</div>
                    </div>
                    <div style="display:flex;gap:8px;flex-wrap:wrap">
                        <span class="tag tag-emerald">Ulangan o‘quvchilar: <b><?= (int) ($tgLinkedCount ?? 0) ?></b> ta</span>
                        <span class="tag tag-gold">Yuborilgan xabarlar: <b><?= (int) ($tgLogsCount ?? 0) ?></b> ta</span>
                    </div>
                </div>

                <form method="POST" action="/admin/settings/telegram" class="adm-form" style="grid-template-columns:1fr 1fr;gap:16px;">
                    <?= csrf_field() ?>
                    <div class="field">
                        <label>Bot Token (BotFather bergan token)</label>
                        <input type="text" name="telegram_bot_token" value="<?= e($tgToken ?? '') ?>" placeholder="123456789:ABCdefGhIJKlmNoPQRsTUVwxyZ" autocomplete="off">
                        <div class="hint">Tokenni kiritib "Saqlash" tugmasini bosing. Bo‘sh bo‘lsa .env dagi qiymat olinadi.</div>
                    </div>
                    <div class="field">
                        <label>Bot Username</label>
                        <input type="text" name="telegram_bot_username" value="<?= e($tgUsername ?? 'LingoUzBot') ?>" placeholder="LingoUzBot" autocomplete="off">
                        <div class="hint">Foydalanuvchilar bir bosishda ulanishi uchun bot username'i (masalan: LingoUzBot).</div>
                    </div>
                    <div class="field" style="grid-column:1 / -1">
                        <label>Webhook URL (Telegramdan xabarlarni qabul qilish manzili)</label>
                        <input type="text" readonly value="https://lingo.vexa.uz/api/telegram/webhook" style="background:#f8fafc;color:#64748b;font-family:monospace">
                    </div>
                    <div style="grid-column:1 / -1;display:flex;gap:12px;align-items:center;flex-wrap:wrap;padding-top:10px;border-top:1px solid #f1f5f9;">
                        <button type="submit" class="btn btn-primary"><?= icon('check-circle', '', 16) ?> Sozlamalarni saqlash</button>
                    </div>
                </form>

                <div style="margin-top:18px;padding-top:16px;border-top:1px dashed #e2e8f0;display:flex;gap:12px;flex-wrap:wrap;align-items:center;">
                    <form method="POST" action="/admin/settings/telegram/webhook" style="display:inline">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-glass" style="border:1px solid #cbd5e1" title="Telegram API ga ushbu sayt webhook manzilini ro‘yxatdan o‘tkazadi">
                            ⚡ Webhookni o‘rnatish (Set Webhook)
                        </button>
                    </form>
                    <form method="POST" action="/admin/settings/telegram/broadcast" style="display:inline" onsubmit="return confirm('Barcha ulangan obunachilarga hozir kunlik so‘z va test yuborilsinmi?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-ghost" style="border:1px solid #e2e8f0" title="Barcha ulangan foydalanuvchilarga sinov xabarini yuboradi">
                            🚀 Kunlik xabarni sinab ko‘rish (Test Broadcast)
                        </button>
                    </form>
                    <div class="hint">Eslatma: Kunlik avtomatik yuborish uchun cron: <code>php /var/www/startups/lingo/artisan lingo:telegram-daily</code></div>
                </div>
            </div>
        </div>
    </main>
</div>
