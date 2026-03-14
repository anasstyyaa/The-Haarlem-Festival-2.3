<?php

namespace App\Services;
use App\Models\HistoryVenueModel;
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
    public function getAllVenues(): array
    {
        return $this->historyVenueRepo->getAll();
    }

    public function getVenueById(int $venueId): ?HistoryVenueModel
    {
        return $this->historyVenueRepo->getById($venueId);
    }

    public function createVenue(HistoryVenueModel $venue): bool
    {
        return $this->historyVenueRepo->create($venue);
    }

    public function updateVenue(HistoryVenueModel $venue): bool
    {
        return $this->historyVenueRepo->update($venue);
    }

    public function deleteVenue(int $venueId): bool
    {
        return $this->historyVenueRepo->delete($venueId);
    }
}
