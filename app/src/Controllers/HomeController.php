<?php

namespace App\Controllers;

class HomeController
{
    public function home($vars = [])
    {
        require __DIR__ . '/../Views/home/index.php';

    }
}
