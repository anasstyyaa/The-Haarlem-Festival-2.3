<?php 
    $isEdit = false;
    try {
        if (isset($restaurant) && $restaurant->getId() > 0) {
            $isEdit = true;
        }
    } catch (Error $e) {
        $isEdit = false;
    }

    $actionUrl = $isEdit ? "/admin/yummy/edit/" . $restaurant->getId() : "/admin/yummy/create";
?>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0"><?= $isEdit ? 'Update Restaurant Details' : 'New Restaurant Details' ?></h5>
    </div>
    <div class="card-body p-4">
        <form action="<?= $actionUrl ?>" method="POST" enctype="multipart/form-data">
            
            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label fw-bold">Restaurant Name</label>
                    <input type="text" name="name" class="form-control" 
                           placeholder="e.g. Ratatouille" 
                           value="<?= htmlspecialchars($restaurant->getName() ?? '') ?>" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Cuisine Type</label>
                    <input type="text" name="cuisine" class="form-control" 
                           placeholder="e.g. French, Seafood"
                           value="<?= htmlspecialchars($restaurant->getCuisine() ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Location</label>
                    <input type="text" name="location" class="form-control" 
                           placeholder="e.g. Grote Markt 10"
                           value="<?= htmlspecialchars($restaurant->getLocation() ?? '') ?>">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Description</label>
                <textarea name="description" class="form-control" rows="4" 
                          placeholder="Brief description..."><?= htmlspecialchars($restaurant->getDescription() ?? '') ?></textarea>
            </div>

            <div class="row mb-4">
                <div class="col-md-12">
                    <label class="form-label fw-bold">Detailed Page Content (WYSIWYG)</label>
                    <textarea name="long_description" class="form-control wysiwyg-editor" 
                              rows="12"><?= $restaurant->getLongDescription() ?? '' ?></textarea>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label fw-bold">Restaurant Image</label>
                    <input type="file" name="image_file" class="form-control" accept="image/*">
                    <?php if ($isEdit && $restaurant->getImageUrl()): ?>
                        <div class="mt-2">
                            <img src="<?= $restaurant->getImageUrl() ?>" alt="Current" class="img-thumbnail" style="height: 80px;">
                            <small class="text-muted d-block">Current image</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Assign Executive Chef</label>
                <select name="chef_id" class="form-select">
                    <option value="">-- No Chef Assigned --</option>
                    <?php foreach ($chefs as $chef): ?>
                        <option value="<?= $chef->getId() ?>" 
                            <?= (isset($restaurant) && $restaurant->getChefId() == $chef->getId()) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($chef->getName()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="row mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Session Duration (min)</label>
                    <input type="number" name="session_duration" class="form-control" value="<?= $restaurant->getSessionDuration() ?? 90 ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Reservation Fee (€)</label>
                    <input type="number" step="0.01" name="reservation_fee" class="form-control" value="<?= $restaurant->getReservationFee() ?? 10 ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Total Slots</label>
                    <input type="number" name="total_slots" class="form-control" value="<?= $restaurant->getTotalSlots() ?? 50 ?>" required>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 border-top pt-4">
                <a href="/admin/yummy" class="btn btn-light px-4">Cancel</a>
                <button type="submit" class="btn <?= $isEdit ? 'btn-success' : 'btn-primary' ?> px-4 fw-bold">
                    <i class="bi bi-save me-2"></i><?= $isEdit ? 'Update Restaurant' : 'Create Restaurant' ?>
                </button>
            </div>

        </form> </div>
</div>