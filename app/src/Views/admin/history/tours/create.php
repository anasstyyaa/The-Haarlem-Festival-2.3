<?php require __DIR__ . '/../../../partials/adminHeader.php'; ?>

<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-plus-circle me-2"></i>Create History Tour</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form method="POST" action="/admin/history/tours/create">
                <div class="mb-3">
                    <label for="slotDate" class="form-label">Date</label>
                    <input type="date" class="form-control" id="slotDate" name="slotDate" required>
                </div>

                <div class="mb-3">
                    <label for="startTime" class="form-label">Start Time</label>
                    <input type="time" class="form-control" id="startTime" name="startTime" required>
                </div>

                <div class="mb-3">
                    <label for="language" class="form-label">Language</label>
                    <select class="form-select" id="language" name="language" required>
                        <option value="">Choose language</option>
                        <option value="English">English</option>
                        <option value="Dutch">Dutch</option>
                        <option value="Mandarin">Mandarin</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="duration" class="form-label">Duration (minutes)</label>
                    <input type="number" class="form-control" id="duration" name="duration" value="150" required>
                </div>

                <div class="mb-3">
                    <label for="minAge" class="form-label">Minimum Age</label>
                    <input type="number" class="form-control" id="minAge" name="minAge" value="12" required>
                </div>

                <div class="mb-3">
                    <label for="capacity" class="form-label">Capacity</label>
                    <input type="number" class="form-control" id="capacity" name="capacity" value="12" required>
                </div>

                <div class="mb-3">
                    <label for="priceIndividual" class="form-label">Individual Price</label>
                    <input type="number" step="0.01" class="form-control" id="priceIndividual" name="priceIndividual" value="17.50" required>
                </div>

                <div class="mb-3">
                    <label for="priceFamily" class="form-label">Family Price</label>
                    <input type="number" step="0.01" class="form-control" id="priceFamily" name="priceFamily" value="60.00" required>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Create Tour</button>
                    <a href="/admin/history/tours" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../../partials/adminFooter.php'; ?>