<?php

namespace App\ViewModels;

use App\Models\TicketModel;
use App\Models\Yummy\RestaurantModel;
use App\Models\HistoryEventModel; 
use App\Models\JazzEventModel;
use App\Models\KidsEventModel; 
use App\Models\DanceEventModel; 

class TicketViewModel {
    public string $title = '';
    public ?string $image = '';    
    public ?string $location = ''; 
    public ?string $date = '';
    public string $startTime = '';
    public string $category = '';
    public string $language = ''; 
    public int $guestCount = 0;
    public float $totalPrice = 0.0;
    public float $unitPrice = 0.0;
    public ?int $programItemId = 0; 
    public ?string $token = null;
    public string $endTime = '';

    public function __construct(TicketModel $ticket) {
        $event = $ticket->getEvent();
        $details = $event->getDetails();

        $this->token = $ticket->getUniqueTicketToken(); 
        $this->programItemId = $ticket->getProgramItemId();
        $this->guestCount = $ticket->getNumberOfPeople();
        $this->totalPrice = $ticket->getTotalPrice();
        $this->unitPrice = $ticket->getUnitPrice();
        $this->category = $event->getEventType()->name;
        $this->image = "/assets/images/placeholder.jpg";
        $this->title = "Event " . $event->getSubEventId();
        $this->location = "Haarlem";
        $this->date = '';      
        $this->startTime = '';
        $this->endTime = '';

        $this->mapObjectDetails($details);
    }

    private function mapObjectDetails($details): void {
        if ($details instanceof RestaurantModel) {
            $this->title = $details->getName();
            $this->location = $details->getLocation();
            $this->image = $details->getImageUrl();
            $session = $details->getSessionData();
            if ($session) {
                $this->date = $session->getDate();
                $this->startTime = date('H:i', strtotime($session->getStartTime()));
                $duration = $details->getSessionDuration() ?? 90;
                $this->endTime = date('H:i', strtotime($session->getStartTime()) + ($duration * 60));
            }
        }

        if ($details instanceof \App\Models\HistoryEventModel) {
            $venue = $details->getVenue();
            $this->title = "History Tour (" . $details->getLanguage() . ")";
            $this->location = $venue ? $venue->getVenueName() : "Haarlem Center";
            $this->date = $details->getSlotDate();
            $this->startTime = date('H:i', strtotime($details->getStartTime()));
            $this->image = $venue ? $venue->getImgURL() : null;
            $startTimeTs = strtotime($details->getStartTime());
            $endTimeTs = $startTimeTs + ($details->getDuration() * 60);
            $this->endTime = date('H:i', $endTimeTs);
            
            $this->language = $details->getLanguage();
        }

        if ($details instanceof \App\Models\JazzEventModel) {
            $artist = $details->getArtist();
            $this->title = $artist ? $artist->getName() : "Jazz Performance";
            $this->location = $details->getVenueName();
            $this->image = $artist ? $artist->getImageUrl() : null;
            $startTs = strtotime($details->getStartDateTime());
            $this->date = date('Y-m-d', $startTs);
            $this->startTime = date('H:i', $startTs);
            
            if ($details->getEndDateTime()) {
                $this->endTime = date('H:i', strtotime($details->getEndDateTime()));
            }
        }

        // 4. Kids Mapping
        if ($details instanceof KidsEventModel) {
            $this->title = $details->getType();
            $this->location = $details->getLocation();
            $this->date = $details->getEventDate();
            $this->image = "/assets/images/home/Tyler1.png";
            $this->startTime = date('H:i', strtotime($details->getStartTime()));
            $this->endTime = date('H:i', strtotime($details->getEndTime()));
        }

        if ($details instanceof \App\Models\DanceEventModel) {
            $artist = $details->getArtist();
            $this->title = $details->getDisplayTitle() ?? ($artist ? $artist->getName() : "Dance Event");
            
            // Map the DJ/Artist's specific image
            $this->image = $artist ? $artist->getImageUrl() : null;
            
            $this->location = $details->getVenueName() ?? "Haarlem";
            $startTs = strtotime($details->getStartDateTime());
            $this->date = date('Y-m-d', $startTs);
            $this->startTime = date('H:i', $startTs);
            
            if ($details->getEndDateTime()) {
                $this->endTime = date('H:i', strtotime($details->getEndDateTime()));
            }
        }
    }
}