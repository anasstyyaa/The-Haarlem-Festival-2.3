<?php

namespace App\Services\Interfaces;

use App\Models\HistoryEventModel;
use App\Models\HistoryVenueModel;

interface IHistoryService
{
    public function getIndexPageData(): array;
    public function getAdminIndexPageData(): array;
    public function getBookingTourPageData(): array;

    public function getAllSessions(): array;
    public function getSessionByEventId(int $eventId): ?HistoryEventModel;
    public function createSession(HistoryEventModel $event): bool;
    public function updateSession(HistoryEventModel $event): bool;
    public function deleteSession(int $eventId): bool;

    public function getAllVenues(): array;
    public function getVenueById(int $venueId): ?HistoryVenueModel;

    public function createVenue(array $post, array $files): HistoryVenueModel;
    public function updateVenue(int $venueId, array $post, array $files): HistoryVenueModel;
    public function deleteVenue(int $venueId): bool;

    public function getStopsByEventId(int $eventId): array;

    public function createBookingFromRequest(array $post, ?int $userId): int;
}