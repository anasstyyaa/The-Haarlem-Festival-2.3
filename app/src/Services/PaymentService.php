<?php

namespace App\Services;

use App\Services\Interfaces\IPaymentService;
use App\Services\Interfaces\Yummy\IRestaurantSessionService;
use App\Services\Interfaces\IJazzEventService;
use App\Services\Interfaces\IJazzPassService;
use App\Repositories\Interfaces\ITicketRepository;
use App\Repositories\Interfaces\IEventRepository;
use App\Repositories\Interfaces\IUserRepository;
use App\Models\PersonalProgram;
use App\Models\TicketModel; 

class PaymentService implements IPaymentService
{
    public function __construct(
        private ITicketRepository $ticketRepository, 
        private IRestaurantSessionService $restaurantSessionService,
        private IJazzEventService $jazzService,
        private IJazzPassService $jazzPassService,
        private IUserRepository $userRepository,
        private IEventRepository $eventRepository
    ) {}

    public function finalizeOrder(string $orderId, string $stripeId): array 
    {
        $tickets = $this->getTicketsByOrderId($orderId);

        if (empty($tickets)) {
            return [];
        }

        $this->ticketRepository->updateTicketsToPaid($orderId, $stripeId);
        foreach ($tickets as $ticket) {
            $event = $ticket->getEvent();
            $qty = $ticket->getNumberOfPeople();
            $targetId = $ticket->getProgramItemId();

            // Ensure targetId exists before trying to update capacity
            if (!$targetId) continue;

            match ($event->getEventType()->value) {
                'jazz'        => $this->jazzService->decreaseTicketsLeft($targetId, $qty),
                'jazzpass'    => $this->jazzPassService->decreaseTicketsLeft($targetId, $qty),
                'reservation' => $this->restaurantSessionService->updateCapacity($targetId, -$qty),
                default       => null
            };
        }

        return $tickets;
    }

    public function releaseExpiredOrder(string $orderId): void 
    {
        $tickets = $this->ticketRepository->getTicketsByOrderId($orderId);
        foreach ($tickets as $t) {
            $event = $this->eventRepository->getById($t['event_id']);
            if ($event && strcasecmp($event->getEventType()->value, 'reservation') === 0) {
                $this->restaurantSessionService->updateCapacity((int)$t['program_item_id'], (int)$t['number_of_people']);
            }
        }
        $this->ticketRepository->markAsExpired($orderId);
    }

    public function savePaidTicket(TicketModel $ticket, string $stripeId): bool {
        // avoiding user = null 
        if ($ticket->getUser() === null) {
            $currentUserId = $_SESSION['user']['id'] ?? null;
            
            if ($currentUserId) {
                $user = $this->userRepository->getById($currentUserId);
                $ticket->setUser($user);
            }
        }

        return $this->ticketRepository->savePaidTicket($ticket, $stripeId);
    }

    public function createPendingOrder(PersonalProgram $program, int $userId): string 
    {
        $tempOrderId = 'ORDER_' . bin2hex(random_bytes(8));
        $user = $this->userRepository->getById($userId);

        foreach ($program->getTickets() as $ticket) {
            if (!$ticket->getUser()) {
                $ticket->setUser($user);
            }
            $this->ticketRepository->savePendingTicket($ticket, $tempOrderId);
        }

        return $tempOrderId;
    }

    public function isOrderExpired(string $orderId): bool 
    {
        $tickets = $this->ticketRepository->getTicketsByOrderId($orderId);
        if (empty($tickets)) return true;

        $createdAt = new \DateTime($tickets[0]['created_at']);
        $now = new \DateTime();
        $interval = $createdAt->diff($now);
        
        $hoursPassed = ($interval->days * 24) + $interval->h;

        return $hoursPassed >= 24;
    }

    public function savePendingTicket(TicketModel $ticket, string $tempOrderId): bool {
        return $this->ticketRepository->savePendingTicket($ticket, $tempOrderId);
    }

    public function updateTicketsToPaid(string $orderId, string $actualStripeId): bool{
        return $this->ticketRepository->updateTicketsToPaid($orderId, $actualStripeId);
    }

    public function markOrderAsExpired(string $orderId): void {
        $this->ticketRepository->markAsExpired($orderId);
    }

    public function getTicketsByOrderId(string $orderId): array
    {
        $rawTickets = $this->ticketRepository->getTicketsByOrderId($orderId);
        $populatedTickets = [];

        foreach ($rawTickets as $row) {
            $event = $this->eventRepository->getById((int)$row['event_id']);
            $user = $row['user_id'] ? $this->userRepository->getById((int)$row['user_id']) : null;

            $ticket = new TicketModel(
                (int)$row['id'],
                $event,
                $user,
                (int)$row['number_of_people']
            );
            $ticket->setProgramItemId((int)$row['program_item_id']);

            // IMPORTANT: We must populate the details here so the Invoice isn't empty!
            $this->populateTicketDetails($ticket);

            $populatedTickets[] = $ticket;
        }
        return $populatedTickets;
    }

    /**
     * This replaces the messy logic that was in your Controller's index()
     */
    private function populateTicketDetails(TicketModel $ticket): void
    {
        $event = $ticket->getEvent();
        $subId = $ticket->getProgramItemId() ?: $event->getSubEventId();
        $type = strtolower($event->getEventType()->value);

        // This logic matches your old Controller index() exactly, but it's now reusable
        if ($type === 'reservation') {
            // You'll need to inject RestaurantServices into this class or use Repos directly
            $repo = new \App\Repositories\Yummy\RestaurantRepository();
            $sessRepo = new \App\Repositories\Yummy\RestaurantSessionRepository();
            $session = $sessRepo->getSessionById($subId);
            if ($session) {
                $restaurant = $repo->getById($session->getRestaurantId());
                $restaurant->setSessionData($session);
                $event->setDetails($restaurant);
            }
        } 
        elseif ($type === 'jazz') {
            $jazzRepo = new \App\Repositories\JazzEventRepository();
            $artistRepo = new \App\Repositories\ArtistRepository();
            $jazzEvent = $jazzRepo->getById($subId);
            if ($jazzEvent) {
                $event->setDetails([
                    'artist' => $artistRepo->getById($jazzEvent->getArtistId()),
                    'jazzEvent' => $jazzEvent
                ]);
            }
        }
        // ... Add similar blocks for 'tour' and 'kids'
    }

}