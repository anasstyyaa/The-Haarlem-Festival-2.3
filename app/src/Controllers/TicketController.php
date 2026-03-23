<?php

namespace App\Controllers;


use App\Models\PersonalProgram;
use App\Services\Interfaces\IPersonalProgramService;
use App\Services\Interfaces\Yummy\IRestaurantService;
use App\Services\Interfaces\Yummy\IRestaurantSessionService; 
use App\Services\Interfaces\IArtistService; 
use App\Services\Interfaces\IJazzEventService;  
use App\Services\Interfaces\IJazzPassService; 
use App\Services\Interfaces\ICommunicationService; 
use App\Services\Interfaces\IUserService; 

use App\Repositories\EventRepository;

use App\Services\Interfaces\IHistoryService; 
use App\Services\HistoryService;
use App\Models\HistoryVenueModel;
use App\Repositories\HistoryVenueRepository; 
use App\Repositories\HistoryEventRepository;

use App\Repositories\KidsEventRepository;
use App\Services\KidsEventService;


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
    private ICommunicationService $communicationService;
    private IUserService $userService;

    private KidsEventService $kidsEventService;
    private HistoryService $historyService;
    private HistoryVenueRepository $historyVenueRepository;
    

    public function __construct(IPersonalProgramService $programService, IRestaurantService $restaurantService, IRestaurantSessionService $restaurantSessionService, IArtistService $artistService, IJazzEventService $jazzEventService,IJazzPassService $jazzPassService, ICommunicationService $communicationService, IUserService $userService)
    {
        $this->programService = $programService; 
        $this->eventRepo = new EventRepository();
        $this->restaurantService = $restaurantService; 
        $this->restaurantSessionService = $restaurantSessionService; 
        $this->artistService = $artistService; 
        $this->jazzEventService = $jazzEventService; 
        //$this->historyService = $historyService; 
        $this->communicationService = $communicationService; 
        $this->jazzPassService = $jazzPassService;
        $this->userService = $userService; 

        $this->kidsEventService = new KidsEventService(new KidsEventRepository());
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

            if (strcasecmp($event->getEventType()->value, 'reservation') === 0) {
                $session = $this->restaurantSessionService->getSessionById($subId);
    
                if ($session) {
                    $restaurant = $this->restaurantService->getRestaurantById($session->getRestaurantId());
                    
                    if ($restaurant) {
                        $restaurant->setSessionData($session);
                        $event->setDetails($restaurant);
                    }
                }
            }

            if (strcasecmp($event->getEventType()->value, 'JazzEvent') === 0) {
                $jazzEvent = $this->jazzEventService->getJazzEventById($subId);

                if ($jazzEvent) {
                    $artist = $this->artistService->getArtistById($jazzEvent->getArtistId());
                    $venueInfo = ($this->jazzEventService->getVenueInfoByJazzEventId($jazzEvent->getId()));

                    $event->setDetails([
                        'artist' => $artist,
                        'venueInfo' => $venueInfo, 
                        'jazzEvent' => $jazzEvent
                    ]);
                }
            }

            if (strcasecmp($event->getEventType()->value, 'jazzpass') === 0) {
                $jazzPass = $this->jazzPassService->getPassById($subId);

                if ($jazzPass) {
                    $event->setDetails($jazzPass);
                }
            }

            if (strcasecmp($event->getEventType()->value, 'tour') === 0) {
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
            
            if (strcasecmp($event->getEventType()->value, 'kids') === 0) {
                $kidsEvent = $this->kidsEventService->getEventById($subId);
                if ($kidsEvent) {
                    $event->setDetails([
                        'name'      => $kidsEvent->getType() === 'Teylers Secret' ? 'Teylers Secret' : $kidsEvent->getType(),
                        'location'  => $kidsEvent->getLocation() ?? 'Teylers Museum, Haarlem',
                        'date'      => $this->kidsEventService->mapDayToDate($kidsEvent->getDay() ?? ''), 
                        'startTime' => $kidsEvent->getStartTime() ?? '10:00'
                    ]);
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

        // capacity check 
        if (strcasecmp($eventType, 'reservation') === 0) {
            $session = $this->restaurantSessionService->getSessionById($subEventId);
            
            if (!$session || $session->getAvailableSlots() < $numberOfPeople) {
                $remaining = $session ? $session->getAvailableSlots() : 0;
                $_SESSION['flash_error'] = "Sorry, there are only $remaining spots left for this session.";
                header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/yummy'));
                exit;
            }
        }
        // add similar checks for other events 

        $eventId = $this->eventRepo->checkEventType($subEventId, $eventType);

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
    
    public function paymentSuccess(): void
    {
        $program = $_SESSION['program'] ?? null;
        $userId = $_SESSION['user']['id'] ?? 0; 
        $stripeSessionId = $_GET['session_id'] ?? 'unknown'; // Stripe passes this back
        $tempOrderId = $_GET['orderId'] ?? null;

        if ($tempOrderId) {
            try {
                $this->programService->updateTicketsToPaid($tempOrderId, $stripeSessionId);
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

                $this->communicationService->sendOrderConfirmation($userData, $program->getTickets(), $stripeSessionId);

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

    public function paymentFailed(): void
    {
        $tempOrderId = $_GET['orderId'] ?? null;
        $userId = $_SESSION['user']['id'] ?? 0; 
        if ($tempOrderId) {
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
        
            $this->communicationService->sendPaymentReminder($userData, $tempOrderId);
        }

        require __DIR__ . '/../Views/payment/failed.php';
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

        $program = $_SESSION['program'] ?? null;
        if (!$program || count($program->getTickets()) === 0) {
            header("Location: /personalProgram");
            exit;
        }
        $userId = $_SESSION['user']['id'];
        $tempOrderId = $this->programService->createPendingTicketsFromSession($program, $userId);
        $ticketNames = [];

        foreach ($program->getTickets() as $ticket) {
            $event = $ticket->getEvent();

            if (strcasecmp($event->getEventType()->name, 'reservation') === 0) {
                $sessionId = $event->getSubEventId();
                $qty = $ticket->getNumberOfPeople();
                $success = $this->restaurantSessionService->updateCapacity($sessionId, -$qty);

                if (!$success) {
                $_SESSION['flash_error'] = "Sorry, one of your selected restaurant sessions has just sold out or doesn't have enough seats left.";
                    header("Location: /personalProgram");
                    exit;
                }
            }
        }

        $userId = $_SESSION['user']['id'];
        $tempOrderId = $this->programService->createPendingTicketsFromSession($program, $userId);
        
        $ticketNames = [];
        foreach ($program->getTickets() as $ticket) {
            $event = $ticket->getEvent();
            $details = $event->getDetails();
            $name = "Festival Ticket";

            if (is_array($details) && isset($details['artist'])) {
                $name = $details['artist']->getName();
            } elseif (is_array($details) && isset($details['name'])) {
                $name = $details['name'];
            } elseif (is_object($details) && method_exists($details, 'getName')) {
                $name = $details->getName();
            }
            
            $ticketNames[] = $name;
        }
        

        $this->redirectToStripe($program->getTickets(), $tempOrderId, $ticketNames);
    }

    public function repay(): void
    {
        $orderId = $_GET['orderId'] ?? '';
        $tickets = $this->programService->getTicketsByOrderId($orderId);

        if (empty($tickets)) {
            $_SESSION['flash_error'] = "This order was not found, has already been paid, or has expired.";
            header("Location: /personalProgram");
            exit;
        }

        $createdAt = new \DateTime($tickets[0]['created_at']);
        $now = new \DateTime();
        $interval = $createdAt->diff($now);
        
        $hoursPassed = ($interval->days * 24) + $interval->h;

        if ($hoursPassed >= 24) {
            foreach ($tickets as $t) {
                $event = $this->eventRepo->getById($t['event_id']);
                
                if ($event && strcasecmp($event->getEventType()->value, 'reservation') === 0) {
                    $sessionId = (int)$t['sub_event_id'];
                    $quantity = (int)$t['number_of_people'];

                    $this->restaurantSessionService->updateCapacity($sessionId, $quantity);
                }
            }

            $this->programService->markOrderAsExpired($orderId);

            $_SESSION['flash_error'] = "Your 24-hour payment window has expired. The items have been released.";
            header("Location: /personalProgram");
            exit;
        }

        $this->redirectToStripe($tickets, $orderId);
    }


    private function redirectToStripe(array $ticketsData, string $orderId, array $customNames = []): void //customNmaes may be empty
    {
        $apiKey = getenv('STRIPE_SECRET_KEY');

        $data = [
            'success_url' => "http://localhost/payment-success?orderId=$orderId&session_id={CHECKOUT_SESSION_ID}",
            'cancel_url' => "http://localhost/payment-failed?orderId=$orderId",
            'mode' => 'payment',
            'payment_method_types[0]' => 'card',
            'payment_method_types[1]' => 'ideal',
        ];

        foreach ($ticketsData as $i => $ticket) {
            // handeling both Object (from Session) or Array (from DB)
            $unitPrice = is_array($ticket) ? $ticket['unit_price'] : $ticket->getUnitPrice();
            $qty = is_array($ticket) ? $ticket['number_of_people'] : $ticket->getNumberOfPeople();
            $name = $customNames[$i] ?? "Haarlem Festival Ticket";

            $data["line_items[$i][price_data][currency]"] = 'eur';
            $data["line_items[$i][price_data][unit_amount]"] = (int)($unitPrice * 100);
            $data["line_items[$i][price_data][product_data][name]"] = $name;
            $data["line_items[$i][quantity]"] = $qty;
        }

        $ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ':');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);

        if (isset($response['url'])) {
            header("Location: " . $response['url']);
            exit;
        } else {
            die("Stripe Error: " . ($response['error']['message'] ?? 'Unknown error'));
        }
    }
}
