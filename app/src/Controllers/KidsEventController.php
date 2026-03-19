<?php 
namespace App\Controllers;

use App\Services\KidsEventService;
use App\Repositories\KidsEventRepository;
use App\ViewModels\KidsEventViewModel;
use App\Models\KidsEventModel;
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
 public function adminIndex(): void
{
      $elements = $this->pageRepo->getByPageName("kids");

        $vm = new PageElementViewModel(
            $this->textRepo,
            $this->imageRepo
        );

        $vm->build($elements);
    $kidsEvents = $this->service->getAll();
    $vmKids = new KidsEventViewModel($kidsEvents);
   require __DIR__ . '/../Views/admin/kids/index.php';
}
public function create(): void
{
    $event = null; 
    require __DIR__ . '/../Views/admin/kids/kidsEventForm.php';
}

public function edit(array $vars): void
{
    $id = (int)$vars['id'];
    $event = $this->service->getEventById($id);


    require __DIR__ . '/../Views/admin/kids/kidsEventForm.php';
}

public function save(): void
{
    $id        = $_POST['id'] ?? null;
    $day       = $_POST['day'] ?? '';
    $startTime = $_POST['startTime'] ?? '';
    $endTime   = $_POST['endTime'] ?? '';

    $event = new KidsEventModel(
        $id ? (int)$id : 0,
        $day,
        $startTime,
        $endTime
    );

    if ($id) {
        $this->service->update($event);
    } else {
        $this->service->create($event);
    }

    header("Location: /admin/kidsPage");
    exit;
}

public function delete(): void
{
    $id = (int)($_POST['id'] ?? 0);

    if ($id <= 0) {
        echo "Invalid ID";
        return;
    }

    $this->service->delete($id);

    header("Location: /admin/kidsPage");
    exit;
}

}
