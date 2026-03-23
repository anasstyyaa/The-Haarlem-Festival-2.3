<?php

namespace App\Services\Interfaces;

use App\Models\TicketModel;
use App\Models\PersonalProgram;

interface IPersonalProgramService
{
      public function addTicketToProgram(int $eventId, int $numberOfPeople, ?int $userId, ?int $programItemId = null): void;
      public function savePaidTicket(TicketModel $ticket, string $stripeId): bool;
      public function createPendingTicketsFromSession(PersonalProgram $program, int $userId): string ;
      public function savePendingTicket(TicketModel $ticket, string $tempOrderId): bool;
      public function updateTicketsToPaid(string $orderId, string $actualStripeId): bool;
      public function getTicketsByOrderId(string $orderId): array;
      public function markOrderAsExpired(string $orderId): void;
}