<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0"><i class="bi bi-geo-alt-fill me-2"></i>Manage History Venues</h3>
    <div class="d-flex gap-2">
        <a href="/admin/history/venues/create" class="btn btn-primary">Add Venue</a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th class="ps-3">ID</th>
                    <th>Image</th>
                    <th>Venue Name</th>
                    <th>Details</th>
                    <th>Location</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($venues)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No history venues found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($venues as $venue): ?>
                        <tr>
                            <td class="ps-3"><?= $venue->getVenueId() ?></td>

                            <td>
                                <?php if ($venue->getImgURL()): ?>
                                    <img
                                        src="<?= htmlspecialchars($venue->getImgURL()) ?>"
                                        alt="<?= htmlspecialchars($venue->getAltText() ?? $venue->getVenueName()) ?>"
                                        style="width: 90px; height: 60px; object-fit: cover; border-radius: 8px;"
                                    >
                                <?php else: ?>
                                    <span class="text-muted">No image</span>
                                <?php endif; ?>
                            </td>

                            <td><strong><?= htmlspecialchars($venue->getVenueName()) ?></strong></td>

                            <td>
                                <?php
                                $details = $venue->getDetails() ?? '';
                                echo htmlspecialchars(mb_strimwidth($details, 0, 120, '...'));
                                ?>
                            </td>

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