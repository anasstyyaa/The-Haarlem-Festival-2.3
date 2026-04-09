<?php require __DIR__ . '/../partials/adminHeader.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-ticket-perforated me-2"></i>Manage Tickets</h2>
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


<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>Event Type</th>
                    <th>People</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Scanned</th>
                    <th>Created</th>
                </tr>
            </thead>

            <tbody>
            <?php if (empty($tickets)): ?>
                <tr>
                    <td colspan="9" class="text-center py-4 text-muted">No tickets found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($tickets as $ticket): ?>
                    <tr>
                        <td><?= $ticket['id'] ?></td>
                        <td><?= $ticket['FullName'] ?? 'Guest' ?></td>
                        <td><?= $ticket['Email'] ?? '-' ?></td>
                        <td><?= ucfirst($ticket['eventType']) ?></td>
                        <td><?= $ticket['number_of_people'] ?></td>
                        <td><strong>€<?= number_format($ticket['total_price'], 2) ?></strong></td>
                        <td><?= ucfirst($ticket['status']) ?></td>
                        <td><?= $ticket['is_scanned'] ? '✅' : '❌' ?></td>
                        <td>
                            <?= isset($ticket['created_at'])
                                ? date('Y-m-d H:i', strtotime($ticket['created_at']))
                                : '-' ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>

            <?php
            $baseUrl = '/admin/tickets';
            $paginationTheme = 'dark';
            require __DIR__ . '/pagination.php';
            ?>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../partials/adminFooter.php'; ?>