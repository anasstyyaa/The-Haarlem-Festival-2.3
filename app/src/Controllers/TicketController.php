<?php 
namespace App\Controllers;

use App\Services\PersonalProgramService;

class TicketController
{
    private PersonalProgramService $programService;

    public function __construct()
    {
        $this->programService = new PersonalProgramService();
    }

   public function addTicket(): void
{
    $eventId = $_POST['event_id'];
    $numberOfPeople = $_POST['number_of_people'];

    $userId = $_SESSION['user_id'] ?? null;

    $this->programService->addTicketToProgram(
        $eventId,
        $numberOfPeople,
        $userId
    );
    echo '<pre>';
print_r($_SESSION);
echo '</pre>';
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/'));
exit;
}

}
