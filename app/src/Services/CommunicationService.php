<?php

namespace App\Services;

use App\Services\Interfaces\ICommunicationService;
use Dompdf\Dompdf;
use Dompdf\Options;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Common\EccLevel;
//use chillerlan\QRCode\Output\QROutputInterface;


class CommunicationService implements ICommunicationService
{
    private function getPdfEngine(): Dompdf
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true); // helpful for loading external images (like QR codes)
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
            
            $qty = $ticket->getNumberOfPeople() ?: 1;
            $unitPriceIncl = $ticket->getUnitPrice();
            $lineTotalIncl = $ticket->getTotalPrice();

            // Assumption: 9% VAT for Festival/Food, 21% for others. 
            $vatRate = 0.09; 
            $vatAmount = $lineTotalIncl - ($lineTotalIncl / (1 + $vatRate));
            $lineExcl = $lineTotalIncl - $vatAmount;

            $totalExclVat += $lineExcl;
            $totalVat += $vatAmount;

            $eventName = $this->getEventNameFromTicket($ticket);
            $location = $this->getEventAddress($ticket);

            $rows .= "
            <tr>
                <td style='padding: 10px; border-bottom: 1px solid #eee;'>
                    <div style='font-weight: bold;'>{$eventName}</div>
                    <div style='font-size: 0.85em; color: #666;'>Loc: {$location}</div>
                </td>
                <td style='padding: 10px; border-bottom: 1px solid #eee;'>{$qty}</td>
                <td style='padding: 10px; border-bottom: 1px solid #eee;'>&euro;" . number_format($lineExcl / $qty, 2) . "</td>
                <td style='padding: 10px; border-bottom: 1px solid #eee;'>&euro;" . number_format($lineExcl, 2) . "</td>
                <td style='padding: 10px; border-bottom: 1px solid #eee; text-align:right;'>" . ($vatRate * 100) . "%</td>
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
        
        // configuring QR options for high-quality PDF output
        $options = new QROptions([
        'version'             => 5,
        'outputType'          => 'gdimage_png',
        'eccLevel'            => EccLevel::L, 
        'addQuietzone'        => true,
        'imageBase64'         => true, 
    ]);

        foreach ($tickets as $ticket) {
            $eventName = $this->getEventNameFromTicket($ticket);
            $token = $ticket->getUniqueTicketToken();
            $eventDate = $this->getEventDateDisplay($ticket);

            // generating the secure URL data
            $qrData = "https://yourdomain.com/scan?token=" . $token;

            // generating the QR code as an SVG string directly
            $qrcode = new QRCode($options);
            $qrCodeMarkup = $qrcode->render($qrData);

            $ticketSections .= "
                <div style='border: 2px dashed #444; padding: 20px; margin-bottom: 30px; font-family: sans-serif;'>
                    <table style='width: 100%;'>
                        <tr>
                            <td style='vertical-align: top; width: 70%;'>
                                <h2 style='margin: 0; color: #d32f2f;'>HAARLEM FESTIVAL</h2>
                                <p style='font-size: 18px; margin: 10px 0;'><strong>Event:</strong> {$eventName}</p>
                                <p><strong>Date/Time:</strong> {$eventDate}</p>
                                <p><strong>Attendee:</strong> {$user['full_name']}</p>
                                <p><strong>Quantity:</strong> {$ticket->getNumberOfPeople()} Person(s)</p>
                            </td>
                            <td style='text-align: right; width: 30%;'>
                                <img src='{$qrCodeMarkup}' width='150' height='150' />
                                <div style='font-size: 9px; color: #666; margin-top: 5px;'>Scan to Validate</div>
                            </td>
                        </tr>
                    </table>
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

    // private function getEventNameFromTicket($ticket): string
    // {
    //     $details = $ticket->getEvent()->getDetails();
        
    //     if (is_array($details)) {
    //         if (isset($details['artist']) && is_object($details['artist'])) {
    //             return "Jazz: " . $details['artist']->getName();
    //         }
    //         return $details['name'] ?? ($details['title'] ?? 'Festival Event');
    //     }

    //     if (is_object($details)) {
    //         if (method_exists($details, 'getName')) {
    //             return $details->getName();
    //         }
    //         if (method_exists($details, 'getType')) {
    //             return $details->getType();
    //         }
    //     }

    //     return $ticket->getEvent()->getEventType()->value . " Ticket";
    // }

    private function getEventNameFromTicket($ticket): string 
    {
        $event = $ticket->getEvent();
        $details = $event->getDetails();
        $type = strtolower($event->getEventType()->value);

        // 1. Handle Restaurant Reservations
        if (($type === 'reservation') && is_object($details)) {
            $restaurantName = $details->getName() ?? 'Restaurant';
            $session = $details->getSessionData(); 
            
            if ($session && method_exists($session, 'getStartTime')) {
                $startStr = $session->getStartTime();
                $duration = $details->getSessionDuration() ?: 90;

                $startTimeObj = new \DateTime($startStr);
                $endTimeObj = clone $startTimeObj;
                $endTimeObj->modify("+{$duration} minutes");

                return "Reservation: {$restaurantName} (" . $startTimeObj->format('H:i') . " - " . $endTimeObj->format('H:i') . ")";
            }
            return "Reservation: {$restaurantName}";
        }
        
        // 2. Handle Jazz Events
        // if ($type === 'jazz') {
        //     // Based on your populateTicketDetails, Jazz details is an array ['artist' => ..., 'jazzEvent' => ...]
        //     if (is_array($details) && isset($details['artist'])) {
        //         $artistName = $details['artist']->getName();
        //         $jazzEvent = $details['jazzEvent']; // This is your JazzEventModel
                
        //         if ($jazzEvent && method_exists($jazzEvent, 'getStartTime')) {
        //             $time = (new \DateTime($jazzEvent->getStartTime()))->format('H:i');
        //             return "Jazz: {$artistName} at {$time}";
        //         }
        //         return "Jazz: {$artistName}";
        //     }
        // }

        // 3. Fallback for JazzPass or unknown types
        return ucfirst($type) . " Ticket";
    }

    private function getEventAddress($ticket): string 
    {
        $details = $ticket->getEvent()->getDetails();
        $type = $ticket->getEvent()->getEventType()->value;

        if ($type === 'reservation' || $type === 'food') {

            if (is_object($details) && method_exists($details, 'getLocation')) {
                return $details->getLocation() ?? 'Haarlem Restaurant';
            }
            
            if (is_array($details) && isset($details['location'])) {
                return $details['location'];
            }
        }

        return match($type) {
            'jazz'     => 'Grote Markt, 2011 RD Haarlem',
            'jazzpass' => 'Festival Access - Various Locations',
            'history'  => 'Church of St. Bavo (Meeting Point)',
            default    => 'Haarlem Festival Venue'
        };
    }

    private function getEventDateDisplay($ticket): string
    {
        $details = $ticket->getEvent()->getDetails();
        $type = strtolower($ticket->getEvent()->getEventType()->value);

        if ($type === 'reservation' && is_object($details)) { //add for kids
            $session = $details->getSessionData();
            return $session ? (new \DateTime($session->getStartTime()))->format('l, d F Y') : 'Date TBD';
        }

        if ($type === 'jazz' && is_array($details) && isset($details['jazzEvent'])) {
            return (new \DateTime($details['jazzEvent']->getStartDateTime()))->format('l, d F Y');
        }

        return "Check Schedule for Details";
    }
    public function sendAccountChangeNotification(array $userData, array $changedFields): bool
{
    try {
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = 'mailpit';
        $mail->Port = 1025;
        $mail->SMTPAuth = false;

        $mail->setFrom('noreply@haarlemfestival.com', 'Haarlem Festival');
        $mail->addAddress($userData['email'], $userData['full_name']);

        $mail->isHTML(true);
        $mail->Subject = 'Your Haarlem Festival account details were changed';

        $changesHtml = '<ul style="margin: 10px 0 20px 20px;">';
        foreach ($changedFields as $field) {
            $changesHtml .= '<li>' . htmlspecialchars($field) . '</li>';
        }
        $changesHtml .= '</ul>';

        $mail->Body = "
            <div style='font-family: sans-serif; line-height: 1.6; color: #333;'>
                <h2>Hello {$userData['full_name']},</h2>
                <p>The following account details were recently updated:</p>
                {$changesHtml}
                <p>If you made this change, you do not need to do anything.</p>
                <p>If you did <strong>not</strong> make this change, please reset your password immediately and contact support.</p>
                <p>Regards,<br>Haarlem Festival</p>
            </div>
        ";

        return $mail->send();
    } catch (Exception $e) {
        error_log("Account Change Email Error: " . $e->getMessage());
        return false;
    }
}

}