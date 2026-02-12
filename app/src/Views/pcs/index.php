<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../components/card_start.php'; ?>

<h1 class="mb-4">Available PCs</h1>

<ul class="list-group list-group-flush">
    <?php foreach ($pcs as $pc): ?>
        <li class="list-group-item bg-transparent text-white border-0 border-bottom" style="border-color: rgba(255,255,255,.08) !important;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <strong><?= htmlspecialchars($pc->name) ?></strong><br>
                    <span class="text-muted"><?= htmlspecialchars($pc->specs) ?></span>
                </div>

                <div>
                    <?php if (!empty($_SESSION['user_id'])): ?>
                        <a class="btn btn-primary btn-sm"
                           href="/reservations/create/<?= (int)$pc->id ?>">
                            Book this PC
                        </a>
                    <?php else: ?>
                        <span class="text-muted">Login to book</span>
                    <?php endif; ?>
                </div>
            </div>
        </li>
    <?php endforeach; ?>
</ul>

<?php require __DIR__ . '/../components/card_end.php'; ?>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
