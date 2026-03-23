<?php

namespace App\Controllers;

use App\Repositories\PageElementRepository;
use App\Repositories\TextRepository;
use App\Repositories\ImageRepository;
use App\ViewModels\PageElementViewModel;
use App\Services\ButtonService;

  

class HomeController
{
    private PageElementRepository $pageRepo;
    private TextRepository $textRepo;
    private ImageRepository $imageRepo;
    private ButtonService $buttonService;

    public function __construct()
{
    $this->pageRepo = new PageElementRepository();
    $this->textRepo = new TextRepository();
    $this->imageRepo = new ImageRepository();
    $this->buttonService = new ButtonService();
}

    public function index(): string
    {
       $elements = $this->pageRepo->getByPageName("home");

         $vm = new PageElementViewModel(
           $this->textRepo,
           $this->imageRepo,
           $this->buttonService);

        $vm->build($elements);

      require __DIR__ . '/../Views/home/index.php';
    }
    public function adminIndex(): void
{
      $elements = $this->pageRepo->getByPageName("home");

        $vm = new PageElementViewModel(
            $this->textRepo,
            $this->imageRepo,
            $this->buttonService
        );

        $vm->build($elements);
   require __DIR__ . '/../Views/admin/home/index.php';
}
}

