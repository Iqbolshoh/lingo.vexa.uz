<div class="certificate-page">
    <div class="container text-center print-hide" style="margin-bottom: 24px;">
        <a href="/dashboard" class="btn btn-outline btn-sm">← O‘quvchi paneliga qaytish</a>
        <button onclick="window.print()" class="btn btn-primary btn-sm" style="margin-left: 10px;">
            🖨️ Sertifikatni Chop Etish / PDF Saqlash
        </button>
    </div>

    <div class="certificate-sheet">
        <div class="cert-outer-border">
            <div class="cert-inner-border">
                <!-- National Ornament Corners -->
                <div class="cert-corner corner-tl">⚜️</div>
                <div class="cert-corner corner-tr">⚜️</div>
                <div class="cert-corner corner-bl">⚜️</div>
                <div class="cert-corner corner-br">⚜️</div>

                <div class="cert-header">
                    <div class="cert-institution">GULOMOV’S ACADEMY • LINGOUZ</div>
                    <div class="cert-subtitle">AQSHDAGI O‘ZBEK TILI ONLAYN AKADEMIYASI</div>
                </div>

                <div class="cert-title-wrap">
                    <h1 class="cert-main-title">DIPLOM</h1>
                    <div class="cert-award-tag">O‘ZBEK TILI BILIMDONI 🌟</div>
                </div>

                <div class="cert-body">
                    <p class="cert-presentation">Ushbu diplom faxr bilan topshiriladi:</p>
                    <div class="cert-student-name"><?= e($student['display_name']) ?></div>
                    <p class="cert-description">
                        AQShda yashab, ona tili — o‘zbek tilini mehr bilan o‘rgangani, 5 ta asosiy mikro-darsni, interaktiv sinovlarni hamda 80% testlarini muvaffaqiyatli topshirib, <strong>A0–A1 Boshlang‘ich Darajasi</strong>ni a’lo baholar bilan tamomlagani uchun!
                    </p>
                </div>

                <div class="cert-footer-row">
                    <div class="cert-col cert-date">
                        <div class="cert-line"><?= date('d.m.Y') ?></div>
                        <span class="cert-label">Sana / Date</span>
                    </div>

                    <div class="cert-col cert-seal-col">
                        <div class="cert-gold-seal">
                            <span class="seal-icon">🇺🇿</span>
                            <span class="seal-text">LINGOUZ<br>RASMIY MUHR</span>
                        </div>
                    </div>

                    <div class="cert-col cert-sign">
                        <div class="cert-signature-img">Gulomov</div>
                        <div class="cert-line">Gulomov Ustoz</div>
                        <span class="cert-label">Akademiya Rahbari</span>
                    </div>
                </div>

                <div class="cert-code">Sertifikat ID: LU-<?= date('Y') ?>-<?= str_pad((string)$student['id'], 4, '0', STR_PAD_LEFT) ?></div>
            </div>
        </div>
    </div>
</div>

<style>
.certificate-sheet {
    max-width: 880px;
    margin: 0 auto;
    background: #ffffff;
    padding: 24px;
    box-shadow: var(--shadow-lg);
    border-radius: 12px;
}
.cert-outer-border {
    border: 6px double #059669;
    padding: 12px;
    background: #fffdfa;
}
.cert-inner-border {
    border: 2px solid #d97706;
    padding: 40px 30px;
    position: relative;
    text-align: center;
}
.cert-corner {
    position: absolute;
    font-size: 22px;
    color: #d97706;
}
.corner-tl { top: 8px; left: 8px; }
.corner-tr { top: 8px; right: 8px; }
.corner-bl { bottom: 8px; left: 8px; }
.corner-br { bottom: 8px; right: 8px; }

.cert-institution {
    font-family: var(--font-heading);
    font-size: 16px;
    font-weight: 700;
    letter-spacing: 2px;
    color: #0f172a;
}
.cert-subtitle {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 1px;
    color: #059669;
    margin-top: 2px;
}
.cert-main-title {
    font-family: var(--font-heading);
    font-size: 52px;
    font-weight: 800;
    letter-spacing: 6px;
    color: #b45309;
    margin: 16px 0 6px;
}
.cert-award-tag {
    display: inline-block;
    background: #fef3c7;
    color: #92400e;
    font-weight: 800;
    font-size: 14px;
    padding: 6px 20px;
    border-radius: 999px;
    border: 1px solid #fde68a;
    letter-spacing: 1px;
}
.cert-presentation {
    font-size: 15px;
    color: #64748b;
    margin-top: 24px;
}
.cert-student-name {
    font-family: var(--font-heading);
    font-size: 38px;
    font-weight: 700;
    color: #0f172a;
    border-bottom: 2px solid #059669;
    display: inline-block;
    padding: 6px 36px;
    margin: 8px 0 16px;
}
.cert-description {
    font-size: 15px;
    color: #334155;
    max-width: 620px;
    margin: 0 auto 36px;
    line-height: 1.6;
}
.cert-footer-row {
    display: flex;
    justify-content: space-around;
    align-items: flex-end;
    margin-top: 20px;
}
.cert-line {
    border-top: 1px solid #94a3b8;
    padding-top: 4px;
    font-weight: 700;
    font-size: 14px;
    min-width: 140px;
}
.cert-label {
    font-size: 11px;
    color: #64748b;
}
.cert-gold-seal {
    width: 84px;
    height: 84px;
    border-radius: 50%;
    background: radial-gradient(circle, #fde047 0%, #d97706 100%);
    box-shadow: 0 4px 10px rgba(217, 119, 6, 0.4);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #78350f;
    border: 3px dashed #ffffff;
}
.seal-icon {
    font-size: 20px;
}
.seal-text {
    font-size: 8.5px;
    font-weight: 800;
    line-height: 1.1;
    text-align: center;
}
.cert-signature-img {
    font-family: cursive;
    font-size: 22px;
    color: #0369a1;
    margin-bottom: -6px;
}
.cert-code {
    margin-top: 28px;
    font-size: 11px;
    color: #94a3b8;
    font-family: monospace;
}

@media print {
    body { background: #ffffff; }
    .site-header, .site-footer, .print-hide { display: none !important; }
    .certificate-sheet { box-shadow: none; border: none; padding: 0; }
}
</style>
