<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../components/card_start.php'; ?>

<h1 class="mb-4">All Reservations (Admin)</h1>

<?php if (empty($reservations)): ?>
    <p class="text-muted">No reservations in the system yet.</p>
<?php else: ?>
    <table class="table table-striped mt-3">
        <thead>
        <tr>
            <th>User Name</th>
            <th>PC Number</th>
            <th>Start</th>
            <th>End</th>
            <th>Status</th>
            <th>Created at</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($reservations as $res): ?>
            <tr>
                <td><?= htmlspecialchars($res->user_name ?? '-') ?></td>
                <td><?= (int)$res->pc_id ?></td>
             <td><?= date('H:i', strtotime($res->start_time)) ?></td>
<td><?= date('H:i', strtotime($res->end_time)) ?></td>
                <td><?= htmlspecialchars($res->status) ?></td>
                <td><?= htmlspecialchars($res->created_at) ?></td>
                <td><?php $reservation = $res; require __DIR__ . '/../partials/cancel_reservation_button.php'; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require __DIR__ . '/../components/card_end.php'; ?>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
