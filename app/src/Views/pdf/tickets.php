<?php
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Common\EccLevel;

/**
 * @var array $user
 * @var array $tickets
 * @var object $service 
 */

$options = new QROptions([
    'version'      => 5,
    'outputType'   => 'gdimage_png',
    'eccLevel'     => EccLevel::L, 
    'addQuietzone' => true,
    'imageBase64'  => true, 
]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Helvetica', sans-serif; }
        .ticket-container {
            border: 2px dashed #000;
            padding: 30px;
            margin-bottom: 50px;
            position: relative;
            height: 250px;
        }
        .festival-name { color: #d32f2f; font-size: 24px; font-weight: bold; margin: 0; }
        .event-title { font-size: 20px; margin: 10px 0; }
        .qr-code { position: absolute; right: 30px; top: 30px; }
        .footer-note { font-size: 10px; color: #777; margin-top: 20px; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <?php foreach ($tickets as $ticket): 
        $eventName = $service->getEventNameFromTicket($ticket);
        $token = $ticket->getUniqueTicketToken();
        $eventDate = $service->getEventDateDisplay($ticket);
        $qrData = "https://yourdomain.com/scan?token=" . $token;
        $qrcode = new QRCode($options);
        $qrCodeMarkup = $qrcode->render($qrData);
    ?>
        <div class="ticket-wrapper">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="vertical-align: top; width: 70%;">
                        <h1 class="festival-header">HAARLEM FESTIVAL</h1>
                        <div class="event-info">
                            <p><span class="label">Event:</span> <span style="font-size: 18px; font-weight: bold;"><?= htmlspecialchars($eventName) ?></span></p>
                            <p><span class="label">Date/Time:</span> <?= htmlspecialchars($eventDate) ?></p>
                            <p><span class="label">Attendee:</span> <?= htmlspecialchars($user['full_name']) ?></p>
                            <p><span class="label">Quantity:</span> <?= $ticket->getNumberOfPeople() ?> Person(s)</p>
                            <p style="margin-top: 15px; font-family: monospace; font-size: 11px; color: #999;">
                                ID: <?= $token ?>
                            </p>
                        </div>
                    </td>
                    <td class="qr-section">
                        <img src="<?= $qrCodeMarkup ?>" width="160" height="160" alt="Ticket QR" />
                        <div class="qr-footer">Scan to Validate</div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="page-break"></div>
    <?php endforeach; ?>
</body>
</html>