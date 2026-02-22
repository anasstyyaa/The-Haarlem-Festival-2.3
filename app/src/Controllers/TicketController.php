<?php 
namespace App\Controllers;

use App\Services\PersonalProgramService;
use App\Repositories\EventRepository;

class TicketController
{
    private PersonalProgramService $programService;
    private EventRepository $eventRepo;

    public function __construct()
    {
        $this->programService = new PersonalProgramService();
        $this->eventRepo = new EventRepository();
    }
    public function index():void{
          require __DIR__ . '/../Views/personalProgram/personalProgram.php';
    }

   public function addTicket(): void
{
    $subEventId = $_POST['event_id'];
    $numberOfPeople = $_POST['number_of_people'];
    $eventType = $_POST['event_type'];
    $userId = $_SESSION['user_id'] ?? null;
    
    $eventId = $this->eventRepo->checkEventType($subEventId, $eventType);

    $this->programService->addTicketToProgram(
        $eventId,
        $numberOfPeople,
        $userId
    );
//     var_dump($_SESSION['program']);
// exit;
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/'));
exit;
}

}
