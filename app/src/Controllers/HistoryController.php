<?php

namespace App\Controllers;

use App\Repositories\HistoryEventRepository;
use App\Repositories\HistoryVenueRepository;
use App\Services\HistoryService;

use App\Repositories\PageElementRepository;
use App\Repositories\TextRepository;
use App\Repositories\ImageRepository;
use App\ViewModels\PageElementViewModel;

class HistoryController
{
    private HistoryService $service;

    private PageElementRepository $pageRepo;
    private TextRepository $textRepo;
    private ImageRepository $imageRepo;

    public function __construct()
    {
        $this->service = new HistoryService(
            new HistoryEventRepository(),
            new HistoryVenueRepository()
        );

        $this->pageRepo  = new PageElementRepository();
        $this->textRepo  = new TextRepository();
        $this->imageRepo = new ImageRepository();
    }

 public function index(): void
{
    $elements = $this->pageRepo->getByPageName("history");

    $pageVm = new PageElementViewModel(
        $this->textRepo,
        $this->imageRepo
    );
    $pageVm->build($elements);

    // schedule
    $sessions = $this->service->getAllSessions();

    // group by day,time, languages
    $byDay = [];
    foreach ($sessions as $s) {
        $day  = $s['slotDate'];                 
        $time = substr($s['startTime'], 0, 5); 
        $lang = $s['language'];

        $byDay[$day][$time][] = $lang;
    }

    // selections from the query parameters
    $selectedDay  = $_GET['day'] ?? null;
    $selectedTime = $_GET['time'] ?? null;
    $selectedLang = $_GET['lang'] ?? null;

    // load selected event/stops only when all 3 are chosen
    $selectedEventId = null;
    $selectedSession = null;
    $stops = [];

    if ($selectedDay && $selectedTime && $selectedLang) {
        $selectedEventId = $this->service->getEventIdBySlot($selectedDay, $selectedTime, $selectedLang);

        if ($selectedEventId) {
            $selectedSession = $this->service->getSessionByEventId($selectedEventId);
            $stops = $this->service->getStopsByEventId($selectedEventId);
        }
    }

    $venues = $this->service->getAllVenues();

    require __DIR__ . '/../Views/event/historyEvent/index.php';
}
}
