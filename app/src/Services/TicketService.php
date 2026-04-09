<?php

namespace App\Services;

use App\Services\Interfaces\ITicketService;
use App\Repositories\Interfaces\ITicketRepository;
use App\Models\TicketModel;

use App\Services\Interfaces\Yummy\IRestaurantSessionService;
use App\Services\Interfaces\Yummy\IRestaurantService;
use App\Services\Interfaces\IJazzEventService;
use App\Services\Interfaces\IHistoryService;
use App\Services\Interfaces\IKidsEventService;
use App\Repositories\Interfaces\IHistoryVenueRepository;
use App\Services\Interfaces\IArtistService;
use App\Services\Interfaces\IJazzPassService;
use App\Services\Interfaces\IDanceEventService;
use App\Models\HistoryVenueModel;
use App\Models\ArtistModel; 

use App\Services\Interfaces\IEventService;
use App\Services\Interfaces\IPersonalProgramService;


class TicketService implements ITicketService
{
   public function __construct(
      private ITicketRepository $ticketRepository,
      private IRestaurantSessionService $restaurantSessionService,
      private IRestaurantService $restaurantService,
      private IJazzEventService $jazzEventService,
      private IHistoryService $historyService,
      private IKidsEventService $kidsEventService,
      private IHistoryVenueRepository $historyVenueRepository,
      private IArtistService $artistService,
      private IJazzPassService $jazzPassService,
      private IEventService $eventService,
      private IPersonalProgramService $programService,
      private IDanceEventService $danceEventService
   ) {}

   public function savePaidTicket(TicketModel $ticket, string $stripeId): bool
   {
      return $this->ticketRepository->savePaidTicket($ticket, $stripeId);
   }

   public function getByToken(string $token): ?array
   {
      return $this->ticketRepository->getByToken($token);
   }

   public function markAsScanned(string $token): bool
   {
      return $this->ticketRepository->markAsScanned($token);
   }

   public function savePendingTicket(TicketModel $ticket, string $tempOrderId): bool
   {
      return $this->ticketRepository->savePendingTicket($ticket, $tempOrderId);
   }

   public function updateTicketsToPaid(string $orderId, string $actualStripeId): bool
   {
      return $this->ticketRepository->updateTicketsToPaid($orderId, $actualStripeId);
   }

   public function markAsExpired(string $orderId): bool
   {
      return $this->ticketRepository->markAsExpired($orderId);
   }

   
   public function getAllWithDetails(): array
   {
      return $this->ticketRepository->getAllWithDetails();
   }

   public function getTicketsByOrderId(string $orderId): array
   {
      return $this->ticketRepository->getTicketsByOrderId($orderId);
   }

   public function getUserTickets(int $userId): array
   {
      return $this->ticketRepository->getTicketsByUserId($userId);
   }

   public function getUserTicketsPaginated(int $userId, int $page = 1): array
   {
      $limit = 5; 
      $tickets = $this->ticketRepository->getTicketsByUserIdPaginated($userId, $page, $limit);
      $totalTickets = $this->ticketRepository->countTicketsByUserId($userId);
      $hydratedTickets = $this->hydrateTickets($tickets);

      return [
         'tickets'       => $hydratedTickets,
         'total_pages'   => ceil($totalTickets / $limit),
         'current_page'  => $page,
         'total_results' => $totalTickets
      ];
   }

   public function countTicketsByUserId(int $userId): int {
      return $this->ticketRepository->countTicketsByUserId($userId);
   }

