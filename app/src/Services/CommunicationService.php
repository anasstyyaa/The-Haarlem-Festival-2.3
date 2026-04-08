<?php

namespace App\Services;

use App\Services\Interfaces\ICommunicationService;
use App\ViewModels\CustomerViewModel; 
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
        //$mail->SMTPDebug = 2;
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

    public function sendOrderConfirmation(CustomerViewModel $customer, array $tickets, string $orderId): bool
    {
        try { 
            $qrOptions = new QROptions([
            'version'    => 5,
            'eccLevel'   => EccLevel::L,
            'outputType' => 'gdimage_png', // Better for PDF
            'imageBase64' => true,
            ]);
            $qrcode = new QRCode($qrOptions);

            $data = [
                'customer' => $customer, 
                'tickets'  => $tickets, 
                'orderId'  => $orderId,
                'qrcode'   => $qrcode, // <--- Add this line
                'user'     => [
                    'full_name'    => $customer->fullName,
                    'email'        => $customer->email,
                    'phone'        => $customer->phoneNumber ?? '',
                    'invoice_date' => date('d-m-Y'),
                    'payment_date' => date('d-m-Y')
                ]
            ];     
            $invoiceHtml = $this->renderView('pdf/invoice', $data);
            $ticketsHtml = $this->renderView('pdf/tickets', $data);

            $mail = $this->getMailer();
            $mail->addAddress($customer->email, $customer->fullName);
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