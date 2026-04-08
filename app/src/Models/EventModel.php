<?php

namespace App\Models;

use App\Models\Enums\EventTypeEnum;

class EventModel
{
    private int $id;
    private EventTypeEnum $eventType;
    private int $subEventId;
    private $details = null;


    public function __construct(int $id, EventTypeEnum $eventType, int $subEventId, $details = null)
    {
        $this->id = $id;
        $this->eventType = $eventType;
        $this->subEventId = $subEventId;
        $this->details = $details;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEventType(): EventTypeEnum
    {
        return $this->eventType;
    }

    public function getSubEventId(): int
    {
        return $this->subEventId;
    }

    public function setDetails($details) {
        $this->details = $details;
    }

    public function getDetails() {
        return $this->details;
    }

    public function getDisplayData(): array {
        $details = $this->getDetails();
        $type = $this->getEventType();
        
        $data = [
            'title' => 'Festival Event',
            'venue' => 'Festival Grounds',
            'startTime' => 'TBD',
            'icon' => 'bi-calendar-event'
        ];

        if ($details === null) {
            return $data;
        }

        
        if ($type === EventTypeEnum::JazzPass && $details instanceof \App\Models\JazzPassModel) {
            $data['title'] = $details->getTitle();
            $data['venue'] = "All Jazz Venues";
            $data['startTime'] = "Festival Period";
            $data['icon'] = 'bi-stars';
        } 
      
        elseif ($type === EventTypeEnum::JazzEvent && $details instanceof \App\Models\JazzEventModel) {
            $data['startTime'] = $details->getStartDateTime();
            $data['title'] = "Jazz Performance"; // You can link Artist here if needed
            $data['venue'] = "Jazz Venue"; 
            $data['icon'] = 'bi-music-note-beamed';
        } 
       
        elseif ($type === EventTypeEnum::Reservation && $details instanceof \App\Models\Yummy\RestaurantModel) {
            $session = $details->getSessionData();
            $data['startTime'] = $session ? $session->getStartTime() : 'TBD';
            $data['venue'] = $details->getLocation() ?? 'Haarlem';
            $data['title'] = $details->getName();
            $data['icon'] = 'bi-egg-fried';
        } 
  
        elseif ($type === EventTypeEnum::Tour && $details instanceof \App\Models\HistoryEventModel) {
            $data['startTime'] = $details->getStartTime();
            $data['venue'] = $details->getVenue() ? $details->getVenue()->getLocation() : 'Haarlem Center';
            $data['title'] = "History Tour";
            $data['icon'] = 'bi-map';
        } 
       
        elseif ($type === EventTypeEnum::KidsEvent && $details instanceof \App\Models\KidsEventModel) {
            $data['title'] = $details->getType();
            $data['venue'] = $details->getLocation();
            $data['startTime'] = $details->getEventDate() . ' ' . $details->getStartTime();
            $data['icon'] = 'bi-balloon-heart';
        }
        
        return $data;
    }
}