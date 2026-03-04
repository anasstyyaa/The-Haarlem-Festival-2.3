<?php

namespace App\Services\Interfaces;

interface IHistoryService
{
    public function getAllSessions(): array;

    public function getSelectedEventId(?int $eventIdFromQuery = null): ?int;

    public function getSessionByEventId(int $eventId): ?array;

    public function getStopsByEventId(int $eventId): array;

    public function getAllVenues(): array;
    public function getEventIdBySlot(string $slotDate, string $startTime, string $language): ?int;
}