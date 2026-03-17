<?php 
namespace App\Controllers;

use App\Services\PersonalProgramService;
use App\Repositories\EventRepository;
use App\Models\PersonalProgram;

use App\Services\Yummy\RestaurantService;
use App\Repositories\Yummy\RestaurantRepository;

use App\Services\ArtistService;
use App\Repositories\ArtistRepository;
use App\Services\JazzEventService;
use App\Repositories\JazzEventRepository;

class TicketController
{
    private PersonalProgramService $programService;
    private EventRepository $eventRepo;
    private RestaurantService $restaurantService;
    private ArtistService $artistService;
    private JazzEventService $jazzEventService;

    public function __construct()
    {
        $this->programService = new PersonalProgramService();
        $this->eventRepo = new EventRepository();
        $this->restaurantService = new RestaurantService(new RestaurantRepository());
        $this->artistService = new ArtistService(new ArtistRepository());
        $this->jazzEventService = new JazzEventService(new JazzEventRepository());

    }

    public function index(): void {
        $program = $_SESSION['program'] ?? new PersonalProgram();
        $tickets = $program->getTickets();

        foreach ($tickets as $ticket) {
            $event = $ticket->getEvent();
            $subId = $event->getSubEventId();

            // strcasecmp for case-insensitive comparison
            if (strcasecmp($event->getEventType()->name, 'reservation') === 0) {
                $restaurant = $this->restaurantService->getRestaurantById($subId);
                
                if ($restaurant) {
                    $event->setDetails($restaurant); 
                }
            }

            if (strcasecmp($event->getEventType()->name, 'JazzEvent') === 0) {
                $jazzEvent = $this->jazzEventService->getJazzEventById($subId);

                if ($jazzEvent) {
                    $artist = $this->artistService->getArtistById($jazzEvent->getArtistId());

                    if ($artist) {
                        $event->setDetails($artist);
                    }
                }
            }
        }

        require __DIR__ . '/../Views/personalProgram/personalProgram.php';
    }

   public function addTicket(): void
    {
        $subEventId = $_POST['event_id'];
        $numberOfPeople = $_POST['number_of_people'];
        $eventType = $_POST['event_type'];
        $userId = $_SESSION['user']['id'] ?? null;
        $programItemId = $_POST['program_item_id'] ?? null;
        
        $eventId = $this->eventRepo->checkEventType($subEventId, $eventType);

        if ($eventId === 0) {
            $_SESSION['error'] = "Configuration Error: No Event found for this restaurant (Type: $eventType, ID: $subEventId).";
            header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/'));
            exit;
        }

        $this->programService->addTicketToProgram(
            $eventId,
            $numberOfPeople,
            $userId,
            $programItemId
        );
        //     var_dump($_SESSION['program']);
        // exit;
            $_SESSION['flash_success'] = "Your booking for $numberOfPeople people has been successfully added to your Personal Program.";

            header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/'));
        exit;
    }

    public function removeTicket(): void
    {
        $ticketId = (int)($_POST['ticket_id'] ?? 0);

        if ($ticketId <= 0) {
            $_SESSION['error'] = "Invalid item selected.";
            header('Location: /personalProgram');
            exit;
        }

        $program = $_SESSION['program'] ?? new PersonalProgram();
        $program->removeTicket($ticketId);

        $_SESSION['program'] = $program;
        $_SESSION['flash_success'] = "Item removed from your Personal Program.";

        header('Location: /personalProgram');
        exit;
    }

}
