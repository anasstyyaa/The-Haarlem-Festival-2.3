<?php

namespace App\Controllers;

class HomeController
{
    public function __construct()
    {
    }

    public function index(): string
    {
       $elements = $this->pageRepo->getByPageName("home");

$vm = new PageElementViewModel(
    $this->textRepo,
    $this->imageRepo
);

$vm->build($elements);

require __DIR__ . '/../Views/home/index.php';
    }
}

