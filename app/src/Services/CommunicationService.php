<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class CommunicationService
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
        $total = 0;
        foreach ($tickets as $ticket) {
            $subtotal = $ticket->getTotalPrice();
            $total += $subtotal;
            
            $details = $ticket->getEvent()->getDetails();
            
            
            if (is_array($details)) {
                $eventName = $details['name'] ?? ($details['title'] ?? 'Festival Event');
            } elseif (is_object($details) && method_exists($details, 'getName')) {
                $eventName = $details->getName();
            } else {
                $eventName = "Event #" . $ticket->getEvent()->getId();
            }

            $rows .= "
                <tr>
                    <td style='padding: 10px; border-bottom: 1px solid #eee;'>{$eventName}</td>
                    <td style='padding: 10px; border-bottom: 1px solid #eee;'>{$ticket->getNumberOfPeople()}</td>
                    <td style='padding: 10px; border-bottom: 1px solid #eee;'>&euro;" . number_format($ticket->getUnitPrice(), 2) . "</td>
                    <td style='padding: 10px; border-bottom: 1px solid #eee;'>&euro;" . number_format($subtotal, 2) . "</td>
                </tr>";
        }

        // CSS for Dompdf
        return "
        <html>
        <body style='font-family: sans-serif;'>
            <h1 style='color: #333;'>INVOICE</h1>
            <p><strong>Order ID:</strong> $orderId</p>
            <p><strong>Customer:</strong> {$user['full_name']}</p>
            <table style='width: 100%; border-collapse: collapse;'>
                <thead>
                    <tr style='background: #f4f4f4;'>
                        <th style='text-align: left; padding: 10px;'>Event</th>
                        <th style='text-align: left; padding: 10px;'>Qty</th>
                        <th style='text-align: left; padding: 10px;'>Price</th>
                        <th style='text-align: left; padding: 10px;'>Total</th>
                    </tr>
                </thead>
                <tbody>$rows</tbody>
            </table>
            <h3 style='text-align: right;'>Grand Total: &euro;" . number_format($total, 2) . "</h3>
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