<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php require __DIR__ . '/../../components/card_start.php'; ?>


<h1>Manage PCs (Admin)</h1>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<a href="/admin/pcs/create" class="btn btn-primary mb-3">+ Add PC</a>

<?php if (empty($pcs)): ?>
    <p>No PCs found.</p>
<?php else: ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Specs</th>
                <th>Price/hour</th>
                <th>Availability</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($pcs as $pc): ?>
            <tr>
                <td><?= (int)$pc->id ?></td>
                <td><?= htmlspecialchars($pc->name ?? '') ?></td>
                <td><?= htmlspecialchars($pc->specs ?? '') ?></td>
                <td><?= htmlspecialchars((string)($pc->price_per_hour ?? '')) ?></td>
                <td>
                    <?php if ((int)($pc->is_active ?? 1) === 1): ?>
                        <span class="badge bg-success">Active</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Maintenance</span>
                    <?php endif; ?>
                </td>
                <td class="d-flex gap-3 align-items-center">
                    <a class="btn btn-sm btn-outline-light" href="/admin/pcs/edit/<?= (int)$pc->id ?>">Edit</a>


                    <form method="post" action="/admin/pcs/toggle/<?= (int)$pc->id ?>" style="display:inline;">
                        <button class="btn btn-sm btn-outline-light" type="submit">
                            Toggle availability
                        </button>
                    </form>

                    <form method="post" action="/admin/pcs/delete/<?= (int)$pc->id ?>" style="display:inline;"
                          onsubmit="return confirm('Delete this PC?');">
                        <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
<?php require __DIR__ . '/../../components/card_end.php'; ?>
<?php require __DIR__ . '/../../layouts/footer.php'; ?>
