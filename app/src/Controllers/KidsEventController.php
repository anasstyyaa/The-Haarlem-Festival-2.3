<?php 
namespace App\Controllers;

use App\Services\Interfaces\IKidsEventService;
use App\ViewModels\KidsEventViewModel;
use App\Models\KidsEventModel;

use App\Services\Interfaces\IPageElementService;
use App\ViewModels\PageElementViewModel;

use App\Services\Interfaces\IExtraKidsEventService;
use App\Models\ExtraKidsEventModel;
use App\ViewModels\ExtraKidsEventViewModel;

class KidsEventController
{
    private IPageElementService $pageService;
    private IKidsEventService $kidsService;
    private IExtraKidsEventService $extraKidsService;
    public function __construct(IPageElementService $pageService, IKidsEventService $kidsService, IExtraKidsEventService $extraKidsService)
    {
        $this->kidsService = $kidsService;
        $this->pageService  = $pageService;
        $this->extraKidsService = $extraKidsService;
    }

private function buildPageVM(string $pageName): PageElementViewModel
{
    $sections = $this->pageService->getPageSections($pageName);
    return new PageElementViewModel($sections);
}public function index(): void
{
    try {
        $vm = $this->buildPageVM('kids');
        $kidsEvents = $this->kidsService->getAll();
        $vmKids = new KidsEventViewModel($kidsEvents);

        $extraEvents = $this->extraKidsService->getAllEvents();
        $extraViewModel = new ExtraKidsEventViewModel($extraEvents);

        require __DIR__ . '/../Views/event/kidsEvent.php';

    } catch (\Throwable $e) {
        http_response_code(500);
        echo "Error loading kids events.";
    }
}

public function adminIndex(): void
{
    try {
        $vm = $this->buildPageVM('kids');
        $kidsEvents = $this->kidsService->getAll();
        $vmKids = new KidsEventViewModel($kidsEvents);

        $extraEvents = $this->extraKidsService->getAllEvents();
        $extraViewModel = new ExtraKidsEventViewModel($extraEvents);

        require __DIR__ . '/../Views/admin/kids/index.php';

    } catch (\Throwable $e) {
        http_response_code(500);
        echo "Error loading admin kids events.";
    }
}

public function create(): void
{
    try {
        require __DIR__ . '/../Views/admin/kids/kidsEventForm.php';
    } catch (\Throwable $e) {
        http_response_code(500);
        echo "Error loading form.";
    }
}

public function edit(array $vars): void
{
    try {
        $id = (int)$vars['id'];
        $event = $this->kidsService->getEventById($id);

        require __DIR__ . '/../Views/admin/kids/kidsEventForm.php';

    } catch (\Throwable $e) {
        http_response_code(500);
        echo "Error editing event.";
    }
}

public function save(): void
{
    try {
        $id = $_POST['id'] ?? null;
        $startTime = $_POST['startTime'];
        $endTime   = $_POST['endTime'];
        $type      = $_POST['type'];
        $location  = $_POST['location'];
        $limit     = (int)($_POST['limit']);
        $eventDate = $_POST['eventDate'];

        $day = date('l', strtotime($eventDate));

        $event = new KidsEventModel(
            $id ? (int)$id : 0,
            $day,
            $startTime,
            $endTime,
            $type,
            $location,
            $limit,
            $eventDate
        );

        if ($id) {
            $this->kidsService->update($event);
        } else {
            $this->kidsService->create($event);
        }

        header("Location: /admin/kidsPage");
        exit;

    } catch (\Throwable $e) {
        http_response_code(500);
        echo "Error saving event.";
    }
}

public function delete(): void
{
    try {
        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            throw new \Exception("Invalid ID");
        }

        $this->kidsService->delete($id);

        header("Location: /admin/kidsPage");
        exit;

    } catch (\Throwable $e) {
        http_response_code(500);
        echo "Error deleting event.";
    }
}

public function detail($vars)
{
    try {
        $id = (int)$vars['id'];
        $event = $this->extraKidsService->getEventById($id);

        if (!$event) {
            http_response_code(404);
            echo "Event not found";
            return;
        }

        include __DIR__ . '/../Views/event/extraKidsEventDetail.php';

    } catch (\Throwable $e) {
        http_response_code(500);
        echo "Error loading event details.";
    }
}

public function createExtra(): void
{
    try {
        require __DIR__ . '/../Views/admin/kids/extraKidsEventForm.php';
    } catch (\Throwable $e) {
        http_response_code(500);
        echo "Error loading extra form.";
    }
}

public function storeExtra()
{
    try {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $id = $_POST['id'] ?? null;

            $event = $id
                ? $this->extraKidsService->getEventById((int)$id)
                : new ExtraKidsEventModel();

            $event->setName($_POST['name']);
            $event->setDescription($_POST['description']);

            if (!empty($_FILES['image']['name'])) {
                $uploadDir = __DIR__ . '/../../public/assets/images';

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileName = time() . '_' . basename($_FILES['image']['name']);
                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $event->setImageUrl('/assets/images/' . $fileName);
                }
            }

            if ($id) {
                $this->extraKidsService->updateEvent($event);
            } else {
                $this->extraKidsService->createEvent($event);
            }

            header('Location: /admin/kidsPage');
            exit;
        }

    } catch (\Throwable $e) {
        http_response_code(500);
        echo "Error saving extra event.";
    }
}

public function deleteExtra(): void
{
    try {
        $id = (int)($_POST['id']);
        $this->extraKidsService->deleteEvent($id);

        header('Location: /admin/kidsPage');
        exit;

    } catch (\Throwable $e) {
        http_response_code(500);
        echo "Error deleting extra event.";
    }
}

public function editExtra(array $vars): void
{
    try {
        $id = (int)$vars['id'];
        $event = $this->extraKidsService->getEventById($id);

        require __DIR__ . '/../Views/admin/kids/extraKidsEventForm.php';

    } catch (\Throwable $e) {
        http_response_code(500);
        echo "Error editing extra event.";
    }
}
}
