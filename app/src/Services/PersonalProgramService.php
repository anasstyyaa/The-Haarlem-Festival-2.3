<?php 
namespace App\Services;

use App\Services\Interfaces\IPersonalProgramService;
use App\Models\PersonalProgram;
use App\Repositories\Interfaces\IEventRepository;
use App\Repositories\Interfaces\IUserRepository;
use App\Models\TicketModel;

class PersonalProgramService implements IPersonalProgramService
{
    // Inject Interfaces instead of creating concrete classes with 'new'
    public function __construct(
        private IEventRepository $eventRepository,
        private IUserRepository $userRepository
    ) {}

    public function addTicketToProgram(int $eventId, int $numberOfPeople, ?int $userId, $programItemId = null): void 
    {
        $programItemId = ($programItemId === '' || $programItemId === 0 || $programItemId === '0') 
            ? null 
            : (int)$programItemId;

        $event = $this->eventRepository->getById($eventId);
        $user = ($userId !== null) ? $this->userRepository->getById($userId) : null;
        $ticket = new TicketModel(0, $event, $user, $numberOfPeople);
        $targetId = ($programItemId !== null && $programItemId > 0) 
            ? $programItemId 
            : (int)$event->getSubEventId();
            
        $ticket->setProgramItemId($targetId);
        $program = $_SESSION['program'] ?? new PersonalProgram();
        $program->addTicket($ticket);

        $_SESSION['program'] = $program;
    }

    public function syncUserToTicket(TicketModel $ticket, int $userId): void 
    {
        if ($ticket->getUser() === null) {
            $user = $this->userRepository->getById($userId);
            $ticket->setUser($user);
        }
    }

    
}