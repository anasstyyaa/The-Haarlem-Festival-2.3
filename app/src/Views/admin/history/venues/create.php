<?php require __DIR__ . '/../../../partials/adminHeader.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-plus-circle me-2"></i>Add New History Venue</h2>
    <a href="/admin/history/venues" class="btn btn-secondary">Back</a>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form method="POST" action="/admin/history/venues/create">
            <div class="mb-3">
                <label for="venueName" class="form-label">Venue Name</label>
                <input type="text" class="form-control" id="venueName" name="venueName" required>
            </div>

            <div class="mb-3">
                <label for="details" class="form-label">Details</label>
                <textarea class="form-control" id="details" name="details" rows="4"></textarea>
            </div>

            <div class="mb-3">
                <label for="location" class="form-label">Location</label>
                <input type="text" class="form-control" id="location" name="location">
            </div>

            <button type="submit" class="btn btn-primary">Create Venue</button>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../../../partials/adminFooter.php'; ?>