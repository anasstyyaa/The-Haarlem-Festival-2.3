<?php require __DIR__ . '/../../../partials/adminHeader.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-geo-alt-fill me-2"></i>Manage History Venues</h2>
    <a href="/admin/history/venues/create" class="btn btn-primary">Add New Venue</a>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th class="ps-3">ID</th>
                    <th>Venue Name</th>
                    <th>Details</th>
                    <th>Location</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($venues)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No history venues found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($venues as $venue): ?>
                        <tr>
                            <td class="ps-3"><?= $venue->getVenueId() ?></td>
                            <td><strong><?= htmlspecialchars($venue->getVenueName()) ?></strong></td>
                            <td><?= htmlspecialchars($venue->getDetails() ?? '') ?></td>
                            <td><?= htmlspecialchars($venue->getLocation() ?? '') ?></td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="/admin/history/venues/edit?id=<?= $venue->getVenueId() ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>

                                    <form action="/admin/history/venues/delete" method="POST" class="d-inline" onsubmit="return confirm('Delete this venue?')">
                                        <input type="hidden" name="id" value="<?= $venue->getVenueId() ?>">
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

<?php require __DIR__ . '/../../../partials/adminFooter.php'; ?>