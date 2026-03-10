<?php require __DIR__ . '/../../partials/adminHeader.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex align-items-center mb-4">
            <a href="/admin/jazz" class="btn btn-outline-secondary me-3">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h2 class="mb-0">Add New Jazz Event</h2>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Jazz Event Details</h5>
            </div>

            <div class="card-body p-4">
                <form action="/admin/jazz/events/create" method="POST">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Artist</label>
                        <select name="artist_id" class="form-control" required>
                            <option value="">Select artist</option>
                            <?php foreach ($artists as $artist): ?>
                                <option value="<?= $artist->getId() ?>">
                                    <?= htmlspecialchars($artist->getName()) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Venue</label>
                        <select name="jazz_venue_id" class="form-control" required>
                            <option value="">Select venue</option>
                            <option value="1">Patronaat - Main Hall</option>
                            <option value="2">Patronaat - Second Hall</option>
                            <option value="3">Patronaat - Third Hall</option>
                            <option value="4">Grote Markt</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Start Date & Time</label>
                        <input type="datetime-local" name="start_datetime" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">End Date & Time</label>
                        <input type="datetime-local" name="end_datetime" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Price</label>
                        <input type="number" step="0.01" min="0" name="price" class="form-control" required>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="/admin/jazz" class="btn btn-light px-4">Cancel</a>
                        <button type="submit" class="btn btn-success px-4">Save Event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../partials/adminFooter.php'; ?>