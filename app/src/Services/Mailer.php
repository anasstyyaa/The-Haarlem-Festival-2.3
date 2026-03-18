<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{
    //sends email link to the user email
    public function send(string $to, string $subject, string $body): void
    {
        $mail = new PHPMailer(true);

        //send email using smtp sesrver
        $mail->isSMTP();
        $mail->Host = 'mailpit';
        $mail->Port = 1025;
        $mail->SMTPAuth = false;

        $mail->setFrom('no-reply@festival.local', 'Haarlem Festival');
        $mail->addAddress($to);


        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
    }
}