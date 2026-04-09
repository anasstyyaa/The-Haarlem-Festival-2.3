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

    public function index(): void
    {
        try {
            $vm = $this->buildPageVM("home");
            require __DIR__ . '/../Views/home/index.php';

        } catch (\Throwable $e) {
            http_response_code(500);
            echo "Error loading homepage.";
        }
    }

    private function buildPageVM(string $pageName): PageElementViewModel
    {
        try {
            $sections = $this->pageService->getPageSections($pageName);
            return new PageElementViewModel($sections);

        } catch (\Throwable $e) {

            return new PageElementViewModel([]);
        }
    }

    public function adminIndex(): void
    {
        try {
            $sections = $this->pageService->getPageSections("home");
            $vm = new PageElementViewModel($sections);

            require __DIR__ . '/../Views/admin/home/index.php';

        } catch (\Throwable $e) {
            http_response_code(500);
            echo "Error loading admin homepage.";
        }
    }
}