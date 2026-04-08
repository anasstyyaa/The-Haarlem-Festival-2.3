<?php

namespace App\Controllers;

use App\Models\HistoryVenueModel;
use App\Repositories\ImageRepository;
use App\Services\ButtonService;
use App\Services\Interfaces\IHistoryService;
use App\Services\Interfaces\IPageElementService;
use App\Services\Interfaces\IPersonalProgramService;
use App\Services\PageElementService;
use App\ViewModels\PageElementViewModel;

class HistoryController
{
    private IHistoryService $service;
    private IPersonalProgramService $programService;

    private IPageElementService $pageService;
    private ImageRepository $imageRepo;
    private ButtonService $buttonService;

    public function __construct(IHistoryService $service, IPersonalProgramService $programService, IPageElementService $pageService)
    {
        $this->service = $service;
        $this->programService = $programService;

        $this->pageService = $pageService;
        $this->imageRepo = new ImageRepository();
        $this->buttonService = new ButtonService();
    }

    private function requireAdmin(): void
    {
        if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'Admin') {
            http_response_code(403);
            echo 'Forbidden';
            exit;
        }
    }

    private function handleImageUpload(string $inputName, string $prefix): ?string
    {
        if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $uploadDir = __DIR__ . '/../../public/assets/images/history/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $extension = strtolower(pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION));
        $newFileName = uniqid($prefix . '_', true) . '.' . $extension;

        if (move_uploaded_file($_FILES[$inputName]['tmp_name'], $uploadDir . $newFileName)) {
            return $newFileName;
        }

        return null;
    }

    private function buildPageVM(string $pageName): PageElementViewModel
    {
        $sections = $this->pageService->getPageSections($pageName);
        return new PageElementViewModel($sections);
    }

    public function index(): void
    {
        $vm = $this->buildPageVM('History');
        $sessions = $this->service->getAllSessions();
        $venues = $this->service->getAllVenues();

        require __DIR__ . '/../Views/event/historyEvent/index.php';
    }

    public function adminIndex(): void
    {
        $this->requireAdmin();

        $vm = $this->buildPageVM('History');
        $venues = $this->service->getAllVenues();
        $tours = $this->service->getAllSessions();
        $pageName = 'History';

        require __DIR__ . '/../Views/admin/history/index.php';
    }

    public function booking(): void
{
    $sessions = $this->service->getAllSessions();
    $stops = [];

    foreach ($sessions as $session) {
        $candidateStops = $this->service->getStopsByEventId($session->getEventId());
        if (!empty($candidateStops)) {
            $stops = $candidateStops;
            break;
        }

    }

    require __DIR__ . '/../Views/event/historyEvent/booking.php';
}

    public function book(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /history/booking');
            exit;
        }

        $eventId = (int)($_POST['eventId'] ?? 0);
        $individualCount = (int)($_POST['individualCount'] ?? 0);
        $familyCount = (int)($_POST['familyCount'] ?? 0);

        $numberOfPeople = $individualCount + ($familyCount * 4);

        if ($eventId <= 0 || $numberOfPeople <= 0) {
            $_SESSION['error'] = 'Please select a valid tour and at least one ticket.';
            header('Location: /history/booking');
            exit;
        }

        $userId = $_SESSION['user_id'] ?? ($_SESSION['user']['id'] ?? null);

        $this->programService->addTicketToProgram(
            $eventId,
            $numberOfPeople,
            $userId
        );

        $_SESSION['flash_success'] = "Your booking for $numberOfPeople people has been successfully added to your Personal Program.";
        header('Location: /history/booking');
        exit;
    }

    /**
     * Old list page no longer used as main page.
     * Keep route working by redirecting to combined admin page.
     */
    public function adminVenues(): void
    {
        header('Location: /admin/history');
        exit;
    }

    public function createVenue(): void
    {
        $this->requireAdmin();

        require __DIR__ . '/../Views/admin/history/venues/create.php';
    }

    public function storeVenue(): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/history');
            exit;
        }

        $venueName = trim($_POST['venueName'] ?? '');
        $details = trim($_POST['details'] ?? '');
        $location = trim($_POST['location'] ?? '');

        if ($venueName === '') {
            $error = 'Venue name is required.';
            require __DIR__ . '/../Views/admin/history/venues/create.php';
            return;
        }

        $imageId = null;

        $uploadedFileName = $this->handleImageUpload('image', 'history_venue');
        if ($uploadedFileName !== null) {
            $imgURL = '/assets/images/history/' . $uploadedFileName;
            $altText = $venueName;
            $imageId = $this->imageRepo->createImage($imgURL, $altText);
        }

        $venue = new HistoryVenueModel(
            0,
            $venueName,
            $details !== '' ? $details : null,
            $location !== '' ? $location : null,
            $imageId
        );

        if ($this->service->createVenue($venue)) {
            header('Location: /admin/history');
            exit;
        }

        $error = 'Failed to create venue.';
        require __DIR__ . '/../Views/admin/history/venues/create.php';
    }

    public function editVenue(): void
    {
        $this->requireAdmin();

        $venueId = (int)($_GET['id'] ?? 0);
        $venue = $this->service->getVenueById($venueId);

        if (!$venue) {
            header('Location: /admin/history');
            exit;
        }

        require __DIR__ . '/../Views/admin/history/venues/edit.php';
    }

    public function updateVenue(): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/history');
            exit;
        }

        $venueId = (int)($_POST['id'] ?? 0);
        $existingVenue = $this->service->getVenueById($venueId);

        if (!$existingVenue) {
            header('Location: /admin/history');
            exit;
        }

        $venueName = trim($_POST['venueName'] ?? '');
        $details = trim($_POST['details'] ?? '');
        $location = trim($_POST['location'] ?? '');

        if ($venueName === '') {
            $error = 'Venue name is required.';
            $venue = $existingVenue;
            require __DIR__ . '/../Views/admin/history/venues/edit.php';
            return;
        }

        $imageId = $existingVenue->getImageId();

        $uploadedFileName = $this->handleImageUpload('image', 'history_venue');
        if ($uploadedFileName !== null) {
            $imgURL = '/assets/images/history/' . $uploadedFileName;
            $altText = $venueName;
            $imageId = $this->imageRepo->createImage($imgURL, $altText);
        }

        $venue = new HistoryVenueModel(
            $venueId,
            $venueName,
            $details !== '' ? $details : null,
            $location !== '' ? $location : null,
            $imageId
        );

        if ($this->service->updateVenue($venue)) {
            header('Location: /admin/history');
            exit;
        }

        $error = 'Failed to update venue.';
        $venue = $existingVenue;
        require __DIR__ . '/../Views/admin/history/venues/edit.php';
    }

    public function deleteVenue(): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/history');
            exit;
        }

        $venueId = (int)($_POST['id'] ?? 0);

        if ($venueId > 0) {
            $this->service->deleteVenue($venueId);
        }

        header('Location: /admin/history');
        exit;
    }

    /**
     * Old list page no longer used as main page.
     * Keep route working by redirecting to combined admin page.
     */
    public function adminTours(): void
    {
        header('Location: /admin/history');
        exit;
    }

    public function createTour(): void
    {
        $this->requireAdmin();
        $tour = null;

        require __DIR__ . '/../Views/admin/history/tours/create.php';
    }

    public function storeTour(): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/history');
            exit;
        }

        $slotDate = trim($_POST['slotDate'] ?? '');
        $startTime = trim($_POST['startTime'] ?? '');
        $language = trim($_POST['language'] ?? '');
        $duration = (int)($_POST['duration'] ?? 150);
        $minAge = (int)($_POST['minAge'] ?? 12);
        $capacity = (int)($_POST['capacity'] ?? 12);
        $priceIndividual = (float)($_POST['priceIndividual'] ?? 17.50);
        $priceFamily = (float)($_POST['priceFamily'] ?? 60.00);

        if ($slotDate === '' || $startTime === '' || $language === '') {
            $error = 'Date, time and language are required.';
            $tour = null;
            require __DIR__ . '/../Views/admin/history/tours/create.php';
            return;
        }

        $tour = new \App\Models\HistoryEventModel(
            0,
            0,
            $slotDate,
            $startTime,
            $language,
            $duration,
            $minAge,
            $capacity,
            $priceIndividual,
            $priceFamily
        );

        if ($this->service->createSession($tour)) {
            header('Location: /admin/history');
            exit;
        }

        $error = 'Failed to create tour.';
        require __DIR__ . '/../Views/admin/history/tours/create.php';
    }

    public function editTour(): void
    {
        $this->requireAdmin();

        $eventId = (int)($_GET['id'] ?? 0);
        $tour = $this->service->getSessionByEventId($eventId);

        if (!$tour) {
            header('Location: /admin/history');
            exit;
        }

        require __DIR__ . '/../Views/admin/history/tours/edit.php';
    }

    public function updateTour(): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/history');
            exit;
        }

        $eventId = (int)($_POST['eventId'] ?? 0);
        $historyEventId = (int)($_POST['historyEventId'] ?? 0);

        $existingTour = $this->service->getSessionByEventId($eventId);
        if (!$existingTour) {
            header('Location: /admin/history');
            exit;
        }

        $slotDate = trim($_POST['slotDate'] ?? '');
        $startTime = trim($_POST['startTime'] ?? '');
        $language = trim($_POST['language'] ?? '');
        $duration = (int)($_POST['duration'] ?? 150);
        $minAge = (int)($_POST['minAge'] ?? 12);
        $capacity = (int)($_POST['capacity'] ?? 12);
        $priceIndividual = (float)($_POST['priceIndividual'] ?? 17.50);
        $priceFamily = (float)($_POST['priceFamily'] ?? 60.00);

        if ($slotDate === '' || $startTime === '' || $language === '') {
            $error = 'Date, time and language are required.';
            $tour = $existingTour;
            require __DIR__ . '/../Views/admin/history/tours/edit.php';
            return;
        }

        $tour = new \App\Models\HistoryEventModel(
            $eventId,
            $historyEventId,
            $slotDate,
            $startTime,
            $language,
            $duration,
            $minAge,
            $capacity,
            $priceIndividual,
            $priceFamily
        );

        if ($this->service->updateSession($tour)) {
            header('Location: /admin/history');
            exit;
        }

        $error = 'Failed to update tour.';
        require __DIR__ . '/../Views/admin/history/tours/edit.php';
    }

    public function deleteTour(): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/history');
            exit;
        }

        $eventId = (int)($_POST['id'] ?? 0);

        if ($eventId > 0) {
            $this->service->deleteSession($eventId);
        }

        header('Location: /admin/history');
        exit;
    }

    public function detail($vars): void
    {
        $venueId = (int)($vars['id'] ?? 0);

        $venue = $this->service->getVenueById($venueId);

        if (!$venue) {
            http_response_code(404);
            echo "Venue not found";
            return;
        }

        require __DIR__ . '/../Views/event/historyEvent/detail.php';
    }
    public function getStops($vars): void
    {
        $eventId = (int)($vars['id'] ?? 0);

        if ($eventId <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid event id']);
            return;
        }

        $stops = $this->service->getStopsByEventId($eventId);

        header('Content-Type: application/json');
        echo json_encode($stops);
    }
}
