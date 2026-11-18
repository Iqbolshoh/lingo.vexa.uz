<div class="result-page">
    <div class="container">
        <div class="result-card <?= $result['passed'] ? 'passed-card' : 'failed-card' ?>">
            <div class="result-badge-icon">
                <?= $result['passed'] ? '🎉' : '🌱' ?>
            </div>

            <h1 class="result-title">
                <?= $result['passed'] ? 'Tabriklaymiz, ajoyib natija! 🏆' : 'Yaxshi harakat! Qaytadan urinib ko‘ring 🌟' ?>
            </h1>

            <div class="result-score-circle">
                <span class="score-percent"><?= $result['percent'] ?>%</span>
                <span class="score-fraction"><?= $result['score'] ?> / <?= $result['total'] ?> to‘g‘ri javob</span>
            </div>

            <div class="result-message-box">
                <?php if ($result['passed']): ?>
                    <p class="pass-text">
                        Siz 80% qoidasini bajardingiz! <strong><?= e($lesson['title']) ?></strong> mavzusi to‘liq o‘zlashtirildi.
                        <?php if ($nextLessonId): ?>
                            <strong><?= $nextLessonId ?>-dars muvaffaqiyatli ochildi! 🔓</strong>
                        <?php else: ?>
                            <strong>Barcha 5 ta MVP darslari tugatildi! Siz haqiqiy chempionsiz! 👑</strong>
                        <?php endif; ?>
                    </p>
                <?php else: ?>
                    <p class="fail-text">
                        Keyingi dars ochilishi uchun kamida <strong>80%</strong> to‘plashingiz kerak. Xafa bo‘lmang, dars so‘zlarini yana bir bor eshitib ko‘rib, qaytadan topshirishingiz mumkin!
                    </p>
                <?php endif; ?>
            </div>

            <div class="result-actions-row">
                <?php if ($result['passed']): ?>
                    <?php if ($nextLessonId): ?>
                        <a href="/lessons/<?= $nextLessonId ?>" class="btn btn-primary btn-lg pulse-hover">
                            Keyingi darsga o‘tish (<?= $nextLessonId ?>-Dars) →
                        </a>
                    <?php endif; ?>
                    <a href="/dashboard" class="btn btn-outline btn-lg">
                        🎒 O‘quvchi paneliga qaytish
                    </a>
                <?php else: ?>
                    <a href="/lessons/<?= $lesson['id'] ?>" class="btn btn-primary btn-lg">
                        🔄 Darsni qayta ko‘rish va qayta topshirish
                    </a>
                    <a href="/dashboard" class="btn btn-outline btn-lg">
                        🎒 O‘quvchi paneliga qaytish
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($result['details'])): ?>
            <div class="review-section">
                <h2>📋 Savollarni birgalikda tekshiramiz:</h2>
                <div class="review-list">
                    <?php foreach ($result['details'] as $idx => $d): ?>
                        <div class="review-item <?= $d['is_correct'] ? 'review-correct' : 'review-incorrect' ?>">
                            <div class="review-item-header">
                                <span class="review-icon"><?= $d['is_correct'] ? '✅ To‘g‘ri' : '❌ Noto‘g‘ri' ?></span>
                                <h4><?= $idx + 1 ?>. <?= e($d['prompt']) ?></h4>
                            </div>

                            <div class="review-answers">
                                <div>Sizning javobingiz: <strong><?= e($d['chosen']) ?></strong></div>
                                <?php if (!$d['is_correct']): ?>
                                    <div class="correct-answer-text">To‘g‘ri javob: <strong><?= e($d['correct']) ?></strong></div>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($d['explanation'])): ?>
                                <div class="review-exp">
                                    💡 <em><?= e($d['explanation']) ?></em>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
