<?php

namespace App\Repositories\Interfaces;

use App\Models\HistoryVenueModel;

interface IHistoryVenueRepository
{
    public function getAll(): array;
    public function getById(int $venueId): ?HistoryVenueModel;
    public function create(HistoryVenueModel $venue): bool;
    public function update(HistoryVenueModel $venue): bool;
    public function delete(int $venueId): bool;

    public function getStopsByEventId(int $eventId): array;
}