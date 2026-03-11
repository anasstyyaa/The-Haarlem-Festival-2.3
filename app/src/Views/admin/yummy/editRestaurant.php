<?php require __DIR__ . '/../../partials/adminHeader.php'; ?>

<div class="container-fluid py-4">
    <div class="row justify-content-center mb-4">
        <div class="col-md-10">
            <div class="d-flex align-items-center">
                <a href="/admin/yummy" class="btn btn-outline-secondary me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h2 class="mb-0">Edit: <?= htmlspecialchars($restaurant->getName()) ?></h2>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10">
            <?php include __DIR__ . '/partials/_alerts.php'; ?>
        </div>
    </div>

    <div class="row justify-content-center mb-5">
        <div class="col-md-10">
            <?php include __DIR__ . '/partials/_restaurantForm.php'; ?>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10">
            <?php include __DIR__ . '/partials/_sessionTable.php'; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/partials/_sessionModal.php'; ?>

<?php require __DIR__ . '/../../partials/adminFooter.php'; ?>

<script src="/js/yummy/addTimeSlot.js"></script>