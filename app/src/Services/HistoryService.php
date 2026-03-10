<?php

namespace App\Services;

use App\Repositories\Interfaces\IHistoryEventRepository;
use App\Repositories\Interfaces\IHistoryVenueRepository;
use App\Services\Interfaces\IHistoryService;
use App\Models\HistoryEventModel;

class HistoryService implements IHistoryService
{
    private IHistoryEventRepository $historyEventRepo;
    private IHistoryVenueRepository $historyVenueRepo;

    public function __construct(
        IHistoryEventRepository $historyEventRepo,
        IHistoryVenueRepository $historyVenueRepo
    ) {
        $this->historyEventRepo = $historyEventRepo;
        $this->historyVenueRepo = $historyVenueRepo;
    }

    // Returns all history tour sessions
    // Used by the History page to populate the day/time/language selector
    public function getAllSessions(): array
    {
        return $this->historyEventRepo->getAll();
    }

    
    public function getSessionByEventId(int $eventId): ?HistoryEventModel
    {
        return $this->historyEventRepo->getByEventId($eventId);
    }

    

}