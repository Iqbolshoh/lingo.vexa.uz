<div class="pg">
    <div class="container">
        <!-- Hero Header -->
        <section class="pg-hero" style="margin-bottom: 32px;">
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
        <div class="stories-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px; margin-bottom: 40px;">
            <?php foreach ($stories as $story): ?>
                <div class="card story-card" style="display: flex; flex-direction: column; justify-content: space-between; border-radius: 18px; overflow: hidden; padding: 0; box-shadow: var(--shadow-md); transition: transform 0.2s ease, box-shadow 0.2s ease;">
                    <div class="story-card-top" style="background: <?= $story['gradient'] ?>; padding: 24px; color: #ffffff; position: relative;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                            <span style="background: rgba(255,255,255,0.22); backdrop-filter: blur(6px); color: #fff; font-size: 12px; font-weight: 800; padding: 4px 10px; border-radius: 999px;">
                                <?= e(is_en() ? $story['level_en'] : $story['level']) ?>
                            </span>
                            <span style="font-size: 12px; color: rgba(255,255,255,0.9); font-weight: 600;">
                                ⏱ <?= e(is_en() ? $story['read_time_en'] : $story['read_time']) ?>
                            </span>
                        </div>
                        <h3 style="font-family: var(--font-heading); font-size: 24px; font-weight: 800; color: #ffffff; margin: 0 0 6px;">
                            <?= e(is_en() ? $story['title_en'] : $story['title_uz']) ?>
                        </h3>
                        <p style="font-size: 13.5px; opacity: 0.95; margin: 0; line-height: 1.4;">
                            <?= e(is_en() ? $story['subtitle_en'] : $story['subtitle_uz']) ?>
                        </p>
                    </div>

                    <div class="story-card-body" style="padding: 20px 24px; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                        <div style="margin-bottom: 16px;">
                            <div style="font-size: 13px; color: #64748b; margin-bottom: 8px;">
                                📖 <?= count($story['paragraphs']) ?> <?= t('ta matn qismi va audio talaffuz') ?>
                            </div>
                            <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                <span class="tag tag-gold" style="font-size: 11.5px;">🏆 <?= e($story['badge']) ?></span>
                                <span class="tag tag-emerald" style="font-size: 11.5px;">+20 XP</span>
                            </div>
                        </div>

                        <a href="/stories/<?= e($story['id']) ?>" class="btn btn-primary" style="width: 100%; justify-content: center; font-weight: 700;">
                            <?= icon('play', '', 16) ?> <?= t('Ertakni o‘qish va tinglash') ?> ↗
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
