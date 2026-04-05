<?php

namespace App\Services\Interfaces;

use App\Models\HistoryVenueModel;
use App\Models\HistoryEventModel;

interface IHistoryService
{
    public function getAllSessions(): array;
    public function getSessionByEventId(int $eventId): ?HistoryEventModel;

    public function createSession(HistoryEventModel $event): bool;
    public function updateSession(HistoryEventModel $event): bool;
    public function deleteSession(int $eventId): bool;

    public function getAllVenues(): array;
    public function getVenueById(int $venueId): ?HistoryVenueModel;
    public function createVenue(HistoryVenueModel $venue): bool;
    public function updateVenue(HistoryVenueModel $venue): bool;
    public function deleteVenue(int $venueId): bool;
    public function getStopsByEventId(int $eventId): array;
        
    }

    