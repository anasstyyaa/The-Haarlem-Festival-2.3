<?php 
namespace App\Services;

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
}
