<?php

namespace App\Services\Interfaces;
use App\Models\HistoryVenueModel;
use App\Models\HistoryEventModel;

interface IHistoryService
{
    public function getAllSessions(): array;

    public function getSessionByEventId(int $eventId): ?HistoryEventModel;
    public function getAllVenues(): array;
    public function getVenueById(int $venueId): ?HistoryVenueModel;
    public function createVenue(HistoryVenueModel $venue): bool;
    public function updateVenue(HistoryVenueModel $venue): bool;
    public function deleteVenue(int $venueId): bool;
}
