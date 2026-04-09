<?php 

namespace App\Services\Interfaces; 

use App\Models\TicketModel; 
use App\Models\PersonalProgram;

interface IPaymentService {

    public function prepareCheckout(PersonalProgram $program, int $userId): string;
    public function completePurchase(string $orderId, string $stripeId, int $userId): void ;
    public function handleFailedPayment(string $orderId, int $userId): void ;
    public function prepareRepayCheckout(string $orderId): string;
    public function finalizeOrder(string $orderId, string $stripeSessionId): array; 
    public function releaseExpiredOrder(string $orderId): void;
    public function savePaidTicket(TicketModel $ticket, string $stripeId): bool;
    public function createPendingOrder(PersonalProgram $program, int $userId): string; 
    public function isOrderExpired(string $orderId): bool; 
    public function savePendingTicket(TicketModel $ticket, string $tempOrderId): bool;
    public function updateTicketsToPaid(string $orderId, string $actualStripeId): bool;
    public function getTicketsByOrderId(string $orderId): array;
    public function markOrderAsExpired(string $orderId): void;

}