<?php

namespace App\Controllers;

use App\Models\HistoryVenueModel;
use App\Services\Interfaces\IHistoryService;
use App\Services\Interfaces\IPersonalProgramService;
use App\Repositories\PageElementRepository;
use App\Repositories\TextRepository;
use App\Repositories\ImageRepository;
use App\ViewModels\PageElementViewModel;
use App\Services\ButtonService;

class HistoryController
{
    private IHistoryService $service;
    private IPersonalProgramService $programService;

    private PageElementRepository $pageRepo;
    private TextRepository $textRepo;
    private ImageRepository $imageRepo;
    private ButtonService $buttonService;

    public function __construct(IHistoryService $service, IPersonalProgramService $programService)
    {
        $this->service = $service;
        $this->programService = $programService;

        $this->pageRepo = new PageElementRepository();
        $this->textRepo = new TextRepository();
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

    public function index(): void
    {
        $elements = $this->pageRepo->getByPageName("history");

        $vm = new PageElementViewModel(
            $this->textRepo,
            $this->imageRepo,
            $this->buttonService
        );

        $vm->build($elements);

        $sessions = $this->service->getAllSessions();
        $venues = $this->service->getAllVenues();

        require __DIR__ . '/../Views/event/historyEvent/index.php';
    }

    public function booking(): void
    {
        $sessions = $this->service->getAllSessions();

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

    public function adminVenues(): void
    {
        $this->requireAdmin();
        $venues = $this->service->getAllVenues();

        require __DIR__ . '/../Views/admin/history/venues/index.php';
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
            header('Location: /admin/history/venues');
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

            // This expects ImageRepository to have createImage($imgURL, $altText): int
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
            header('Location: /admin/history/venues');
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
            header('Location: /admin/history/venues');
            exit;
        }

        require __DIR__ . '/../Views/admin/history/venues/edit.php';
    }

    public function updateVenue(): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/history/venues');
            exit;
        }

        $venueId = (int)($_POST['id'] ?? 0);
        $existingVenue = $this->service->getVenueById($venueId);

        if (!$existingVenue) {
            header('Location: /admin/history/venues');
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

            // This expects ImageRepository to have createImage($imgURL, $altText): int
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
            header('Location: /admin/history/venues');
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
            header('Location: /admin/history/venues');
            exit;
        }

        $venueId = (int)($_POST['id'] ?? 0);

        if ($venueId > 0) {
            $this->service->deleteVenue($venueId);
        }

        header('Location: /admin/history/venues');
        exit;
    }
}