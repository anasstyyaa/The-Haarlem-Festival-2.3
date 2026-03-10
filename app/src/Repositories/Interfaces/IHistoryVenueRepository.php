<?php

namespace App\Repositories\Interfaces;

interface IHistoryVenueRepository
{
    public function getAll(): array;

    public function getStopsByEventId(int $eventId): array;
}