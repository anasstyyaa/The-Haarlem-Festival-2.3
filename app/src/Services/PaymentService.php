<?php

namespace App\Services;

use App\Services\Interfaces\ICommunicationService;
use App\Services\Interfaces\IPaymentService;
use App\Services\Interfaces\Yummy\IRestaurantSessionService;
use App\Services\Interfaces\IJazzEventService;
use App\Services\Interfaces\IJazzPassService;
use App\Services\Interfaces\ITicketService;
use App\Repositories\Interfaces\IEventRepository;
use App\Repositories\Interfaces\IUserRepository;
use App\Models\PersonalProgram;
use App\Models\TicketModel;
use App\Services\Interfaces\IKidsEventService;
use App\Services\Interfaces\IStripeGateway;
use App\ViewModels\TicketViewModel; 
use App\ViewModels\CustomerViewModel;

class PaymentService implements IPaymentService
{

    public function __construct(
        private ITicketService $ticketService, 
        private IRestaurantSessionService $restaurantSessionService,
        private IJazzEventService $jazzService,
        private IJazzPassService $jazzPassService,
        private IUserRepository $userRepository,
        private IEventRepository $eventRepository,
        private IKidsEventService $kidsService,
        private ICommunicationService $communicationService,
        private IStripeGateway $stripeGateway
    ) {}

    public function prepareCheckout(PersonalProgram $program, int $userId): string 
    {
        $orderId = $this->createPendingOrder($program, $userId);
        $tickets = $this->getTicketsByOrderId($orderId);
        return $this->generateStripeUrlForOrder($tickets, $orderId);

    }

    public function completePurchase(string $orderId, string $stripeId, int $userId): void 
    {
        $tickets = $this->finalizeOrder($orderId, $stripeId);

        $userModel = $this->userRepository->getById($userId);
        $customer = new CustomerViewModel($userModel, $orderId);
        $ticketViewModels = array_map(fn($t) => new TicketViewModel($t), $tickets);
        
        // by placing try / catch here, i avoid error "payment failed" appear on user's screen while in fact only email falure happend   
        try {
            $this->communicationService->sendOrderConfirmation($customer, $ticketViewModels, $orderId);
        } catch (\Exception $e) {
            error_log("Silent Error: Order confirmation email failed for {$orderId}");
        }
          
        
    }

    public function prepareRepayCheckout(string $orderId): string 
    {
        $tickets = $this->getTicketsByOrderId($orderId);
        
        if (empty($tickets)) {
            throw new \Exception("Order not found or has no tickets.");
        }

        return $this->generateStripeUrlForOrder($tickets, $orderId);
    }

    public function handleFailedPayment(string $orderId, int $userId): void 
    {
        $user = $this->userRepository->getById($userId);
        
        if ($user) {
            $userData = [
                'email' => $user->getEmail(), 
                'full_name' => $user->getFullName()
            ];
            $this->communicationService->sendPaymentReminder($userData, $orderId);
        }
    }

    public function finalizeOrder(string $orderId, string $stripeId): array 
    {
        $tickets = $this->getTicketsByOrderId($orderId);

        if (empty($tickets)) {
            throw new \App\Exceptions\OrderException("Finalization failed: No tickets found for Order {$orderId}.");
        }

        $this->ticketService->updateTicketsToPaid($orderId, $stripeId);

        foreach ($tickets as $ticket) {
            $event = $ticket->getEvent();
            $qty = $ticket->getNumberOfPeople();
            $targetId = $event->getSubEventId() ?: $ticket->getProgramItemId(); 

            if (!$targetId) continue;

            match (strtolower($event->getEventType()->value)) {
                'jazz'        => $this->jazzService->decreaseTicketsLeft($targetId, $qty),
                'jazzpass'    => $this->jazzPassService->decreaseTicketsLeft($targetId, $qty),
                'reservation' => $this->restaurantSessionService->updateCapacity($targetId, -$qty),
                'kids'        => $this->kidsService->decreaseCapacity($targetId, $qty),
                default       => null
            };
        }

        return $tickets;
    }

    public function releaseExpiredOrder(string $orderId): void 
    {
        $tickets = $this->ticketService->getTicketsByOrderId($orderId);
        foreach ($tickets as $t) {
            $event = $this->eventRepository->getById($t['event_id']);
            if ($event && strcasecmp($event->getEventType()->value, 'reservation') === 0) {
                $this->restaurantSessionService->updateCapacity((int)$t['program_item_id'], (int)$t['number_of_people']);
            }
        }
        $this->ticketService->markAsExpired($orderId);
    }

    public function savePaidTicket(TicketModel $ticket, string $stripeId): bool { 
        if ($ticket->getUser() === null) {
            $currentUserId = $_SESSION['user']['id'] ?? null;
            
            if ($currentUserId) {
                $user = $this->userRepository->getById($currentUserId);
                $ticket->setUser($user);
            }
        }

        return $this->ticketService->savePaidTicket($ticket, $stripeId);
    }

    public function createPendingOrder(PersonalProgram $program, int $userId): string 
    {
        $tempOrderId = 'ORDER_' . bin2hex(random_bytes(8));
        $user = $this->userRepository->getById($userId);

        foreach ($program->getTickets() as $ticket) {
            if (!$ticket->getUser()) {
                $ticket->setUser($user);
            }
            $this->ticketService->savePendingTicket($ticket, $tempOrderId);
        }

        return $tempOrderId;
    }

    public function isOrderExpired(string $orderId): bool 
    {
        $tickets = $this->ticketService->getTicketsByOrderId($orderId);
        if (empty($tickets)) return true;

        $createdAt = new \DateTime($tickets[0]['created_at']);
        $now = new \DateTime();
        $interval = $createdAt->diff($now);
        
        $hoursPassed = ($interval->days * 24) + $interval->h;

        return $hoursPassed >= 24;
    }

    public function savePendingTicket(TicketModel $ticket, string $tempOrderId): bool {
        return $this->ticketService->savePendingTicket($ticket, $tempOrderId);
    }

    public function updateTicketsToPaid(string $orderId, string $actualStripeId): bool{
        return $this->ticketService->updateTicketsToPaid($orderId, $actualStripeId);
    }

    public function markOrderAsExpired(string $orderId): void {
        $this->ticketService->markAsExpired($orderId);
    }

    public function getTicketsByOrderId(string $orderId): array
    {
        $rawTickets = $this->ticketService->getTicketsByOrderId($orderId);
        $populatedTickets = [];

        foreach ($rawTickets as $row) {
            $event = $this->eventRepository->getById((int)$row['event_id']);
            $user = $row['user_id'] ? $this->userRepository->getById((int)$row['user_id']) : null;

            $ticket = new TicketModel(
                (int)$row['id'],
                $event,
                $user,
                (int)$row['number_of_people'],
                $row['unique_ticket_token'] 
            );
            $ticket->setProgramItemId((int)$row['program_item_id']);

            $populatedTickets[] = $ticket;
        }

        return $this->ticketService->hydrateTickets($populatedTickets);
    }

    private function generateStripeUrlForOrder(array $tickets, string $orderId): string 
    {
        $ticketViewModels = array_map(fn($t) => new TicketViewModel($t), $tickets);
        return $this->stripeGateway->createCheckoutSession($ticketViewModels, $orderId);
    }

}