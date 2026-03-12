<?php

namespace App\Controllers;

use App\Repositories\HistoryEventRepository;
use App\Repositories\HistoryVenueRepository;
use App\Services\HistoryService;
use App\Services\PersonalProgramService;
use App\Models\HistoryVenueModel;
// use App\Repositories\PageElementRepository;
// use App\Repositories\TextRepository;
// use App\Repositories\ImageRepository;
// use App\ViewModels\PageElementViewModel;

class HistoryController
{
    private HistoryService $service;
    private PersonalProgramService $programService;

    // private PageElementRepository $pageRepo;
    // private TextRepository $textRepo;
    // private ImageRepository $imageRepo;

    public function __construct()
    {
        $this->service = new HistoryService(
            new HistoryEventRepository(),
            new HistoryVenueRepository()
        );

        $this->programService = new PersonalProgramService();

        // Not needed right now because didnt implement editable cms content yet
        // $this->pageRepo = new PageElementRepository();
        // $this->textRepo = new TextRepository();
        // $this->imageRepo = new ImageRepository();
    }
    private function requireAdmin(): void
    {
        if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'Admin') {
            http_response_code(403);
            echo 'Forbidden';
            exit;
        }
    }
    //loads the history page and all available tour sessions.
    public function index(): void
    {

        $sessions = $this->service->getAllSessions();
        include __DIR__ . '/../Views/event/historyEvent/index.php';
    }

    public function book(): void   // adds the selected history tour to the personal program
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /history');
            exit;
        }

        // Get form values
        $eventId = (int)($_POST['eventId'] ?? 0);
        $individualCount = (int)($_POST['individualCount'] ?? 0);
        $familyCount = (int)($_POST['familyCount'] ?? 0);

        // Convert tickets into total number of people, family ticket = 4 people.
        $numberOfPeople = $individualCount + ($familyCount * 4);

        if ($eventId <= 0 || $numberOfPeople <= 0) {  //to prevent empty or invalid form submissions
            header('Location: /history');
            exit;
        }

        $userId = $_SESSION['user_id'] ?? ($_SESSION['user']['id'] ?? null);
        $this->programService->addTicketToProgram(
            $eventId,
            $numberOfPeople,
            $userId
        );
        $_SESSION['flash_success'] = "Your booking for $numberOfPeople people has been successfully added to your Personal Program.";
        header('Location: /history');
        exit;
    }
    public function adminVenues(): void
    {
        $this->requireAdmin();
        $venues = $this->service->getAllVenues();
        include __DIR__ . '/../Views/admin/history/venues/index.php';
    }
    public function createVenue(): void
    {
        $this->requireAdmin();
        include __DIR__ . '/../Views/admin/history/venues/create.php';
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
            include __DIR__ . '/../Views/admin/history/venues/create.php';
            return;
        }

        $venue = new HistoryVenueModel(
            0,
            $venueName,
            $details !== '' ? $details : null,
            $location !== '' ? $location : null,
            null
        );

        if ($this->service->createVenue($venue)) {
            header('Location: /admin/history/venues');
            exit;
        }

        $error = 'Failed to create venue.';
        include __DIR__ . '/../Views/admin/history/venues/create.php';
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

    include __DIR__ . '/../Views/admin/history/venues/edit.php';
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
        include __DIR__ . '/../Views/admin/history/venues/edit.php';
        return;
    }

    $venue = new HistoryVenueModel(
        $venueId,
        $venueName,
        $details !== '' ? $details : null,
        $location !== '' ? $location : null,
        $existingVenue->getImageId()
    );

    if ($this->service->updateVenue($venue)) {
        header('Location: /admin/history/venues');
        exit;
    }

    $error = 'Failed to update venue.';
    include __DIR__ . '/../Views/admin/history/venues/edit.php';
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
    /* detail page
   
    public function show($vars): void
    {   
        $eventId = (int)$vars['id'];
        $session = $this->service->getSessionByEventId($eventId);
        $stops = $this->service->getStopsByEventId($eventId);

        include __DIR__ . '/../Views/event/historyEvent/show.php';
    }
    */
}
