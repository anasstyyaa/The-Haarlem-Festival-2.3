<?php require __DIR__ . '/../../partials/adminHeader.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex align-items-center mb-4">
            <a href="/admin/yummy" class="btn btn-outline-secondary me-3">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h2 class="mb-0">Add New Restaurant</h2>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Restaurant Details</h5>
            </div>
            
            <div class="card-body p-4">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form action="/admin/yummy/create" method="POST" enctype="multipart/form-data">
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Restaurant Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Ratatouille" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Cuisine Type</label>
                            <input type="text" name="cuisine" class="form-control" placeholder="e.g. French, Seafood">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Location</label>
                            <input type="text" name="location" class="form-control" placeholder="e.g. Grote Markt 10">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Brief description of the restaurant..."></textarea>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Detailed Page Content (WYSIWYG)</label>
                            <textarea name="long_description" class="form-control wysiwyg-editor" rows="12" placeholder="Describe the menu, history, and special features..."></textarea>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Restaurant Image</label>
                            <input type="file" name="image_file" class="form-control" accept="image/*">
                            <div class="form-text">Select a high-quality photo (JPG, PNG, or WebP).</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Assign Executive Chef</label>
                        <select name="chef_id" class="form-select">
                            <option value="">-- No Chef Assigned --</option>
                            <?php foreach ($chefs as $chef): ?>
                                <option value="<?= $chef->getId() ?>" 
                                    <?= ($restaurant->getChefId() == $chef->getId()) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($chef->getName()) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Choose a chef from the "Manage Chefs" list above.</div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="/admin/yummy" class="btn btn-light px-4">Cancel</a>
                        <button type="submit" class="btn btn-success px-4">Save Restaurant</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../partials/adminFooter.php'; ?>