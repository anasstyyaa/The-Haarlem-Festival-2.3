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
    private TextRepository $textRepo;
    private ImageRepository $imageRepo;
    private ButtonService $buttonService;

    public function __construct()
{
    $this->pageService = new IPageElementService();
    $this->textRepo = new TextRepository();
    $this->imageRepo = new ImageRepository();
    $this->buttonService = new ButtonService();
}

    public function index()
    {
       $elements = $this->pageService->getByPageName("home");

         $vm = new PageElementViewModel(
           $this->textRepo,
           $this->imageRepo,
           $this->buttonService);

        $vm->build($elements);

      require __DIR__ . '/../Views/home/index.php';
    }
    public function adminIndex(): void
{
      $elements = $this->pageService->getByPageName("home");

        $vm = new PageElementViewModel(
            $this->textRepo,
            $this->imageRepo,
            $this->buttonService
        );

        $vm->build($elements);
   require __DIR__ . '/../Views/admin/home/index.php';
}
}

