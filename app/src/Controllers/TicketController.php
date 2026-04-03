<?php

namespace App\Controllers;

use App\Models\PersonalProgram;
use App\Services\Interfaces\IPersonalProgramService;
use App\Services\Interfaces\Yummy\IRestaurantService;
use App\Services\Interfaces\Yummy\IRestaurantSessionService; 
use App\Services\Interfaces\IArtistService; 
use App\Services\Interfaces\IJazzEventService;  
use App\Services\Interfaces\IJazzPassService; 

use App\Repositories\EventRepository;

use App\Services\Interfaces\IHistoryService; 
use App\Services\HistoryService;
use App\Models\HistoryVenueModel;
use App\Repositories\HistoryVenueRepository; 
use App\Repositories\HistoryEventRepository;

use App\Repositories\KidsEventRepository;
use App\Repositories\TicketRepository;
use App\Services\Interfaces\IKidsEventService;

use App\Services\Interfaces\ITicketService;


class TicketController
{
    private IPersonalProgramService $programService;
    private EventRepository $eventRepo;
    private IRestaurantService $restaurantService;
    private IRestaurantSessionService $restaurantSessionService;
    private IArtistService $artistService;
    private IJazzEventService $jazzEventService;
    private IJazzPassService $jazzPassService;
    //private IHistoryService $historyService;
    //private ICommunicationService $communicationService;
    //private IUserService $userService;

    private IKidsEventService $kidsEventService;
    private HistoryService $historyService;
    private HistoryVenueRepository $historyVenueRepository;
    //private UserRepository $userRepository;
    private TicketRepository $ticketRepository;
    private ITicketService $ticketService;
    

    public function __construct(IPersonalProgramService $programService, IRestaurantService $restaurantService, IRestaurantSessionService $restaurantSessionService, IArtistService $artistService, IJazzEventService $jazzEventService, IJazzPassService $jazzPassService, TicketRepository $ticketRepository, IKidsEventService $kidsEventService, ITicketService $ticketService)

    {
        $this->programService = $programService; 
        $this->eventRepo = new EventRepository();
        $this->ticketRepository = $ticketRepository;
        $this->ticketService = $ticketService;
        $this->restaurantService = $restaurantService; 
        $this->restaurantSessionService = $restaurantSessionService; 
        $this->artistService = $artistService; 
        $this->jazzEventService = $jazzEventService; 
        //$this->historyService = $historyService; 
        //$this->communicationService = $communicationService; 
        $this->jazzPassService = $jazzPassService;
        //$this->userService = $userService; 

        $this->kidsEventService = $kidsEventService;
         $this->historyService = new HistoryService(
            new HistoryEventRepository(),
            new HistoryVenueRepository()
        );
        $this->historyVenueRepository = new HistoryVenueRepository(); 
    }

    public function index(): void
    {
        $program = $_SESSION['program'] ?? new PersonalProgram();
        $tickets = $program->getTickets();

        $tickets = $this->ticketService->hydrateTickets($tickets);

        require __DIR__ . '/../Views/personalProgram/personalProgram.php';
    
    }

