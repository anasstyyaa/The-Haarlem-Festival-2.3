<?php

namespace App\Controllers;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QrController
{
    public function index(): void
    {
        try {
            $text = trim($_GET['text'] ?? '');

            if ($text === '') {
                $text = 'invalid';
            }

            $options = new QROptions([
                'scale' => 5,
                'outputBase64' => false,
            ]);

            header('Content-Type: image/svg+xml');
            echo (new QRCode($options))->render($text);
        } catch (\Exception $e) {
            error_log('QR error: ' . $e->getMessage());
            http_response_code(500);
            echo 'Failed to generate QR code.';
        }
    }
}
