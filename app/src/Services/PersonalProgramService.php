<?php 
namespace App\Services;

use App\Models\PersonalProgram;
use App\Repositories\EventRepository;
use App\Repositories\UserRepository;
use App\Models\TicketModel;

class PersonalProgramService implements IPersonalProgramService
{
    private EventRepository $eventRepository;
    private UserRepository $userRepository;

    public function __construct(
    ) {
        $this->eventRepository = new EventRepository();
        $this->userRepository = new UserRepository();
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


}
