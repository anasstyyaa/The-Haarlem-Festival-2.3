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
}