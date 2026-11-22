<div class="pg">
    <div class="container">
        <!-- Hero Header -->
        <section class="pg-hero theme-teal" style="margin-bottom: 32px;">
            <div class="hero-split">
                <div>
                    <span class="hero-chip"><?= icon('book-open', '', 14) ?> <?= t('O‘zbek Xalq Ertaklari') ?></span>
                    <h1><?= t('Ovozli Ertaklar va Madaniy Hikoyalar') ?></h1>
                    <p>
                        <?= t('O‘zbek xalqining boy madaniyati, donoligi va qadriyatlarini aks ettiruvchi ertaklar. Har bir gapni ovozli eshiting, yangi so‘zlarni bosing va yakunda test orqali +20 XP ball to‘plang!') ?>
                    </p>
                    <div style="margin-top: 18px; display: flex; gap: 12px; flex-wrap: wrap;">
                        <span class="tag tag-emerald">🎧 <?= t('Gapma-gap audio o‘qish') ?></span>
                        <span class="tag tag-gold">✨ <?= t('Bosiluvchi lug‘at') ?></span>
                        <span class="tag tag-lapis">🎯 <?= t('Viktorina va XP mukofoti') ?></span>
                    </div>
                </div>
                <div class="hero-owl-ring">
                    <?= mascot('learning', 110) ?>
                </div>
            </div>
        </section>

        <!-- Stories Grid -->
        <div class="stories-grid">
            <?php foreach ($stories as $story): ?>
                <div class="card story-card">
                    <div class="story-card-top" style="background: <?= $story['gradient'] ?>;">
                        <div class="story-badge-bar">
                            <span class="story-level-pill">
                                <?= e(is_en() ? $story['level_en'] : $story['level']) ?>
                            </span>
                            <span class="story-read-time">
                                ⏱ <?= e(is_en() ? $story['read_time_en'] : $story['read_time']) ?>
                            </span>
                        </div>
                        <h3 class="story-title">
                            <?= e(is_en() ? $story['title_en'] : $story['title_uz']) ?>
                        </h3>
                        <p class="story-subtitle">
                            <?= e(is_en() ? $story['subtitle_en'] : $story['subtitle_uz']) ?>
                        </p>
                    </div>

                    <div class="story-card-body">
                        <div class="story-meta-box">
                            <div class="story-meta-text">
                                📖 <?= count($story['paragraphs']) ?> <?= t('ta matn qismi va audio talaffuz') ?>
                            </div>
                            <div class="story-tags-row">
                                <span class="tag tag-gold">🏆 <?= e($story['badge']) ?></span>
                                <span class="tag tag-emerald">+20 XP</span>
                            </div>
                        </div>

                        <a href="/stories/<?= e($story['id']) ?>" class="btn btn-primary btn-block" style="justify-content: center; font-weight: 700;">
                            <?= icon('play', '', 16) ?> <?= t('Ertakni o‘qish va tinglash') ?> ↗
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
