<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0"><i class="bi bi-calendar-event me-2"></i>Manage History Tours</h3>
    <div class="d-flex gap-2">
        <a href="/admin/history/tours/create" class="btn btn-primary">Add Tour</a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th class="ps-3">Event ID</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Language</th>
                    <th>Capacity</th>
                    <th>Prices</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tours)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No tours found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($tours as $tour): ?>
                        <tr>
                            <td class="ps-3"><?= $tour->getEventId() ?></td>
                            <td><?= htmlspecialchars($tour->getSlotDate()) ?></td>
                            <td><?= htmlspecialchars(substr($tour->getStartTime(), 0, 5)) ?></td>
                            <td><?= htmlspecialchars($tour->getLanguage()) ?></td>
                            <td><?= htmlspecialchars((string)$tour->getCapacity()) ?></td>
                            <td>
                                Individual: €<?= number_format($tour->getPriceIndividual(), 2) ?><br>
                                Family: €<?= number_format($tour->getPriceFamily(), 2) ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="/admin/history/tours/edit?id=<?= $tour->getEventId() ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>

                                    <form action="/admin/history/tours/delete" method="POST" class="d-inline" onsubmit="return confirm('Delete this tour?')">
                                        <input type="hidden" name="id" value="<?= $tour->getEventId() ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>