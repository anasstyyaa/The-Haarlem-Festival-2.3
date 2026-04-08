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

    public function index():void
    {
     $vm = $this->buildPageVM("home");
      require __DIR__ . '/../Views/home/index.php';
    }
     private function buildPageVM(string $pageName): PageElementViewModel
{
    $sections = $this->pageService->getPageSections($pageName);
    return new PageElementViewModel($sections);
}

    public function adminIndex(): void
{
    $sections = $this->pageService->getPageSections("home");
    $vm = new PageElementViewModel($sections);
      require __DIR__ . '/../Views/admin/home/index.php';
}
}

