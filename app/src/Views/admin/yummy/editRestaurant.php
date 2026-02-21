<?php require __DIR__ . '/../../partials/adminHeader.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex align-items-center mb-4">
            <a href="/admin/yummy" class="btn btn-outline-secondary me-3">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h2 class="mb-0">Edit Restaurant</h2>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Updating: <?= htmlspecialchars($restaurant->getName()) ?></h5>
            </div>
            
            <div class="card-body p-4">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form action="/admin/yummy/edit/<?= $restaurant->getId() ?>" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $restaurant->getId() ?>">

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Restaurant Name</label>
                            <input type="text" name="name" class="form-control" 
                                   value="<?= htmlspecialchars($restaurant->getName()) ?>" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Cuisine Type</label>
                            <input type="text" name="cuisine" class="form-control" 
                                   value="<?= htmlspecialchars($restaurant->getCuisine() ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Location</label>
                            <input type="text" name="location" class="form-control" 
                                   value="<?= htmlspecialchars($restaurant->getLocation() ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($restaurant->getDescription() ?? '') ?></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Restaurant Image</label>
                            <div class="mb-2">
                                <?php if ($restaurant->getImageUrl()): ?>
                                    <img src="<?= htmlspecialchars($restaurant->getImageUrl()) ?>" alt="Restaurant" class="img-thumbnail" style="height: 60px;">
                                <?php else: ?>
                                    <span class="text-muted small">No image set</span>
                                <?php endif; ?>
                            </div>
                            <input type="file" name="image_file" class="form-control" accept="image/*">
                            <div class="form-text">Choose a file to replace the current image.</div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="/admin/yummy" class="btn btn-light px-4">Cancel</a>
                        <button type="submit" class="btn btn-warning px-4">Update Restaurant</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../partials/adminFooter.php'; ?>