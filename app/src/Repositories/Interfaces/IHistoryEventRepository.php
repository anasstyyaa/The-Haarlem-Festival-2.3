<?php

namespace App\Repositories\Interfaces;
use App\Models\HistoryEventModel;
interface IHistoryEventRepository
{
    public function getAll(): array;

    public function getByEventId(int $eventId): ?HistoryEventModel;
    
    public function getEventIdBySlot(string $slotDate, string $startTime, string $language): ?int;

    public function createBooking(int $eventId, ?int $userId): int;
    
public function addBookingItem(int $bookingId, string $ticketType, int $quantity): bool;
}