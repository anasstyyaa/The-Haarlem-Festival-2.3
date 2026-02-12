<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../components/card_start.php'; ?>

<h1 class="mb-4">My Reservations</h1>

<?php
$errors = $_SESSION['reservation_errors'] ?? [];
unset($_SESSION['reservation_errors']);
?>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if (empty($reservations)): ?>
    <p class="text-muted">You have no reservations yet.</p>
<?php else: ?>
    <table class="table table-striped mt-3">
        <thead>
        <tr>
            <th>PC Number</th>
            <th>Date</th>
            <th>Start</th>
            <th>End</th>
            <th>Status</th>
            <th>Actions</th>
            <th>Total Price</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($reservations as $res): ?>
            <tr>
                <td><?= (int)$res->pc_id ?></td>
                <td><?= date('d M Y', strtotime($res->start_time)) ?></td>
                <td><?= date('H:i', strtotime($res->start_time)) ?></td>
                <td><?= date('H:i', strtotime($res->end_time)) ?></td>

                <td><?= htmlspecialchars($res->status) ?></td>
                <td><?php $reservation = $res; require __DIR__ . '/../partials/cancel_reservation_button.php'; ?></td>

                <td>€<?= number_format((float)($res->total_price ?? 0), 2) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require __DIR__ . '/../components/card_end.php'; ?>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
