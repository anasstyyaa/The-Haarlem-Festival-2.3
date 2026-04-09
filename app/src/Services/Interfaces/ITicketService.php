<?php

namespace App\Services\Interfaces;

use App\Models\TicketModel; 

interface ITicketService
{
    public function savePaidTicket(TicketModel $ticket, string $stripeId): bool;
    public function getByToken(string $token): ?array;
    public function markAsScanned(string $token): bool;
    public function savePendingTicket(TicketModel $ticket, string $tempOrderId): bool ;
    public function updateTicketsToPaid(string $orderId, string $actualStripeId): bool ;
    public function markAsExpired(string $orderId): bool;
    public function getAllWithDetails(): array;
    public function getTicketsByOrderId(string $orderId): array;
    public function getUserTickets(int $userId): array;
    public function hydrateTickets(array $tickets): array;
    public function addToProgram(array $data, ?int $userId): void; 
    public function updateProgramQuantity(int $itemId, string $action): void;
    public function countTicketsByUserId(int $userId): int;
    public function getUserTicketsPaginated(int $userId, int $page = 1): array;
    public function getPaginatedTickets(int $page = 1): array;
    public function getExportData(array $requestedColumns): array;
    
}