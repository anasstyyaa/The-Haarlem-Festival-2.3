<?php
/**
 * @var array $user
 * @var array $tickets
 * @var string $orderId
 * @var object $service 
 */
$totalExclVat = 0;
$totalVat = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.4; margin: 0; padding: 0; }
        .container { padding: 30px; }
        .header-table { width: 100%; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .brand { font-size: 24px; font-weight: bold; color: #d32f2f; text-transform: uppercase; }
        .invoice-label { font-size: 32px; font-weight: 100; color: #777; text-align: right; }
        
        .info-table { width: 100%; margin-top: 30px; border-spacing: 0; }
        .info-box { vertical-align: top; width: 50%; font-size: 13px; }
        
        .details-table { width: 100%; border-collapse: collapse; margin-top: 40px; }
        .details-table th { background: #f4f4f4; color: #333; padding: 12px 10px; text-align: left; border-bottom: 2px solid #ddd; font-size: 13px; }
        .details-table td { padding: 12px 10px; border-bottom: 1px solid #eee; font-size: 13px; vertical-align: top; }
        
        .totals-table { width: 35%; margin-left: 65%; margin-top: 30px; border-collapse: collapse; }
        .totals-table td { padding: 8px 5px; font-size: 13px; }
        .grand-total-row { font-weight: bold; font-size: 16px; background: #f9f9f9; }
        .grand-total-row td { border-top: 2px solid #333; padding-top: 10px; }
    </style>
</head>
<body>
<div class="container">
    <table class="header-table">
        <tr>
            <td class="brand">Haarlem Festival</td>
            <td class="invoice-label">INVOICE</td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td class="info-box">
                <strong style="color: #d32f2f;">BILL TO:</strong><br>
                <div style="font-size: 16px; font-weight: bold; margin-top: 5px;">
                    <?= htmlspecialchars($customer->fullName) ?>
                </div>
                <?= htmlspecialchars($customer->email) ?><br>
                <?= htmlspecialchars($customer->phoneNumber ?? '') ?>
            </td>
            <td class="info-box" style="text-align: right;">
                <strong>Invoice #:</strong> INV-<?= $orderId ?><br>
                <strong>Invoice Date:</strong> <?= date('d-m-Y') ?><br>
                <strong>Payment Date:</strong> <?= date('d-m-Y') ?><br>
                <strong>Status:</strong> <span style="color: #28a745; font-weight: bold;">PAID</span>
            </td>
        </tr>
    </table>

    <table class="details-table">
        <thead>
            <tr>
                <th>Description</th>
                <th style="text-align: center;">Qty</th>
                <th style="text-align: right;">Unit (Excl.)</th>
                <th style="text-align: right;">Total (Excl.)</th>
                <th style="text-align: right;">VAT</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                $totalExclVat = 0;
                foreach ($tickets as $ticket): 
                    $itemTotalExcl = $ticket->totalPrice / 1.09;
                    $totalExclVat += $itemTotalExcl;
                ?>
                    <tr>
                        <td><?= htmlspecialchars($ticket->title) ?></td>
                        <td style="text-align: center;"><?= $ticket->guestCount ?></td>
                        <td style="text-align: right;">&euro;<?= number_format($ticket->unitPrice / 1.09, 2) ?></td>
                        <td style="text-align: right;">&euro;<?= number_format($itemTotalExcl, 2) ?></td>
                        <td style="text-align: right;">9%</td>
                    </tr>
                <?php endforeach; 
                $totalVat = $totalExclVat * 0.09;
                ?>
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td>Subtotal (Excl. VAT):</td>
            <td style="text-align: right;">&euro;<?= number_format($totalExclVat, 2) ?></td>
        </tr>
        <tr>
            <td>VAT Total (9%):</td>
            <td style="text-align: right;">&euro;<?= number_format($totalVat, 2) ?></td>
        </tr>
        <tr class="grand-total-row">
            <td>GRAND TOTAL:</td>
            <td style="text-align: right;">&euro;<?= number_format($totalExclVat + $totalVat, 2) ?></td>
        </tr>
    </table>
    
    <div style="margin-top: 50px; font-size: 11px; color: #999; text-align: center; border-top: 1px solid #eee; padding-top: 20px;">
        Thank you for supporting the Haarlem Festival. This is a computer-generated invoice.
    </div>
</div>
</body>
</html>