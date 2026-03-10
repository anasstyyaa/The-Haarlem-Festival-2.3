<?php

namespace App\Controllers;

use App\Repositories\HistoryEventRepository;
use App\Repositories\HistoryVenueRepository;
use App\Services\HistoryService;
use App\Services\PersonalProgramService;
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

    public function index(): void
    {
       
        $sessions = $this->service->getAllSessions();
        include __DIR__ . '/../Views/event/historyEvent/index.php';
    }

    public function book(): void
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
        header('Location: /history');
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