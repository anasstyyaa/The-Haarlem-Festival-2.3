<div class="section<?= htmlspecialchars($section) ?>">
    <?php foreach ($elements as $element): ?>
        <?= $element->render(); ?>
    <?php endforeach; ?>
</div>