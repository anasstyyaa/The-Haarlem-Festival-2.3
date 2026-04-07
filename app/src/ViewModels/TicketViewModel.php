<?php

namespace App\ViewModels;

use App\Models\TicketModel;

class TicketViewModel {
    public string $title;
    public string $image;
    public string $location;
    public string $date;
    public string $startTime;
    public string $category;
    public string $language = ''; 
    public int $guestCount;
    public float $totalPrice;
    public float $unitPrice;
    public int $programItemId; 

    public function __construct(TicketModel $ticket) {
        $event = $ticket->getEvent();
        $details = $event->getDetails();

       // bacis ticket info
        $this->programItemId = $ticket->getProgramItemId();
        $this->guestCount = $ticket->getNumberOfPeople();
        $this->totalPrice = $ticket->getTotalPrice();
        $this->unitPrice = $ticket->getUnitPrice();
        
        // metadata
        $this->category = $event->getEventType()->name;
        $this->image = "/assets/images/placeholder.jpg";
        $this->title = "Event " . $event->getSubEventId();
        $this->location = "Haarlem";
        $this->date = '';
        $this->startTime = '';

        // routing the mapping logic 
        if (is_array($details)) {
            $this->mapArrayDetails($details);
        } elseif ($details) {
            $this->mapObjectDetails($details, $event);
        }
    }

    private function mapArrayDetails(array $details): void {
        // kids event 
        if (isset($details['name'])) {
            $this->title = $details['name'];
            $this->location = $details['location'] ?? $this->location;
            $this->date = $details['date'] ?? '';
            $this->startTime = $details['startTime'] ?? '';
        } 
        
        // jazz 
        if (isset($details['artist'])) {
            $artist = $details['artist'];
            $venueInfo = $details['venueInfo'] ?? null;
            $jazzEvent = $details['jazzEvent'] ?? null;

            if ($artist && method_exists($artist, 'getName')) {
                $this->title = $artist->getName();
            }

            if ($artist && method_exists($artist, 'getImageUrl')) {
                $this->image = $artist->getImageUrl() ?: $this->image;
            }

            if (!empty($venueInfo['VenueName'])) {
                $this->location = $venueInfo['VenueName'];
                if (!empty($venueInfo['HallName'])) {
                    $this->location .= ', ' . $venueInfo['HallName'];
                }
            }

            if ($jazzEvent && method_exists($jazzEvent, 'getStartDateTime')) {
                $ts = strtotime($jazzEvent->getStartDateTime());
                $this->date = date('Y-m-d', $ts);
                $this->startTime = date('H:i', $ts);
            }
        }
    }

    private function mapObjectDetails($details, $event): void {
        // objects check
        if (method_exists($details, 'getTitle')) $this->title = $details->getTitle();
        if (method_exists($details, 'getName')) $this->title = $details->getName();
        if (method_exists($details, 'getLocation')) $this->location = $details->getLocation();
        if (method_exists($details, 'getLanguage')) $this->language = $details->getLanguage();

        // image checks
        if (method_exists($details, 'getImageUrl') && $details->getImageUrl()) {
            $this->image = $details->getImageUrl();
        }

        // tour logic
        if ($event->getEventType()->value === 'tour' && method_exists($details, 'getVenue')) {
            $venue = $details->getVenue();
            if ($venue && $venue->getImgURL()) {
                $this->image = $venue->getImgURL();
            }
        }

        // restaurant logic 
        if ($details instanceof \App\Models\Yummy\RestaurantModel) {
            $session = $details->getSessionData();
            if ($session) {
                $this->date = date('Y-m-d', strtotime($session->getDate()));
                $this->startTime = date('H:i', strtotime($session->getStartTime()));
            }
        }

        // specific slot logic 
        if (method_exists($details, 'getSlotDate')) $this->date = $details->getSlotDate();
        if (method_exists($details, 'getStartTime')) $this->startTime = date('H:i', strtotime($details->getStartTime()));

        // jazz pass mapping 
        if ($event->getEventType()->value === 'jazzpass') {
            $this->date = $this->mapPassDate($this->title);
            $this->startTime = 'Festival hours';
        }
    }

    private function mapPassDate(string $title): string {
        $title = strtolower($title);
        if (str_contains($title, 'thursday')) return '2026-07-23';
        if (str_contains($title, 'friday'))   return '2026-07-24';
        if (str_contains($title, 'saturday')) return '2026-07-25';
        return '23–25 Jul 2026';
    }
}