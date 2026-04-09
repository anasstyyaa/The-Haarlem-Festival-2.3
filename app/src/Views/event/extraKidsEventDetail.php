<?php require __DIR__ . '/../partials/header.php'; ?>

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