<!-- will delete this head when we will have a header -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Users</h2>
        <a href="/admin/users/create" class="btn btn-primary">Add New User</a>
    </div>

    <table class="table table-striped table-hover border">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Created At</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user->getId() ?></td>
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
                            <span class="badge bg-danger">Inactive (Soft-Deleted)</span>
                        <?php else: ?>
                            <span class="badge bg-success">Active</span>
                        <?php endif; ?>
                    </td>
                    <td><?= date('d-m-Y', strtotime($user->getCreatedAt())) ?></td>
                    <td class="text-center">
                        <a href="/admin/users/edit?id=<?= $user->getId() ?>" class="btn btn-sm btn-warning">Edit</a>
                        
                        <?php if (!$user->getDeletedAt()): ?>
                            <form action="/admin/users/delete" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                <input type="hidden" name="id" value="<?= $user->getId() ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Deactivate</button>
                            </form>
                        <?php else: ?>
                            <form action="/admin/users/restore" method="POST" class="d-inline">
                                <input type="hidden" name="id" value="<?= $user->getId() ?>">
                                <button type="submit" class="btn btn-sm btn-success">Restore</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>