    public function addTicket(): void
    {
        $subEventId = $_POST['event_id'];
        $numberOfPeople = $_POST['number_of_people'];
        $eventType = $_POST['event_type'];
        $userId = $_SESSION['user']['id'] ?? null;
        $programItemId = $_POST['program_item_id'] ?? null;

        // capacity check 
        if (strcasecmp($eventType, 'reservation') === 0) {
            if ($programItemId) {
                // overriding the Restaurant ID with the Session ID
                $subEventId = $programItemId; 
            } else {
                // If no session ID is found, i can't create a valid ticket
                $_SESSION['error'] = "Please select a specific time slot.";
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit;
            }
        }

        if (strcasecmp($eventType, 'jazz') === 0) {
            $jazzEvent = $this->jazzEventService->getJazzEventById((int)$subEventId);

            if (!$jazzEvent || $jazzEvent->getTicketsLeft() < $numberOfPeople) {
                $remaining = $jazzEvent ? $jazzEvent->getTicketsLeft() : 0;
                $_SESSION['flash_error'] = "Sorry, there are only $remaining tickets left for this jazz event.";
                header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/jazz'));
                exit;
            }
        }

        if (strcasecmp($eventType, 'jazzpass') === 0) {
            $jazzPass = $this->jazzPassService->getPassById((int)$subEventId);

            if (!$jazzPass || $jazzPass->getTicketsLeft() < $numberOfPeople) {
                $remaining = $jazzPass ? $jazzPass->getTicketsLeft() : 0;
                $_SESSION['flash_error'] = "Sorry, there are only $remaining passes left.";
                header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/jazz'));
                exit;
            }
        }
        // add similar checks for other events 

        $eventId = $this->eventRepo->checkEventType((int)$subEventId, $eventType);

        if ($eventId === 0) {
            $_SESSION['error'] = "Configuration Error: No Event found! (Type: $eventType, ID: $subEventId).";
            header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/'));
            exit;
        }

        $this->programService->addTicketToProgram(
            $eventId,
            $numberOfPeople,
            $userId,
            $programItemId
        );

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

    /**
 * Simple ticket scan method for employees
 */
public function scan(): void
{
    $this->requireEmployee();

    try {
        $token = $_GET['token'] ?? '';
        if ($token === '') {
            $status = 'error';
            $message = 'No ticket token provided.';
            $ticket = null;

            require __DIR__ . '/../Views/employee/scanResult.php';
            return;
        }
        $ticket = $this->ticketRepository->getByToken($token);

        if (!$ticket) {
            $status = 'error';
            $message = 'Invalid ticket.';
            $ticket = null;

            require __DIR__ . '/../Views/employee/scanResult.php';
            return;
        }

        if ($ticket['is_scanned'] == 1) {
            $status = 'warning';
            $message = 'This ticket has already been scanned.';

            require __DIR__ . '/../Views/employee/scanResult.php';
            return;
        }

        $this->ticketRepository->markAsScanned($token);

        $status = 'success';
        $message = 'Ticket is valid. Entry allowed.';

        require __DIR__ . '/../Views/employee/scanResult.php';

    } catch (\Exception $e) {
        error_log("Scan error: " . $e->getMessage());

        $status = 'error';
        $message = 'Something went wrong.';
        $ticket = null;

        require __DIR__ . '/../Views/employee/scanResult.php';
    }
}

    private function requireEmployee(): void //checks if current user is employee
    {
        try {
            if (!isset($_SESSION['user'])) {
                throw new \Exception("User not logged in.");
            }
            if (($_SESSION['user']['role'] ?? '') !== 'Employee') {
                throw new \Exception("User is not an employee.");
            }
        } catch (\Exception $e) {
            error_log("Access error: " . $e->getMessage());
            echo "Access denied. Employees only.";
            exit;
        }
    }
    
    public function scanPage(): void
    {
        $this->requireEmployee();

        require __DIR__ . '/../Views/employee/scan.php';
    }

      public function adminIndex()
    {
        $tickets = $this->ticketRepository->getAllWithDetails();

        require __DIR__ . '/../Views/admin/dashboard.php';
    }
  public function exportCsv(): void
{
    $tickets = $this->ticketRepository->getAllWithDetails();

    $selectedColumns = array_intersect(
        $_POST['columns'] ?? [],
        array_keys($tickets[0] ?? [])
    );
    if (empty($selectedColumns)) {
        header("Location: /admin");
        exit;
    }

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="tickets.csv"');

    $output = fopen('php://output', 'w');

    fputcsv($output, $selectedColumns);

    foreach ($tickets as $row) {
        fputcsv(
            $output,
            array_map(fn($col) => $row[$col] ?? '', $selectedColumns)
        );
    }

    fclose($output);
    exit;
}//import to excel
}
