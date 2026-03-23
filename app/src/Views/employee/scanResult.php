<?php require __DIR__ . '/../partials/header.php'; ?>

<?php
$status = $status ?? 'error';
$message = $message ?? 'Unknown result.';
$ticket = $ticket ?? null;

$bgColor = '#f8d7da';
$textColor = '#842029';
$icon = '❌';

if ($status === 'success') {
    $bgColor = '#d1e7dd';
    $textColor = '#0f5132';
    $icon = '✅';
} elseif ($status === 'warning') {
    $bgColor = '#fff3cd';
    $textColor = '#664d03';
    $icon = '⚠️';
}
?>

<div style="max-width:700px; margin:40px auto; padding:20px;">
    <div style="background-color: <?= $bgColor ?>; color: <?= $textColor ?>; padding:30px; border-radius:12px; text-align:center; box-shadow:0 4px 12px rgba(0,0,0,0.1);">
        <div style="font-size:60px; margin-bottom:10px;"><?= $icon ?></div>
        <h2 style="margin-bottom:10px;"><?= htmlspecialchars($message) ?></h2>

        <?php if ($ticket): ?>
            <div style="margin-top:20px; text-align:left; background:#fff; padding:20px; border-radius:10px; color:#333;">
                <p><strong>Ticket ID:</strong> <?= htmlspecialchars((string)$ticket['id']) ?></p>
                <p><strong>Event ID:</strong> <?= htmlspecialchars((string)$ticket['event_id']) ?></p>
                <p><strong>People:</strong> <?= htmlspecialchars((string)$ticket['number_of_people']) ?></p>
                <p><strong>Status:</strong> <?= htmlspecialchars((string)$ticket['status']) ?></p>
            </div>
        <?php endif; ?>

        <div style="margin-top:25px;">
            <a href="/employee/scan" style="display:inline-block; padding:12px 20px; background:#0d6efd; color:white; text-decoration:none; border-radius:8px;">
                Scan Another Ticket
            </a>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>