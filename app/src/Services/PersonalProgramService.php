<?php 
namespace App\Services;

use App\Models\PersonalProgram;
use App\Models\TicketModel;

class PersonalProgramService
{
    // private EventRepository $eventRepository;
    // private UserRepository $userRepository;

//     public function __construct(
//         EventRepository $eventRepository,
//         UserRepository $userRepository
//     ) {
//         $this->eventRepository = $eventRepository;
//         $this->userRepository = $userRepository;
//     }

//     public function addTicketToProgram(int $eventId, int $numberOfPeople, ?int $userId): void {

//     $event = $this->eventRepository->findById($eventId);

//     $user = null;
//     if ($userId !== null) {
//         $user = $this->userRepository->findById($userId);
//     }

//     $ticket = new TicketModel(0, $event, $user, $numberOfPeople);

//     $program = $_SESSION['program'] ?? new PersonalProgram();
//     $program->addTicket($ticket);

//     $_SESSION['program'] = $program;
// }

}