   public function hydrateTickets(array $tickets): array
   {
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

         if (strcasecmp($event->getEventType()->value, 'jazz') === 0) {
            $jazzEvent = $this->jazzEventService->getJazzEventById($subId);

            if ($jazzEvent) {
               $artist = $this->artistService->getArtistById($jazzEvent->getArtistId());
               if ($artist instanceof \App\Models\ArtistModel) {
                     $jazzEvent->setArtist($artist);
               }

               $venueInfo = $this->jazzEventService->getVenueInfoByJazzEventId($jazzEvent->getId());
               if (!empty($venueInfo['VenueName'])) {
                     $location = $venueInfo['VenueName'] . (!empty($venueInfo['HallName']) ? ' - ' . $venueInfo['HallName'] : '');
                     $jazzEvent->setVenueName($location);
               }

               $event->setDetails($jazzEvent);
            }
         }

         if (strcasecmp($event->getEventType()->value, 'jazzpass') === 0) {
            $jazzPass = $this->jazzPassService->getPassById($subId);

            if ($jazzPass) {
               $event->setDetails($jazzPass);
            }
         }

         if (strcasecmp($event->getEventType()->value, 'tour') === 0) {
            $targetHistoryId = $event->getSubEventId(); 
            $allSessions = $this->historyService->getAllSessions();
            $historyEvent = null;

            foreach ($allSessions as $session) {
               if ($session->getHistoryEventId() === $targetHistoryId) {
                     $historyEvent = $session;
                     break;
               }
            }

            if ($historyEvent) {
               $stops = $this->historyVenueRepository->getStopsByEventId($targetHistoryId);

               if (!empty($stops)) {
                     $firstStop = $stops[0];

                     $venue = new HistoryVenueModel(
                        (int)($firstStop['venueId'] ?? 0),
                        $firstStop['venueName'] ?? '',
                        $firstStop['details'] ?? null,
                        $firstStop['location'] ?? null,
                        isset($firstStop['imageId']) ? (int)$firstStop['imageId'] : null,
                        $firstStop['imgURL'] ?? null,
                        $firstStop['altText'] ?? null
                     );

                     $historyEvent->setVenue($venue);
               }
               $event->setDetails($historyEvent);
            }
         }

         if (strcasecmp($event->getEventType()->value, 'kids') === 0) {
            $kidsEvent = $this->kidsEventService->getEventById($subId);
            if ($kidsEvent) {
               $event->setDetails($kidsEvent);
            }
         }


         if (strcasecmp($event->getEventType()->value, 'dance') === 0) {
            $danceEvent = $this->danceEventService->getDanceEventById($subId);

            if ($danceEvent) {
               $artist = $this->artistService->getArtistById($danceEvent->getArtistId());
               
               if ($artist instanceof ArtistModel) {
                     $danceEvent->setArtist($artist);
               }

               $venueInfo = $this->danceEventService->getVenueInfoByDanceEventId($danceEvent->getId());
               if ($venueInfo) {
                     $danceEvent->setVenueName($venueInfo['VenueName']);
               }

               $event->setDetails($danceEvent);
            }
         }
      }

      return $tickets;
   }

   public function addToProgram(array $data, ?int $userId): void
   {
      $subEventId = (int)($data['event_id'] ?? 0);
      $numberOfPeople = (int)($data['number_of_people'] ?? 1);
      $eventType = $data['event_type'] ?? '';
      $programItemId = $data['program_item_id'] ?? null;

      // yummy 
      if (strcasecmp($eventType, 'reservation') === 0) {
         if ($programItemId) {
            $subEventId = (int)$programItemId;
         } else {
            throw new \Exception("Please select a specific time slot.");
         }
      }

      // jazz
      if (strcasecmp($eventType, 'jazz') === 0) {
         $jazzEvent = $this->jazzEventService->getJazzEventById($subEventId);
         if (!$jazzEvent || $jazzEvent->getTicketsLeft() < $numberOfPeople) {
            $remaining = $jazzEvent ? $jazzEvent->getTicketsLeft() : 0;
            throw new \Exception("Sorry, there are only $remaining tickets left for this jazz event.");
         }
      }

      // jazz pass
      if (strcasecmp($eventType, 'jazzpass') === 0) {
         $jazzPass = $this->jazzPassService->getPassById($subEventId);
         if (!$jazzPass || $jazzPass->getTicketsLeft() < $numberOfPeople) {
            $remaining = $jazzPass ? $jazzPass->getTicketsLeft() : 0;
            throw new \Exception("Sorry, there are only $remaining passes left.");
         }
      }

      $eventId = $this->eventService->checkEventType($subEventId, $eventType);

      if ($eventId === 0) {
         throw new \Exception("Configuration Error: No Event found for Type: $eventType.");
      }

      $this->programService->addTicketToProgram(
         $eventId,
         $numberOfPeople,
         $userId,
         $programItemId
      );
   }

   public function updateProgramQuantity(int $itemId, string $action): void
   {
      if (!isset($_SESSION['program'])) return;
      $program = $_SESSION['program'];
      $tickets = $program->getTickets();

      foreach ($tickets as $ticket) {
         if ($ticket->getProgramItemId() === $itemId) {
            $currentQty = $ticket->getNumberOfPeople();

            if ($action === 'increase') {
               $ticket->setNumberOfPeople($currentQty + 1);
            } elseif ($action === 'decrease' && $currentQty > 1) {
               $ticket->setNumberOfPeople($currentQty - 1);
            }
            break;
         }
      }
      $_SESSION['program'] = $program;
   }
 public function getPaginatedTickets(int $page = 1): array
{
    $limit = 10;

    $tickets = $this->ticketRepository->getAllWithDetailsPaginated($page, $limit);
    $total = $this->ticketRepository->countAllWithDetails();

    return [
        'tickets' => $tickets,
        'total_pages' => ceil($total / $limit),
        'current_page' => $page,
        'total_results' => $total
    ];
}
public function getExportData(array $requestedColumns): array//check
{
    $tickets = $this->ticketRepository->getAllWithDetails();

    if (empty($tickets)) {
        throw new \Exception("No data available");
    }

    $validColumns = array_keys($tickets[0]);

    $selectedColumns = array_intersect($requestedColumns, $validColumns);

    if (empty($selectedColumns)) {
        throw new \Exception("No valid columns selected");
    }

    return [
        'columns' => $selectedColumns,
        'rows' => $tickets
    ];
}
}
