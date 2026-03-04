<?php

namespace App\Services;

use App\Repositories\Interfaces\IHistoryEventRepository;
use App\Repositories\Interfaces\IHistoryVenueRepository;
use App\Services\Interfaces\IHistoryService;

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

    public function getAllSessions(): array
    {
        return $this->historyEventRepo->getAll();
    }

    public function getSelectedEventId(?int $eventIdFromQuery = null): ?int
    {
        $sessions = $this->getAllSessions();
        if (empty($sessions)) {
            return null;
        }

        if ($eventIdFromQuery !== null) {
            foreach ($sessions as $s) {
                if ((int)$s['eventId'] === (int)$eventIdFromQuery) {
                    return (int)$eventIdFromQuery;
                }
            }
        }

        return (int)$sessions[0]['eventId'];
    }

    public function getSessionByEventId(int $eventId): ?array
    {
        return $this->historyEventRepo->getByEventId($eventId);
    }

    public function getStopsByEventId(int $eventId): array
    {
        return $this->historyVenueRepo->getStopsByEventId($eventId);
    }

    public function getAllVenues(): array
    {
        return $this->historyVenueRepo->getAll();
    }
    public function getEventIdBySlot(string $slotDate, string $startTime, string $language): ?int
{
    return $this->historyEventRepo->getEventIdBySlot($slotDate, $startTime, $language);
}
}