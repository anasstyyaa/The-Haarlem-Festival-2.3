<?php

namespace App\Services\Interfaces;

use App\Models\HistoryEventModel;

interface IHistoryService
{
    public function getAllSessions(): array;

    public function getSessionByEventId(int $eventId): ?HistoryEventModel;


}