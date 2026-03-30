<?php require __DIR__ . '/../../../partials/adminHeader.php'; ?>

<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-plus-circle me-2"></i>Create History Venue</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form method="POST" action="/admin/history/venues/create" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="venueName" class="form-label">Venue Name</label>
                    <input type="text" class="form-control" id="venueName" name="venueName" required>
                </div>

                <div class="mb-3">
                    <label for="details" class="form-label">Details</label>
                    <textarea class="form-control" id="details" name="details" rows="5"></textarea>
                </div>

                <div class="mb-3">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" class="form-control" id="location" name="location">
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Upload Image</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    <div class="form-text">Choose an image from your PC for this venue.</div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Create Venue</button>
                    <a href="/admin/history/venues" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../../partials/adminFooter.php'; ?>