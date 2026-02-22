<?php require __DIR__ . '/../../partials/adminHeader.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-cup-hot-fill me-2"></i>Manage Restaurants</h2>
    <a href="/admin/yummy/create" class="btn btn-primary">Add New Restaurant</a>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th class="ps-3">ID</th>
                    <th>Restaurant</th>
                    <th>Cuisine</th>
                    <th>Location</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($restaurants)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No restaurants found in database.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($restaurants as $restaurant): ?>
                        <tr>
                            <td class="ps-3"><?= $restaurant->getId() ?></td>
                            <td>
                                <strong><?= htmlspecialchars($restaurant->getName()) ?></strong>
                            </td>
                            <td>
                                <span class="badge bg-info text-dark">
                                    <?= htmlspecialchars($restaurant->getCuisine()) ?>
                                </span>
                            </td>
                            <td>
                                <small class="text-muted"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($restaurant->getLocation()) ?></small>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="/admin/yummy/edit/<?= $restaurant->getId() ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    
                                    <a href="/admin/yummy/delete/<?= $restaurant->getId() ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Are you sure you want to delete this restaurant?')">
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