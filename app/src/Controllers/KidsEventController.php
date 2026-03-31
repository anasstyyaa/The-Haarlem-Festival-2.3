<?php 
namespace App\Controllers;

use App\Services\KidsEventService;
use App\Repositories\KidsEventRepository;
use App\ViewModels\KidsEventViewModel;
use App\Models\KidsEventModel;
use App\Services\PageElementService;
use App\Repositories\TextRepository;
use App\Repositories\ImageRepository;
use App\ViewModels\PageElementViewModel;
use App\Services\ButtonService;

use App\Services\ExtraKidsEventService;
use App\Models\ExtraKidsEventModel;
use App\ViewModels\ExtraKidsEventViewModel;

class KidsEventController
{
    private PageElementService $pageService;
    private KidsEventService $service;
    private ExtraKidsEventService $extraKidsService;
    public function __construct()
    {
        $this->service = new KidsEventService(new KidsEventRepository);
       $this->pageService  = new PageElementService();
        $this->extraKidsService = new ExtraKidsEventService();
    }

   public function index(): void
{
     $vm = $this->buildPageVM('kids');

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
      $extraEvents = $this->extraKidsService->getAllEvents();
        $extraViewModel = new ExtraKidsEventViewModel($extraEvents);
   require __DIR__ . '/../Views/event/kidsEvent.php';
}
private function buildPageVM(string $pageName): PageElementViewModel
{
    $sections = $this->pageService->getPageSections($pageName);
    return new PageElementViewModel($sections);
}
 public function adminIndex(): void
{
    $vm = $this->buildPageVM('kids');
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
 public function detail($vars)
    {
        $id = (int)$vars['id'];
        $event = $this->extraKidsService->getEventById($id);

        if (!$event) {
            http_response_code(404);
            echo "Event not found";
            return;
        }

        include __DIR__ . '/../Views/event/extraKidsEventDetail.php';
    }

    public function storeExtra()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $event = new ExtraKidsEventModel();
            $event->setName($_POST['name'] ?? '');
            $event->setDescription($_POST['description'] ?? '');

            if ($this->extraKidsService->createEvent($event)) {
                header('Location: /extrakids');
                exit;
            }
        }
    }

    public function deleteExtra($vars)
    {
        $this->extraKidsService->deleteEvent((int)$vars['id']);
        header('Location: /extrakids');
        exit;
    }

}
