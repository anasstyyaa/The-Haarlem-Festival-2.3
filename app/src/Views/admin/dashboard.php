<?php require __DIR__ . '/../partials/adminHeader.php'; ?>
<style>.admin-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.admin-table th,
.admin-table td {
    padding: 10px;
    border-bottom: 1px solid #ddd;
    text-align: left;
}

.admin-table th {
    background: #4b1608;
    color: white;
}

.admin-table tr:hover {
    background: #f5f5f5;
}</style>

<h1>Tickets Dashboard</h1>

<table class="admin-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Event ID</th>
            <th>User</th>
            <th>People</th>
            <th>Unit Price</th>
            <th>Total</th>
            <th>Status</th>
            <th>Scanned</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($tickets as $ticket): ?>
            <tr>
                <td><?= $ticket['id'] ?></td>

                <td><?= $ticket['event_id'] ?></td>

                <td>
                    <?= $ticket['user_id'] ?? 'Guest' ?>
                </td>

                <td><?= $ticket['number_of_people'] ?></td>

                <td>€<?= number_format($ticket['unit_price'], 2) ?></td>

                <td>€<?= number_format($ticket['total_price'], 2) ?></td>

                <td><?= $ticket['status'] ?></td>

                <td>
                    <?= $ticket['is_scanned'] ? '✅' : '❌' ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require __DIR__ . '/../partials/adminFooter.php'; ?>