<?php require __DIR__ . '/../../partials/adminHeader.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex align-items-center mb-4">
            <a href="/admin/jazz" class="btn btn-outline-secondary me-3">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h2 class="mb-0">Edit Jazz Pass</h2>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Updating Pass</h5>
            </div>

            <div class="card-body p-4">
                <form action="/admin/jazz/passes/edit/<?= $pass->getId() ?>" method="POST" enctype="multipart/form-data">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Title</label>
                        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($pass->getTitle()) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($pass->getDescription() ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Price</label>
                        <input type="number" step="0.01" min="0" name="price" class="form-control" value="<?= htmlspecialchars((string)$pass->getPrice()) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Capacity</label>
                        <input type="number" min="0" name="capacity" class="form-control" value="<?= htmlspecialchars((string)$pass->getCapacity()) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Tickets Left</label>
                        <input type="number" min="0" name="tickets_left" class="form-control" value="<?= htmlspecialchars((string)$pass->getTicketsLeft()) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Current Image</label><br>
                        <?php if ($pass->getImageUrl()): ?>
                            <img src="<?= htmlspecialchars($pass->getImageUrl()) ?>" alt="Pass" class="img-thumbnail mb-2" style="height: 100px;">
                        <?php else: ?>
                            <span class="text-muted">No image set</span>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">New Image</label>
                        <input type="file" name="image_file" class="form-control" accept="image/*">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Active</label>
                        <select name="is_active" class="form-control">
                            <option value="1" <?= $pass->isActive() ? 'selected' : '' ?>>Active</option>
                            <option value="0" <?= !$pass->isActive() ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="/admin/jazz" class="btn btn-light px-4">Cancel</a>
                        <button type="submit" class="btn btn-warning px-4">Update Pass</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../partials/adminFooter.php'; ?>