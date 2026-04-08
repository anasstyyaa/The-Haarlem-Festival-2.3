<?php

namespace App\Controllers;

use App\Services\Interfaces\IPageElementService;
use App\ViewModels\PageElementViewModel;

class HomeController
{
    private IPageElementService $pageService;

    public function __construct(IPageElementService $pageService)
{
    $this->pageService = $pageService;
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

