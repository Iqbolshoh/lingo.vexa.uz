<div class="pg">
    <div class="container">
        <section class="pg-hero">
            <span class="hero-chip"><?= icon('layers', '', 14) ?> Kurslar katalogi</span>
            <h1>Bilim yo‘llari</h1>
            <p>Til kursida so‘z va gaplarni o‘rganamiz, madaniyat kursida esa osh, non, Navro‘z va qadimiy shaharlar bilan tanishamiz. Har bir kurs oxirida — chiroyli diplom!</p>
        </section>
        <h2 class="section-h"><?= icon('book-open', '', 24) ?> Barcha kurslar</h2>
        <div class="course-grid">
            <?php foreach ($courseStates as $cs): $c = $cs['course']; ?>
                <?php require __DIR__ . '/partials/course_card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>
