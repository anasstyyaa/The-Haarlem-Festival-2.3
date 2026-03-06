<?php require __DIR__ . '/../../partials/adminHeader.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex align-items-center mb-4">
            <a href="/admin/yummy" class="btn btn-outline-secondary me-3">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h2 class="mb-0">Edit Chef Profile</h2>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Modify Details for <?= htmlspecialchars($chef->getName()) ?></h5>
            </div>
            
            <div class="card-body p-4">
                <form action="/admin/chefs/edit/<?= $chef->getId() ?>" method="POST" enctype="multipart/form-data">
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Chef Name</label>
                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($chef->getName()) ?>" required>
                        </div>
                    </div>

                    <div class="col-md-4">
                            <label class="form-label fw-bold">Years of Experience</label>
                            <input type="number" name="experience_years" class="form-control" value="<?= $chef->getExperienceYears() ?>" min="0">
                        </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Biography (WYSIWYG)</label>
                        <textarea name="description" class="form-control wysiwyg-editor" rows="10"><?= $chef->getDescription() ?></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Chef Photo</label>
                            <div class="mb-3 mt-2">
                                <?php if ($chef->getImageUrl()): ?>
                                    <img src="<?= $chef->getImageUrl() ?>" alt="Current Photo" class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="text-muted small">No photo uploaded.</div>
                                <?php endif; ?>
                            </div>
                            <input type="file" name="image_file" class="form-control" accept="image/*">
                            <div class="form-text">Leave blank to keep current photo.</div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="/admin/yummy" class="btn btn-light px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">Update Chef</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../partials/adminFooter.php'; ?>