<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../components/card_start.php'; ?>

<h1 class="mb-2">Book <?= htmlspecialchars($pc->name) ?></h1>

<p class="text-muted mb-4">
    <?= htmlspecialchars($pc->specs) ?>
</p>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" action="/reservations/create" class="mt-3">
    <input type="hidden" name="pc_id" value="<?= (int)$pc->id ?>">

    <div class="mb-3">
        <label for="start_time" class="form-label">Start time</label>
        <input
            type="datetime-local"
            name="start_time"
            id="start_time"
            class="form-control"
            required
        >
    </div>

    <div class="mb-3">
        <label for="end_time" class="form-label">End time</label>
        <input
            type="datetime-local"
            name="end_time"
            id="end_time"
            class="form-control"
            required
        >
    </div>

    <div class="mt-3">
        <strong>Total price:</strong>
        <span id="totalPrice">€0.00</span>
    </div>

    <script>
        window.PRICE_PER_HOUR = <?= json_encode((float)$pc->price_per_hour) ?>;
    </script>
    <script src="/js/reservation.js"></script>

    <button type="submit" class="btn btn-primary mt-3">Confirm reservation</button>
    <a href="/pcs" class="btn btn-secondary ms-2 mt-3">Cancel</a>
</form>

<?php require __DIR__ . '/../components/card_end.php'; ?>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
