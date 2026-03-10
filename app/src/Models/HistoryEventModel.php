<?php

namespace App\Models;

class HistoryEventModel
{
    private int $eventId;
    private int $historyEventId;
    private string $slotDate;
    private string $startTime;
    private string $language;
    private int $duration;
    private int $minAge;
    private int $capacity;
    private float $priceIndividual;
    private float $priceFamily;

    public function __construct(int $eventId, int $historyEventId, string $slotDate, string $startTime ,string $language , int $duration, int $minAge, int $capacity, float $priceIndividual, float $priceFamily
    ) {
        $this->eventId = $eventId;
        $this->historyEventId = $historyEventId;
        $this->slotDate = $slotDate;
        $this->startTime = $startTime;
        $this->language = $language;
        $this->duration = $duration;
        $this->minAge = $minAge;
        $this->capacity = $capacity;
        $this->priceIndividual = $priceIndividual;
        $this->priceFamily = $priceFamily;
    }

    public function getEventId(): int
    {
        return $this->eventId;
    }

    public function getHistoryEventId(): int
    {
        return $this->historyEventId;
    }

    public function getSlotDate(): string
    {
        return $this->slotDate;
    }

    public function getStartTime(): string
    {
        return $this->startTime;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function getMinAge(): int
    {
        return $this->minAge;
    }

    public function getCapacity(): int
    {
        return $this->capacity;
    }

    public function getPriceIndividual(): float
    {
        return $this->priceIndividual;
    }

    public function getPriceFamily(): float
    {
        return $this->priceFamily;
    }
}