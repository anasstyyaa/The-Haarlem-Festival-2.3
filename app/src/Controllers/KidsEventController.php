<?php 
namespace App\Controllers;

use App\Services\KidsEventService;
use App\Repositories\KidsEventRepository;
use App\ViewModels\KidsEventViewModel;
use App\Repositories\PageElementRepository;
use App\Repositories\TextRepository;
use App\Repositories\ImageRepository;
use App\ViewModels\PageElementViewModel;

class KidsEventController
{
    private PageElementRepository $pageRepo;
    private TextRepository $textRepo;
    private ImageRepository $imageRepo;
private KidsEventService $service;
    public function __construct()
    {
        $this->service = new KidsEventService(new KidsEventRepository);
         $this->pageRepo  = new PageElementRepository();
        $this->textRepo  = new TextRepository();
        $this->imageRepo = new ImageRepository();
    }

   public function index(): void
{
      $elements = $this->pageRepo->getByPageName("kids");

        $vm = new PageElementViewModel(
            $this->textRepo,
            $this->imageRepo
        );

        $vm->build($elements);
    $kidsEvents = $this->service->getAll();
   //  var_dump($kidsEvents);
//    var_dump($elements);
// die();
//     foreach ($vm->getSections() as $section => $elements) {
//     foreach ($elements as $element) {
//         echo get_class($element) . ' -> ';
//         echo $element instanceof \App\Models\ImageModel
//             ? $element->getImgURL()
//             : $element->getContent();
//         echo "\n";
//     }
// }
// die();
    $vmKids = new KidsEventViewModel($kidsEvents);
   require __DIR__ . '/../Views/event/kidsEvent.php';
}

}
