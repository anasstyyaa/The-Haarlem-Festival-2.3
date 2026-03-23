<?php require __DIR__ . '/../../partials/adminHeader.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex align-items-center mb-4">
            <a href="/admin/jazz" class="btn btn-outline-secondary me-3">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h2 class="mb-0">Add New Jazz Pass</h2>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Jazz Pass Details</h5>
            </div>

            <div class="card-body p-4">
                <form action="/admin/jazz/passes/create" method="POST" enctype="multipart/form-data">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="description" class="form-control wysiwyg-editor" rows="5" placeholder="Full artist description for the detail page..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Price</label>
                        <input type="number" step="0.01" min="0" name="price" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Capacity</label>
                        <input type="number" min="0" name="capacity" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Image</label>
                        <input type="file" name="image_file" class="form-control" accept="image/*">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Active</label>
                        <select name="is_active" class="form-control">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="/admin/jazz" class="btn btn-light px-4">Cancel</a>
                        <button type="submit" class="btn btn-success px-4">Save Pass</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../partials/adminFooter.php'; ?>