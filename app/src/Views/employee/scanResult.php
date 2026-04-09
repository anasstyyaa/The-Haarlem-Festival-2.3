<?php require __DIR__ . '/../partials/header.php'; ?>

<?php
$status = $status ?? 'error';
$message = $message ?? 'Unknown result.';
$ticket = $ticket ?? null;

$statusClass = 'scan-result--error';
$icon = '❌';

if ($status === 'success') {
    $statusClass = 'scan-result--success';
    $icon = '✅';
} elseif ($status === 'warning') {
    $statusClass = 'scan-result--warning';
    $icon = '⚠️';
}
?>

<div class="scan-result-page">
    <div class="scan-result-card <?= $statusClass ?>">
        <div class="scan-result-status-label <?= $statusClass ?>">
            <?= strtoupper($status) ?>
        </div>
        <h2 class="scan-result-message"><?= htmlspecialchars($message) ?></h2>

        <?php if ($ticket): ?>
            <div class="scan-result-ticket">
                <p><strong>Ticket ID:</strong> <?= htmlspecialchars((string)$ticket['id']) ?></p>
                <p><strong>Event ID:</strong> <?= htmlspecialchars((string)$ticket['event_id']) ?></p>
                <p><strong>People:</strong> <?= htmlspecialchars((string)$ticket['number_of_people']) ?></p>
                <p><strong>Status:</strong> <?= htmlspecialchars((string)$ticket['status']) ?></p>
            </div>
        <?php endif; ?>

        <div class="scan-result-actions">
            <a href="/employee/scan" class="scan-result-button">
                Scan Another Ticket
            </a>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>