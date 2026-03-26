<?php

namespace App\Models;

use App\Models\Enums\EventTypeEnum;

class EventModel//test
{
    private int $id;
    private EventTypeEnum $eventType;
    private int $subEventId;

    public function __construct(int $id, EventTypeEnum $eventType, int $subEventId)
    {
        $this->id = $id;
        $this->eventType = $eventType;
        $this->subEventId = $subEventId;
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
}