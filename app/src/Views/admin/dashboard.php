<?php require __DIR__ . '/../partials/adminHeader.php'; ?>

<style>
.admin-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    font-size: 14px;
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
}

.status-paid { color: green; font-weight: bold; }
.status-pending { color: orange; font-weight: bold; }
.status-expired { color: red; font-weight: bold; }

.top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.export-btn {
    background: #4b1608;
    color: white;
    padding: 8px 12px;
    text-decoration: none;
    border-radius: 4px;
}

.export-btn:hover {
    background: #6d1f0c;
}
</style>

<div class="top-bar">
    <h1>Tickets Dashboard</h1>

    <a href="/admin/export-csv" class="export-btn">Export CSV</a>
     <a href="/admin/export-excel" class="export-btn">Export Excel</a>
</div>
<form method="POST" action="/admin/export-excel">
    <div style="margin-bottom:10px;">
        <label><input type="checkbox" name="columns[]" value="id" checked> ID</label>
        <label><input type="checkbox" name="columns[]" value="FullName" checked> User</label>
        <label><input type="checkbox" name="columns[]" value="Email" checked> Email</label>
        <label><input type="checkbox" name="columns[]" value="eventType" checked> Event Type</label>
        <label><input type="checkbox" name="columns[]" value="number_of_people"> People</label>
        <label><input type="checkbox" name="columns[]" value="unit_price"> Unit Price</label>
        <label><input type="checkbox" name="columns[]" value="total_price" checked> Total</label>
        <label><input type="checkbox" name="columns[]" value="status" checked> Status</label>
        <label><input type="checkbox" name="columns[]" value="is_scanned"> Scanned</label>
        <label><input type="checkbox" name="columns[]" value="created_at"> Created</label>
    </div>
    <button type="submit" class="export-btn">Export Selected to Excel</button>
</form>
<form method="POST" action="/admin/export-csv">
    <div style="margin-bottom:10px;">
        <label><input type="checkbox" name="columns[]" value="id" checked> ID</label>
        <label><input type="checkbox" name="columns[]" value="FullName" checked> User</label>
        <label><input type="checkbox" name="columns[]" value="Email" checked> Email</label>
        <label><input type="checkbox" name="columns[]" value="eventType" checked> Event Type</label>
        <label><input type="checkbox" name="columns[]" value="number_of_people"> People</label>
        <label><input type="checkbox" name="columns[]" value="unit_price"> Unit Price</label>
        <label><input type="checkbox" name="columns[]" value="total_price" checked> Total</label>
        <label><input type="checkbox" name="columns[]" value="status" checked> Status</label>
        <label><input type="checkbox" name="columns[]" value="is_scanned"> Scanned</label>
        <label><input type="checkbox" name="columns[]" value="created_at"> Created</label>
    </div>

    <button type="submit" class="export-btn">Export Selected to CSV</button>
</form>

<table class="admin-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Email</th>
            <th>Event Type</th>
            <th>People</th>
            <th>Unit Price</th>
            <th>Total</th>
            <th>Status</th>
            <th>Scanned</th>
            <th>Created</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($tickets as $ticket): ?>
            <tr>
                <td><?= $ticket['id'] ?></td>

                <td>
                    <?= $ticket['FullName'] ?? 'Guest' ?>
                </td>

                <td>
                    <?= $ticket['Email'] ?? '-' ?>
                </td>

                <td>
                    <?= ucfirst($ticket['eventType']) ?>
                </td>

                <td><?= $ticket['number_of_people'] ?></td>

                <td>€<?= number_format($ticket['unit_price'], 2) ?></td>

                <td>
                    <strong>€<?= number_format($ticket['total_price'], 2) ?></strong>
                </td>

                <td class="status-<?= strtolower($ticket['status']) ?>">
                    <?= ucfirst($ticket['status']) ?>
                </td>

                <td>
                    <?= $ticket['is_scanned'] ? '✅' : '❌' ?>
                </td>

                <td>
                    <?= isset($ticket['created_at']) 
                        ? date('Y-m-d H:i', strtotime($ticket['created_at'])) 
                        : '-' ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require __DIR__ . '/../partials/adminFooter.php'; ?>