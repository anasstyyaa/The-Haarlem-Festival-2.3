<?php
namespace App\Controllers;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QrController
{
    public function index(): void
    {
        $text = $_GET['text'] ?? 'test';

        $options = new QROptions([
            'scale' => 5,
            'outputBase64' => false,
        ]);

        header('Content-Type: image/svg+xml');
        echo (new QRCode($options))->render($text);
        exit;
    }
}