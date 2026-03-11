<?php

namespace App\Repositories\Interfaces;

use App\Models\HistoryEventModel;

interface IHistoryEventRepository
{
    public function getAll(): array;

    public function getByEventId(int $eventId): ?HistoryEventModel;
    
}