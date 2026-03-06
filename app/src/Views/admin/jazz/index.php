<?php require __DIR__ . '/../../partials/adminHeader.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-music-note-beamed me-2"></i>Manage Jazz Artists</h2>
    <a href="/admin/jazz/create" class="btn btn-primary">Add New Artist</a>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th class="ps-3">ID</th>
                    <th>Artist</th>
                    <th>Short Description</th>
                    <th>Image</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($artists)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No artists found in database.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($artists as $artist): ?>
                        <tr>
                            <td class="ps-3"><?= $artist->getId() ?></td>
                            <td>
                                <strong><?= htmlspecialchars($artist->getName()) ?></strong>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?= htmlspecialchars($artist->getShortDescription() ?? '') ?>
                                </small>
                            </td>
                            <td>
                                <?php if ($artist->getImageUrl()): ?>
                                    <img src="<?= htmlspecialchars($artist->getImageUrl()) ?>" alt="Artist" class="img-thumbnail" style="height: 60px;">
                                <?php else: ?>
                                    <span class="text-muted small">No image set</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="/admin/jazz/edit/<?= $artist->getId() ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>

                                    <a href="/admin/jazz/delete/<?= $artist->getId() ?>"
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Are you sure you want to delete this artist?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../../partials/adminFooter.php'; ?>