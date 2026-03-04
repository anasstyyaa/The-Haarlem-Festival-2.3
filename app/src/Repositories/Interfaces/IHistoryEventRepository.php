<?php

namespace App\Repositories\Interfaces;

interface IHistoryEventRepository
{
    public function getAll(): array;

    public function getByEventId(int $eventId): ?array;
    
    public function getEventIdBySlot(string $slotDate, string $startTime, string $language): ?int;
}