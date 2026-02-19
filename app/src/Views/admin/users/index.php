<?php require __DIR__ . '/../../partials/adminHeader.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-people-fill me-2"></i>Manage Users</h2>
    <a href="/admin/users/create" class="btn btn-primary">Add New User</a>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th class="ps-3">ID</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created At</th> <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No users found in database.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="ps-3"><?= $user->getId() ?></td>
                            <td>
                                <strong><?= htmlspecialchars($user->getFullName()) ?></strong><br>
                                <small class="text-muted">@<?= htmlspecialchars($user->getUserName()) ?></small>
                            </td>
                            <td><?= htmlspecialchars($user->getEmail()) ?></td>
                            <td>
                                <span class="badge bg-info text-dark"><?= ucfirst($user->getRole()) ?></span>
                            </td>
                            <td>
                                <?php if ($user->getDeletedAt()): ?>
                                    <span class="badge bg-danger">Inactive</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Active</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d-m-Y', strtotime($user->getCreatedAt())) ?></td>
                            <td class="text-center">
                                <a href="/admin/users/edit?id=<?= $user->getId() ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <a href="/admin/users/delete?id=<?= $user->getId() ?>" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../../partials/adminFooter.php'; ?>