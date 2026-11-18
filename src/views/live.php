<div class="live-page">
    <div class="container">
        <div class="live-header-banner">
            <span class="live-pill">JONLI EFIR VA NUTQIY AMALIYOT 🎙️</span>
            <h1>Zoom Live Speaking Room</h1>
            <p>AQShdagi tengdoshlar bilan har haftada 2 marta o‘zbek tilida jonli muloqot qilish xonasi.</p>
        </div>

        <div class="live-grid">
            <div class="live-main-card">
                <?php foreach ($sessions as $s): ?>
                    <div class="session-box">
                        <div class="session-status-badge">
                            <span class="pulse-dot red"></span>
                            Rejalashtirilgan sessiya
                        </div>

                        <h2><?= e($s['title']) ?></h2>
                        <p class="session-desc"><?= e($s['description']) ?></p>

                        <div class="session-time-grid">
                            <div class="time-block">
                                <span class="time-zone">🇺🇸 Eastern Time (NY, FL)</span>
                                <span class="time-val">11:00 AM</span>
                            </div>
                            <div class="time-block">
                                <span class="time-zone">🇺🇸 Central Time (Chicago, TX)</span>
                                <span class="time-val">10:00 AM</span>
                            </div>
                            <div class="time-block">
                                <span class="time-zone">🇺🇸 Mountain Time (Denver)</span>
                                <span class="time-val">09:00 AM</span>
                            </div>
                            <div class="time-block">
                                <span class="time-zone">🇺🇸 Pacific Time (LA, Seattle)</span>
                                <span class="time-val">08:00 AM</span>
                            </div>
                        </div>

                        <div class="session-host-info">
                            <span>👨‍🏫 Mas’ul Mentor: <strong><?= e($s['mentor_name']) ?></strong></span>
                        </div>

                        <div class="session-action">
                            <a href="<?= e($s['zoom_url']) ?>" target="_blank" rel="noopener" class="btn btn-primary btn-xl pulse-hover">
                                🚀 Zoom Xonasiga Ulanish
                            </a>
                            <span class="zoom-note">Parol va kirish huquqi faqat ro‘yxatdan o‘tgan oilalarga beriladi.</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="live-rules-card">
                <h3>🛡️ Bolalar Xavfsizligi Qoidalari</h3>
                <ul class="rules-list">
                    <li>
                        <strong>Kutish xonasi (Waiting Room):</strong> Har bir bola mentor tomonidan tekshirilib, xonaga qabul qilinadi.
                    </li>
                    <li>
                        <strong>Ota-onalar nazorati:</strong> 7–11 yoshli bolalar darsida ota-onalarning birga o‘tirishi tavsiya etiladi.
                    </li>
                    <li>
                        <strong>Ochiq chat yo‘qligi:</strong> Bolalar o‘rtasida ochiq shaxsiy xabar yozish imkoniyati xavfsizlik uchun yopilgan.
                    </li>
                    <li>
                        <strong>Yozib olish siyosati:</strong> Darslar ota-onalarning yozma roziligisiz yozib olinmaydi va ommaga tarqatilmaydi.
                    </li>
                </ul>

                <div class="badge-speaker-earned">
                    🎉 <em>Ushbu xonaga kirganingiz uchun profilingizga <strong>«Jonli Muloqotchi» 🗣️</strong> nishoni qo‘shildi!</em>
                </div>
            </div>
        </div>
    </div>
</div>
