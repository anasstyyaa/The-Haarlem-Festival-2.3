<?php

namespace App\Controllers;

use App\Services\PersonalProgramService;
use App\Repositories\EventRepository;
use App\Models\PersonalProgram;

use App\Services\Yummy\RestaurantService;
use App\Repositories\Yummy\RestaurantRepository;
use App\Repositories\Yummy\RestaurantSessionRepository;
use App\Services\Yummy\RestaurantSessionService;

use App\Services\ArtistService;
use App\Repositories\ArtistRepository;
use App\Services\JazzEventService;
use App\Repositories\JazzEventRepository;

use App\Services\HistoryService;
use App\Repositories\HistoryEventRepository;
use App\Repositories\HistoryVenueRepository;
use App\Models\HistoryVenueModel;

use App\Services\CommunicationService;
use App\Services\UserService;
use App\Repositories\UserRepository;

class TicketController
{
    private PersonalProgramService $programService;
    private EventRepository $eventRepo;
    private RestaurantService $restaurantService;
    private RestaurantSessionService $restaurantSessionService;
    private ArtistService $artistService;
    private JazzEventService $jazzEventService;
    private HistoryService $historyService;
    private HistoryVenueRepository $historyVenueRepository;
    private CommunicationService $communicationService;
    private UserService $userService;
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->programService = new PersonalProgramService();
        $this->eventRepo = new EventRepository();
        $this->restaurantService = new RestaurantService(new RestaurantRepository());
        $this->restaurantSessionService = new RestaurantSessionService(new RestaurantSessionRepository(), new RestaurantRepository());
        $this->artistService = new ArtistService(new ArtistRepository());
        $this->jazzEventService = new JazzEventService(new JazzEventRepository());
        $this->communicationService = new CommunicationService();
        $this->userService = new UserService(new UserRepository());

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

        foreach ($tickets as $ticket) {
            $event = $ticket->getEvent();
            $subId = $event->getSubEventId();

            if (strcasecmp($event->getEventType()->name, 'reservation') === 0) {
                $session = $this->restaurantSessionService->getSessionById($subId);
    
                if ($session) {
                    $restaurant = $this->restaurantService->getRestaurantById($session->getRestaurantId());
                    
                    if ($restaurant) {
                        $restaurant->setSessionData($session);
                        $event->setDetails($restaurant);
                    }
                }
            }

            if (strcasecmp($event->getEventType()->name, 'JazzEvent') === 0) {
                $jazzEvent = $this->jazzEventService->getJazzEventById($subId);

                if ($jazzEvent) {
                    $artist = $this->artistService->getArtistById($jazzEvent->getArtistId());
                    $venueInfo = (new JazzEventRepository())->getVenueInfoByJazzEventId($jazzEvent->getId());

                    $event->setDetails([
                        'artist' => $artist,
                        'venueInfo' => $venueInfo
                    ]);
                }
            }

            if (strcasecmp($event->getEventType()->name, 'tour') === 0) {
                $historyEvent = $this->historyService->getSessionByEventId($event->getId());

                if ($historyEvent) {
                    $stops = $this->historyVenueRepository->getStopsByEventId($event->getId());

                    if (!empty($stops)) {
                        $firstStop = $stops[0];

                        $venue = new HistoryVenueModel(
                            (int)($firstStop['venueId'] ?? 0),
                            $firstStop['venueName'] ?? '',
                            $firstStop['details'] ?? null,
                            $firstStop['location'] ?? null,
                            isset($firstStop['imageId']) ? (int)$firstStop['imageId'] : null
                        );

                        $historyEvent->setVenue($venue);
                    }

                    $event->setDetails($historyEvent);
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

        $_SESSION['flash_success'] = "Your booking for $numberOfPeople people has been successfully added to your Personal Program.";

        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/'));
        exit;
    }
    
    public function paymentSuccess(): void
    {
        $program = $_SESSION['program'] ?? null;
        $userId = $_SESSION['user']['id'] ?? 0; 
        $stripeSessionId = $_GET['session_id'] ?? 'unknown'; // Stripe passes this back

        if ($program && count($program->getTickets()) > 0) {
            try {
                foreach ($program->getTickets() as $ticket) {
                    $this->programService->savePaidTicket($ticket, $stripeSessionId);
                }

                $communicationService = new CommunicationService();
                $userId = $_SESSION['user']['id'];

                $userModel = $this->userService->getUserById($userId); 

                if ($userModel) {
                    $userData = [
                        'email'      => $userModel->getEmail(),
                        'full_name' => $userModel->getFullName(),
                    ];
                } else {
                    // Fallback if user isn't found for some reason
                    $userData = [
                        'email'      => $_SESSION['user']['email'],
                        'full_name' => $_SESSION['user']['userName'],
                    ];
                }

                $communicationService->sendOrderConfirmation($userData, $program->getTickets(), $stripeSessionId);

                unset($_SESSION['program']);
                $_SESSION['flash_success'] = "Thank you! Your tickets have been secured.";

            } catch (\Exception $e) {
                error_log("Database Error during payment success: " . $e->getMessage());
                $_SESSION['error'] = "Payment recorded, but there was an issue saving your tickets. Please contact support.";
                //die("Database Error: " . $e->getMessage());
            }
        }

        require __DIR__ . '/../Views/payment/success.php';
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
    public function checkout(): void
    {
        if (!isset($_SESSION['user'])) {
            $_SESSION['flash_error'] = "You must be logged in to checkout.";
            header('Location: /personalProgram'); 
            exit();
        }

        // Stripe Secret Key (a test key from stripe.com)
        $apiKey = getenv('STRIPE_SECRET_KEY');

        $program = $_SESSION['program'] ?? null;
        if (!$program || count($program->getTickets()) === 0) {
            header("Location: /personalProgram");
            exit;
        }

        // Preparing the data for Stripe
        $data = [
            'success_url' => 'http://localhost/payment-success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => 'http://localhost/personalProgram',
            'mode' => 'payment',
            'payment_method_types[0]' => 'card',
            'payment_method_types[1]' => 'ideal',
        ];

        $i = 0;
        foreach ($program->getTickets() as $ticket) {
            $event = $ticket->getEvent();
            $details = $event->getDetails();
            $name = "Festival Ticket";

            if (is_array($details) && isset($details['artist'])) {
                $artist = $details['artist'] ?? null;

                if ($artist && method_exists($artist, 'getName')) {
                    $name = $artist->getName();
                }
            } elseif ($details && method_exists($details, 'getName')) {
                $name = $details->getName();
            }

            // Stripe needs the price in Cents (1000 = €10.00)
            $data["line_items[$i][price_data][currency]"] = 'eur';
            $unitAmount = (int)($ticket->getUnitPrice() * 100); 
            $data["line_items[$i][price_data][unit_amount]"] = $unitAmount;
            $data["line_items[$i][price_data][product_data][name]"] = $name;
            $data["line_items[$i][quantity]"] = $ticket->getNumberOfPeople();
            $i++;
        }

        // Sends the request to Stripe via cURL
        $ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ':');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);

        // Redirects to the Stripe payment page
        if (isset($response['url'])) {
            header("Location: " . $response['url']);
            exit;
        } else {
            // If there is an error (e.g. invalid key), show it
            echo "<h1>Stripe Error</h1>";
            echo "<pre>" . print_r($response, true) . "</pre>";
            exit;
        }
    }
}
