<?php

namespace App\Repositories\Interfaces;

use App\Models\HistoryEventModel;

interface IHistoryEventRepository
{
    public function getAll(): array;
    public function getByEventId(int $eventId): ?HistoryEventModel;
    public function getEventIdBySlot(string $slotDate, string $startTime, string $language): ?int;

    public function create(HistoryEventModel $event): bool;
    public function update(HistoryEventModel $event): bool;
    public function delete(int $eventId): bool;
}