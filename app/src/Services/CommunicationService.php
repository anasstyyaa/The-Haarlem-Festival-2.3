<?php

namespace App\Services;

use App\Services\Interfaces\ICommunicationService;
use Dompdf\Dompdf;
use Dompdf\Options;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class CommunicationService implements ICommunicationService
{
    private function getPdfEngine(): Dompdf
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true); // may be useful if we want to add logos via URL
        return new Dompdf($options);
    }

    public function sendOrderConfirmation(array $user, array $tickets, string $orderId): bool
    {
        try {
            // generating Invoice PDF
            $invoiceHtml = $this->renderInvoiceHtml($user, $tickets, $orderId);
            $dompdfInvoice = $this->getPdfEngine();
            $dompdfInvoice->loadHtml($invoiceHtml);
            $dompdfInvoice->setPaper('A4', 'portrait');
            $dompdfInvoice->render();
            $invoiceBinary = $dompdfInvoice->output();

            // generating Tickets PDF 
            $ticketsHtml = $this->renderTicketsHtml($user, $tickets, $orderId);
            $dompdfTickets = $this->getPdfEngine();
            $dompdfTickets->loadHtml($ticketsHtml);
            $dompdfTickets->setPaper('A4', 'portrait');
            $dompdfTickets->render();
            $ticketsBinary = $dompdfTickets->output();

            // preparing the email
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'mailpit'; 
            $mail->Port = 1025;
            $mail->SMTPAuth = false;

            $mail->setFrom('noreply@haarlemfestival.com', 'Haarlem Festival');
            $mail->addAddress($user['email'], $user['full_name']);

            $mail->isHTML(true);
            $mail->Subject = 'Your Haarlem Festival Tickets - Order ' . $orderId;
            $mail->Body = "Dear {$user['full_name']},<br><br>Thank you for your purchase! Please find your <b>Invoice</b> and <b>Tickets</b> attached.";

            // attaching both PDF's 
            $mail->addStringAttachment($invoiceBinary, 'Invoice_' . $orderId . '.pdf');
            $mail->addStringAttachment($ticketsBinary, 'Tickets_' . $orderId . '.pdf');

            return $mail->send();
        } catch (Exception $e) {
            die("Mailer Error: " . $mail->ErrorInfo . " | Exception: " . $e->getMessage());
        }
    }

    private function renderInvoiceHtml(array $user, array $tickets, string $orderId): string
    {
        $rows = "";
        $totalExclVat = 0;
        $totalVat = 0;
        
        foreach ($tickets as $ticket) {
            $details = $ticket->getEvent()->getDetails();
            
            $qty = $ticket->getNumberOfPeople();
            $unitPriceIncl = $ticket->getUnitPrice();
            $lineTotalIncl = $ticket->getTotalPrice();

            // Assumption: 9% VAT for Festival/Food, 21% for others. 
            $vatRate = 0.09; 
            $vatAmount = $lineTotalIncl - ($lineTotalIncl / (1 + $vatRate));
            $lineExcl = $lineTotalIncl - $vatAmount;

            $totalExclVat += $lineExcl;
            $totalVat += $vatAmount;

            if (is_array($details)) {
                $eventName = $details['name'] ?? ($details['title'] ?? 'Festival Event');
            } elseif (is_object($details) && method_exists($details, 'getName')) {
                $eventName = $details->getName();
            } else {
                $eventName = "Event #" . $ticket->getEvent()->getId();
            }

            $rows .= "
                <tr>
                    <td style='padding: 8px; border-bottom: 1px solid #eee;'>{$eventName}</td>
                    <td style='padding: 8px; border-bottom: 1px solid #eee;'>{$qty}</td>
                    <td style='padding: 8px; border-bottom: 1px solid #eee;'>&euro;" . number_format($unitPriceIncl, 2) . "</td>
                    <td style='padding: 8px; border-bottom: 1px solid #eee; text-align:right;'>&euro;" . number_format($lineTotalIncl, 2) . " <br><small>(" . ($vatRate * 100) . "% VAT)</small></td>
                </tr>";
        }

        $grandTotal = $totalExclVat + $totalVat;

        // CSS for Dompdf
        return "
        <html>
        <body style='font-family: sans-serif; color: #333;'>
            <table style='width: 100%;'>
                <tr>
                    <td><h1>INVOICE</h1></td>
                    <td style='text-align: right;'>
                        <strong>Haarlem Festival</strong><br>
                    </td>
                </tr>
            </table>

            <hr>

            <table style='width: 100%; margin-top: 20px;'>
                <tr>
                    <td style='width: 50%; vertical-align: top;'>
                        <strong>Bill To:</strong><br>
                        {$user['full_name']}<br>
                        {$user['email']}<br>
                        {$user['phone']}
                    </td>
                    <td style='width: 50%; text-align: right; vertical-align: top;'>
                        <strong>Invoice #:</strong> INV-{$orderId}<br>
                        <strong>Invoice Date:</strong> {$user['invoice_date']}<br>
                        <strong>Payment Date:</strong> {$user['payment_date']}<br>
                        <strong>Status:</strong> PAID
                    </td>
                </tr>
            </table>

            <table style='width: 100%; border-collapse: collapse; margin-top: 30px;'>
                <thead>
                    <tr style='background: #333; color: white;'>
                        <th style='padding: 10px; text-align: left;'>Description</th>
                        <th style='padding: 10px; text-align: left;'>Qty</th>
                        <th style='padding: 10px; text-align: left;'>Unit Price</th>
                        <th style='padding: 10px; text-align: right;'>Total (Incl. VAT)</th>
                    </tr>
                </thead>
                <tbody>{$rows}</tbody>
            </table>

            <table style='width: 40%; margin-left: 60%; margin-top: 20px;'>
                <tr>
                    <td style='padding: 5px;'>Subtotal (Excl. VAT):</td>
                    <td style='text-align: right;'>&euro;" . number_format($totalExclVat, 2) . "</td>
                </tr>
                <tr>
                    <td style='padding: 5px;'>VAT Total:</td>
                    <td style='text-align: right;'>&euro;" . number_format($totalVat, 2) . "</td>
                </tr>
                <tr style='font-weight: bold; font-size: 1.2em; border-top: 2px solid #333;'>
                    <td style='padding: 5px;'>Grand Total:</td>
                    <td style='text-align: right;'>&euro;" . number_format($grandTotal, 2) . "</td>
                </tr>
            </table>
        </body>
        </html>";
    }

    private function renderTicketsHtml(array $user, array $tickets, string $orderId): string
    {
        $ticketSections = "";
        
        foreach ($tickets as $ticket) {
            $details = $ticket->getEvent()->getDetails();
            
            // logical check for name (same as invoice logic)
            if (is_array($details)) {
                $eventName = $details['name'] ?? ($details['title'] ?? 'Festival Event');
            } elseif (is_object($details) && method_exists($details, 'getName')) {
                $eventName = $details->getName();
            } else {
                $eventName = "Event #" . $ticket->getEvent()->getId();
            }

            $ticketSections .= "
                <div style='border: 2px solid #333; padding: 30px; margin-bottom: 50px; font-family: sans-serif;'>
                    <h1 style='background: #333; color: white; padding: 10px; margin-top: 0;'>HAARLEM FESTIVAL TICKET</h1>
                    <p style='font-size: 20px;'><strong>Event:</strong> {$eventName}</p>
                    <p><strong>Order ID:</strong> #{$orderId}</p>
                    <p><strong>Attendee:</strong> {$user['full_name']}</p>
                    <p><strong>Quantity:</strong> {$ticket->getNumberOfPeople()} Person(s)</p>
                    <hr>
                    <p style='text-align: center; color: #888;'>Scan this ticket at the entrance</p>
                    <div style='height: 100px; background: #eee; text-align: center; line-height: 100px; color: #aaa;'>[ UNIQUE TICKET CODE: " . strtoupper(uniqid()) . " ]</div>
                </div>
                <div style='page-break-after: always;'></div>";
        }

        return "<html><body>$ticketSections</body></html>";
    }

    public function sendPaymentReminder(array $userData, string $orderId): bool
    {
        try {
            $mail = new PHPMailer(true);
            
            // mailpit conf
            $mail->isSMTP();
            $mail->Host = 'mailpit'; 
            $mail->Port = 1025;
            $mail->SMTPAuth = false;

            // recipients 
            $mail->setFrom('noreply@haarlemfestival.com', 'Haarlem Festival');
            $mail->addAddress($userData['email'], $userData['full_name']);

            // content 
            $mail->isHTML(true);
            $mail->Subject = 'Complete your Haarlem Festival Order';
            
            // this link points to a new 'repay' route
            $repayUrl = "http://localhost/repay?orderId=" . $orderId;

            $mail->Body = "
                <div style='font-family: sans-serif; line-height: 1.6;'>
                    <h2>Hi {$userData['full_name']},</h2>
                    <p>It looks like your payment wasn't completed. Don't worry, we've saved your tickets for you!</p>
                    <p>Your reservation is held for <strong>24 hours</strong> from the time of your order.</p>
                    <div style='margin: 30px 0;'>
                        <a href='{$repayUrl}' style='background-color: #28a745; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>
                            Complete My Payment Now
                        </a>
                    </div>
                    <p style='color: #666; font-size: 0.9em;'>If you do not complete the payment within 24 hours, your tickets will be released back to the festival pool.</p>
                    <p>See you at the festival!<br>The Haarlem Festival Team</p>
                </div>
            ";

            return $mail->send();
        } catch (Exception $e) {
            error_log("Reminder Email Error: " . $e->getMessage());
            return false;
        }
    }

}