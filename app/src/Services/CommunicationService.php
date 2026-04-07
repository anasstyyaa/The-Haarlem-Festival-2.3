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
    private function getMailer(): PHPMailer
    {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'mailpit'; 
        $mail->Port = 1025;
        $mail->SMTPAuth = false;
        $mail->setFrom('noreply@haarlemfestival.com', 'Haarlem Festival');
        return $mail;
    }

    private function generatePdf(string $html): string
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        return $dompdf->output();
    }

    public function sendOrderConfirmation(array $user, array $tickets, string $orderId): bool
    {
        try { 
            $data = [
                'user' => $user, 
                'tickets' => $tickets, 
                'orderId' => $orderId, 
                'service' => $this
            ];        
            $invoiceHtml = $this->renderView('pdf/invoice', $data);
            $ticketsHtml = $this->renderView('pdf/tickets', $data);

            $mail = $this->getMailer();
            $mail->addAddress($user['email'], $user['full_name']);
            $mail->isHTML(true);
            $mail->Subject = "Your Haarlem Festival Tickets - Order $orderId";
            $mail->Body = "Please find your tickets attached.";

            $mail->addStringAttachment($this->generatePdf($invoiceHtml), "Invoice_$orderId.pdf");
            $mail->addStringAttachment($this->generatePdf($ticketsHtml), "Tickets_$orderId.pdf");

            return $mail->send();
        } catch (Exception $e) {
            die("Mailer Error: " . $mail->ErrorInfo . " | Exception: " . $e->getMessage());
        }
    }

    private function renderView(string $path, array $data): string
    {
        extract($data);
        ob_start();
        include __DIR__ . "/../Views/{$path}.php";
        return ob_get_clean();
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

        if ($type === 'reservation' && is_object($details)) {
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