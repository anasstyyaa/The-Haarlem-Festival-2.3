<?php require __DIR__ . '/../../../partials/adminHeader.php'; ?>

<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-pencil-square me-2"></i>Edit History Venue</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form method="POST" action="/admin/history/venues/edit" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $venue->getVenueId() ?>">

                <div class="mb-3">
                    <label for="venueName" class="form-label">Venue Name</label>
                    <input
                        type="text"
                        class="form-control"
                        id="venueName"
                        name="venueName"
                        value="<?= htmlspecialchars($venue->getVenueName()) ?>"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label for="details" class="form-label">Details</label>
                    <textarea class="form-control" id="details" name="details" rows="5"><?= htmlspecialchars($venue->getDetails() ?? '') ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="location" class="form-label">Location</label>
                    <input
                        type="text"
                        class="form-control"
                        id="location"
                        name="location"
                        value="<?= htmlspecialchars($venue->getLocation() ?? '') ?>"
                    >
                </div>

                <?php if ($venue->getImgURL()): ?>
                    <div class="mb-3">
                        <label class="form-label d-block">Current Image</label>
                        <img
                            src="<?= htmlspecialchars($venue->getImgURL()) ?>"
                            alt="<?= htmlspecialchars($venue->getAltText() ?? $venue->getVenueName()) ?>"
                            style="max-width: 250px; border-radius: 8px;"
                        >
                    </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label for="image" class="form-label">Upload New Image</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    <div class="form-text">Leave empty if you want to keep the current image.</div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="/admin/history/venues" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../../partials/adminFooter.php'; ?>