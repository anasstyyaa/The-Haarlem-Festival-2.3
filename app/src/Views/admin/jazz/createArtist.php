<?php require __DIR__ . '/../../partials/adminHeader.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex align-items-center mb-4">
            <a href="/admin/jazz" class="btn btn-outline-secondary me-3">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h2 class="mb-0">Add New Artist</h2>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Artist Details</h5>
            </div>

            <div class="card-body p-4">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form action="/admin/jazz/create" method="POST" enctype="multipart/form-data">

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Artist Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Karsu" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Short Description</label>
                        <textarea name="short_description" class="form-control" rows="3" placeholder="Short text for the lineup card..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Full Description</label>
                        <textarea name="description" class="form-control wysiwyg-editor" rows="5" placeholder="Full artist description for the detail page..."></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Artist Image</label>
                            <input type="file" name="image_file" class="form-control" accept="image/*">
                            <div class="form-text">Select a high-quality photo (JPG, PNG, or WebP).</div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="/admin/jazz" class="btn btn-light px-4">Cancel</a>
                        <button type="submit" class="btn btn-success px-4">Save Artist</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../partials/adminFooter.php'; ?>