<?php require __DIR__ . '/../../partials/adminHeader.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-people-fill me-2"></i>Manage Users</h2>
    <a href="/admin/users/create" class="btn btn-primary">Add New User</a>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <div class="card shadow-sm border-0 mb-4 p-3">
            <form action="/admin/users" method="GET" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select">
                        <option value="">All Roles</option>
                        <option value="Admin" <?= ($_GET['role'] ?? '') === 'Admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="User" <?= ($_GET['role'] ?? '') === 'User' ? 'selected' : '' ?>>User</option>
                        <option value="Employee" <?= ($_GET['role'] ?? '') === 'Employee' ? 'selected' : '' ?>>Employee</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="sort" class="form-select">
                        <option value="created_at_desc" <?= ($_GET['sort'] ?? '') === 'created_at_desc' ? 'selected' : '' ?>>Newest First</option>
                        <option value="created_at_asc" <?= ($_GET['sort'] ?? '') === 'created_at_asc' ? 'selected' : '' ?>>Oldest First</option>
                        <option value="name_asc" <?= ($_GET['sort'] ?? '') === 'name_asc' ? 'selected' : '' ?>>Name (A-Z)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100">Filter</button>
                </div>
            </form>
        </div>
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
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="/admin/users/edit?id=<?= $user->getId() ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    
                                    <?php if (!$user->getDeletedAt()): ?>
                                        <form action="/admin/users/delete" method="POST" class="d-inline" onsubmit="return confirm('Delete this user?')">
                                            <input type="hidden" name="id" value="<?= $user->getId() ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <form action="/admin/users/restore" method="POST" class="d-inline">
                                            <input type="hidden" name="id" value="<?= $user->getId() ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-success">
                                                <i class="bi bi-arrow-counterclockwise"></i> Restore
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <?php 
                $baseUrl = '/admin/users'; 
                $queryParams = $filters; // using the filters array from users controller
                $paginationTheme = 'dark';
                require __DIR__ . '/../../partials/pagination.php'; 
            ?>
            </table>
    </div>
</div>

<?php require __DIR__ . '/../../partials/adminFooter.php'; ?>