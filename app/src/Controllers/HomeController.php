<?php

namespace App\Controllers;

class HomeController
{
    public function __construct()
    {
    }

    public function index(): string
    {
        ob_start();
        require __DIR__ . '/../Views/home/index.php';
        return ob_get_clean();
    }
}

