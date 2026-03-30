<?php 
namespace App\Services;

use App\Services\Interfaces\ITicketService;
use App\Repositories\Interfaces\ITicketRepository;
use App\Models\TicketModel;

class TicketService implements ITicketService
{
    public function __construct(
        private ITicketRepository $ticketRepository
    ) {}
     public function savePaidTicket(TicketModel $ticket, string $stripeId): bool
    {
       return $this->ticketRepository->savePaidTicket($ticket, $stripeId);
    }

    public function getByToken(string $token): ?array 
    {
      return $this->getByToken($token);
    }
    
    public function markAsScanned(string $token): bool  
    {
       return $this->ticketRepository->markAsScanned($token);
    }

    public function savePendingTicket(TicketModel $ticket, string $tempOrderId): bool 
    {
        return $this->ticketRepository->savePendingTicket($ticket, $tempOrderId);
    }

    public function updateTicketsToPaid(string $orderId, string $actualStripeId): bool 
    {
       return $this->ticketRepository->updateTicketsToPaid($orderId,$actualStripeId);
    }

    public function markAsExpired(string $orderId): bool 
    {
       return $this->ticketRepository->markAsExpired($orderId);
    }

    public function getAll(): array
    {
       return $this->ticketRepository->getAll();
    }

    public function getAllWithDetails(): array
    {
       return $this->ticketRepository->getAllWithDetails();
    }

    public function getTicketsByOrderId(string $orderId): array 
    {
      return $this->ticketRepository->getTicketsByOrderId($orderId);
    }

    
}