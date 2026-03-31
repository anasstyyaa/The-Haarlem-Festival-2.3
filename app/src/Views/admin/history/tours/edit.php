<?php require __DIR__ . '/../../../partials/adminHeader.php'; ?>

<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-pencil-square me-2"></i>Edit History Tour</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form method="POST" action="/admin/history/tours/edit">
                <input type="hidden" name="eventId" value="<?= $tour->getEventId() ?>">
                <input type="hidden" name="historyEventId" value="<?= $tour->getHistoryEventId() ?>">

                <div class="mb-3">
                    <label for="slotDate" class="form-label">Date</label>
                    <input type="date" class="form-control" id="slotDate" name="slotDate" value="<?= htmlspecialchars($tour->getSlotDate()) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="startTime" class="form-label">Start Time</label>
                    <input type="time" class="form-control" id="startTime" name="startTime" value="<?= htmlspecialchars(substr($tour->getStartTime(), 0, 5)) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="language" class="form-label">Language</label>
                    <select class="form-select" id="language" name="language" required>
                        <option value="English" <?= $tour->getLanguage() === 'English' ? 'selected' : '' ?>>English</option>
                        <option value="Dutch" <?= $tour->getLanguage() === 'Dutch' ? 'selected' : '' ?>>Dutch</option>
                        <option value="Mandarin" <?= $tour->getLanguage() === 'Mandarin' ? 'selected' : '' ?>>Mandarin</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="duration" class="form-label">Duration (minutes)</label>
                    <input type="number" class="form-control" id="duration" name="duration" value="<?= $tour->getDuration() ?>" required>
                </div>

                <div class="mb-3">
                    <label for="minAge" class="form-label">Minimum Age</label>
                    <input type="number" class="form-control" id="minAge" name="minAge" value="<?= $tour->getMinAge() ?>" required>
                </div>

                <div class="mb-3">
                    <label for="capacity" class="form-label">Capacity</label>
                    <input type="number" class="form-control" id="capacity" name="capacity" value="<?= $tour->getCapacity() ?>" required>
                </div>

                <div class="mb-3">
                    <label for="priceIndividual" class="form-label">Individual Price</label>
                    <input type="number" step="0.01" class="form-control" id="priceIndividual" name="priceIndividual" value="<?= $tour->getPriceIndividual() ?>" required>
                </div>

                <div class="mb-3">
                    <label for="priceFamily" class="form-label">Family Price</label>
                    <input type="number" step="0.01" class="form-control" id="priceFamily" name="priceFamily" value="<?= $tour->getPriceFamily() ?>" required>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="/admin/history/tours" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../../partials/adminFooter.php'; ?>