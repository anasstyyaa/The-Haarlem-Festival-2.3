<?php require __DIR__ . '/../../partials/adminHeader.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex align-items-center mb-4">
            <a href="/admin/jazz" class="btn btn-outline-secondary me-3">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h2 class="mb-0">Edit Artist</h2>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Updating: <?= htmlspecialchars($artist->getName()) ?></h5>
            </div>

            <div class="card-body p-4">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form action="/admin/jazz/edit/<?= $artist->getId() ?>" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $artist->getId() ?>">

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Artist Name</label>
                            <input type="text" name="name" class="form-control"
                                   value="<?= htmlspecialchars($artist->getName()) ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Short Description</label>
                        <textarea name="short_description" class="form-control" rows="3"><?= htmlspecialchars($artist->getShortDescription() ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Full Description</label>
                        <textarea name="description" class="form-control wysiwyg-editor" rows="5"><?= htmlspecialchars($artist->getDescription() ?? '') ?></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Artist Image</label>
                            <div class="mb-2">
                                <?php if ($artist->getImageUrl()): ?>
                                    <img src="<?= htmlspecialchars($artist->getImageUrl()) ?>" alt="Artist" class="img-thumbnail" style="height: 60px;">
                                <?php else: ?>
                                    <span class="text-muted small">No image set</span>
                                <?php endif; ?>
                            </div>
                            <input type="file" name="image_file" class="form-control" accept="image/*">
                            <div class="form-text">Choose a file to replace the current image.</div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="/admin/jazz" class="btn btn-light px-4">Cancel</a>
                        <button type="submit" class="btn btn-warning px-4">Update Artist</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../partials/adminFooter.php'; ?>