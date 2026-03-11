<?php require __DIR__ . '/../../partials/adminHeader.php'; ?>

<div class="container-fluid py-4">
    <div class="row justify-content-center mb-4">
        <div class="col-md-10">
            <div class="d-flex align-items-center">
                <a href="/admin/yummy" class="btn btn-outline-secondary me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h2 class="mb-0">Add New Restaurant</h2>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10">
            <?php include __DIR__ . '/partials/_alerts.php'; ?>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10">
            <?php include __DIR__ . '/partials/_restaurantForm.php'; ?>
        </div>
    </div>

    <?php if (isset($restaurant) && $restaurant->getId()): ?>
        <div class="row justify-content-center mt-5">
            <div class="col-md-10">
                <?php include __DIR__ . '/partials/_sessionTable.php'; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="row justify-content-center mt-4">
            <div class="col-md-10">
                <div class="alert alert-info border-0 shadow-sm">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Note:</strong> You will be able to manage time slots and daily schedules after the restaurant is created.
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php if (isset($restaurant) && $restaurant->getId()): ?>
    <?php include __DIR__ . '/partials/_sessionModal.php'; ?>
<?php endif; ?>

<?php require __DIR__ . '/../../partials/adminFooter.php'; ?>