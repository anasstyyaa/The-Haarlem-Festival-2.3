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
        // 1. Sanitize the incoming ID
        $programItemId = ($programItemId === '' || $programItemId === 0 || $programItemId === '0') 
            ? null 
            : (int)$programItemId;

        // 2. Fetch the required Entities (Objects)
        $event = $this->eventRepository->getById($eventId);
        $user = ($userId !== null) ? $this->userRepository->getById($userId) : null;

        // 3. Instantiate the TicketModel (Object)
        // We pass the Event object and User object directly
        $ticket = new TicketModel(0, $event, $user, $numberOfPeople);

        // 4. Set the specific program item ID
        $targetId = ($programItemId !== null && $programItemId > 0) 
            ? $programItemId 
            : (int)$event->getSubEventId();
            
        $ticket->setProgramItemId($targetId);

        // 5. Manage the Session via the PersonalProgram object
        $program = $_SESSION['program'] ?? new PersonalProgram();
        $program->addTicket($ticket);

        $_SESSION['program'] = $program;
    }

    public function syncUserToTicket(TicketModel $ticket, int $userId): void 
    {
        if ($ticket->getUser() === null) {
            // Fetch User object and set it on the Ticket object
            $user = $this->userRepository->getById($userId);
            $ticket->setUser($user);
        }
    }

    
}