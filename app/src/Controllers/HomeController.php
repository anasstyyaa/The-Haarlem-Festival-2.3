<?php

namespace App\Controllers;

use App\Services\PageElementService;
use App\Services\Interfaces\IPageElementService;
use App\Repositories\TextRepository;
use App\Repositories\ImageRepository;
use App\ViewModels\PageElementViewModel;
use App\Services\ButtonService;

  

class HomeController
{
    private PageElementService $pageService;

    public function __construct()
{
    $this->pageService = new PageElementService();
}

    public function index()
    {
     $sections = $this->pageService->getPageSections("home");

    $vm = new PageElementViewModel($sections);


      require __DIR__ . '/../Views/home/index.php';
    }
    public function adminIndex(): void
{
    $sections = $this->pageService->getPageSections("home");

    $vm = new PageElementViewModel($sections);
      require __DIR__ . '/../Views/admin/home/index.php';
}
}

