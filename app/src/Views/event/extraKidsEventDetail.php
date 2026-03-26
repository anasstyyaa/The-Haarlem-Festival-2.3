<?php require __DIR__ . '/../partials/header.php'; ?>
<style>
    
/* ===== DETAIL PAGE ===== */
.kids-detail {
    max-width: 900px;
    margin: 60px auto;
    padding: 20px;
}

.kids-detail h1 {
    font-size: 2.5rem;
    margin-bottom: 20px;
}

/* BACK BUTTON */
.kids-detail a {
    display: inline-block;
    margin-bottom: 20px;
    text-decoration: none;
    color: #555;
}

.kids-detail a:hover {
    text-decoration: underline;
}

/* IMAGE */
.detail-img {
    width: 100%;
    max-height: 400px;
    object-fit: cover;
    border-radius: 12px;
    margin-bottom: 25px;
}

/* DESCRIPTION */
.detail-description {
    font-size: 1.1rem;
    line-height: 1.7;
    color: #333;
}
</style>
<section class="kids-detail">

    <a href="/kidsEvent">← Back</a>

    <h1><?= htmlspecialchars($event->getName()) ?></h1>

    <?php if ($event->getImageUrl()): ?>
        <img src="<?= htmlspecialchars($event->getImageUrl()) ?>" class="detail-img">
    <?php endif; ?>

    <div class="detail-description">
        <?= html_entity_decode($event->getDescription() ?? '') ?>
    </div>

</section>

<?php require __DIR__ . '/../partials/footer.php'; ?>