<?php 
namespace App\Services;

use App\Services\Interfaces\IPersonalProgramService;
use App\Models\PersonalProgram;
use App\Repositories\EventRepository;
use App\Repositories\UserRepository;
use App\Repositories\TicketRepository;
use App\Models\TicketModel;

class PersonalProgramService implements IPersonalProgramService
{
    private EventRepository $eventRepository;
    private UserRepository $userRepository;
    private TicketRepository $ticketRepository;

    public function __construct() {
        $this->eventRepository = new EventRepository();
        $this->userRepository = new UserRepository();
        $this->ticketRepository = new TicketRepository();
    }

    public function addTicketToProgram(int $eventId, int $numberOfPeople, ?int $userId, ?int $programItemId = null): void {
        $event = $this->eventRepository->getById($eventId);

        $user = null;
        if ($userId !== null) {
            $user = $this->userRepository->getById($userId);
        }

        $ticket = new TicketModel(0, $event, $user, $numberOfPeople);

        if ($programItemId !== null) {
            $ticket->setProgramItemId($programItemId);
        }

        $program = $_SESSION['program'] ?? new PersonalProgram();
        $program->addTicket($ticket);

        $_SESSION['program'] = $program;
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

    public function createPendingTicketsFromSession(PersonalProgram $program, int $userId): string 
    {
        // generating a unique reference for this "Pay Later" attempt
        $tempOrderId = 'ORDER_' . bin2hex(random_bytes(8));

        foreach ($program->getTickets() as $ticket) {
            // setting the user on the ticket object if not already set
            if (!$ticket->getUser()) {
                $ticket->setUser($this->userRepository->getById($userId));
            }
            
            $this->ticketRepository->savePendingTicket($ticket, $tempOrderId);
        }

        return $tempOrderId;
    }

    public function savePendingTicket(TicketModel $ticket, string $tempOrderId): bool {
        return $this->ticketRepository->savePendingTicket($ticket, $tempOrderId);
    }

    public function updateTicketsToPaid(string $orderId, string $actualStripeId): bool{
        return $this->ticketRepository->updateTicketsToPaid($orderId, $actualStripeId);
    }

    public function getTicketsByOrderId(string $orderId): array {
        return $this->ticketRepository->getTicketsByOrderId($orderId);
    }

    public function markOrderAsExpired(string $orderId): void {
        $this->ticketRepository->markAsExpired($orderId);
    }
}
