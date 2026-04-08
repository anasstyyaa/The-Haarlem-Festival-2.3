<?php
/**
 * @var array $tickets
 * @var object $qrcode 
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Helvetica', sans-serif; }
        .ticket-wrapper {
            border: 2px dashed #333;
            padding: 20px;
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .festival-header { color: #d32f2f; }
    </style>
</head>
<body>
    <?php foreach ($tickets as $ticket): ?>
        <div class="ticket-wrapper">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 70%;">
                        <h1 class="festival-header">HAARLEM FESTIVAL</h1>
                        <p><strong>Event:</strong> <?= htmlspecialchars($ticket->title) ?></p>
                        <p><strong>Location:</strong> <?= htmlspecialchars($ticket->location) ?></p>
                        <p><strong>Date:</strong> <?= htmlspecialchars($ticket->date) ?></p>
                        <p><strong>Time:</strong> 
                            <?= htmlspecialchars($ticket->startTime) ?> 
                            <?php if (!empty($ticket->endTime)): ?>
                                - <?= htmlspecialchars($ticket->endTime) ?>
                            <?php endif; ?>
                        </p>
                        <?php if (!empty($ticket->language)): ?>
                            <p><strong>Language:</strong> <?= htmlspecialchars($ticket->language) ?></p>
                        <?php endif; ?>
                        <p><strong>Quantity:</strong> <?= $ticket->guestCount ?> Person(s)</p>
                    </td>
                    <td style="width: 30%; text-align: right; vertical-align: top;">
                        <img src="<?= $qrcode->render($ticket->token) ?>" width="120" />
                        <div style="font-size: 10px; margin-top: 5px; color: #666;">Scan at Entrance</div>
                    </td>
                </tr>
            </table>
        </div>
    <?php endforeach; ?>
</body>
</html>