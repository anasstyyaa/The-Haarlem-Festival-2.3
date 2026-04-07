<?php

namespace App\Models;

class KidsEventModel
{
    private int $id;
    private string $day;
    private string $startTime;
    private string $endTime;
    private string $type;
    private string $location;
    private int $limit;
    private string $eventDate;

public function __construct(
    int $id,
    string $day,
    string $startTime,
    string $endTime,
    string $type,
    string $location,
    int $limit,
    string $eventDate   
) {
    $this->id = $id;
    $this->day = $day;
    $this->startTime = $startTime;
    $this->endTime = $endTime;
    $this->type = $type;
    $this->location = $location;
    $this->limit = $limit;
    $this->eventDate = $eventDate; 
}
public function getEventDate(): string
{
    return $this->eventDate;
}
public function setEventDate(string $eventDate): void
{
    $this->eventDate = $eventDate;
}
public function getLimit(): int
{
    return $this->limit;
}
public function setLimit(int $limit): void
{
    $this->limit = $limit;
}

public function getType(): string
{
    return $this->type;
}

public function getLocation(): string
{
    return $this->location;
}

    public function getId(): int
    {
        return $this->id;
    }

    public function getDay(): string
    {
        return $this->day;
    }

    public function getStartTime(): string
    {
        return $this->startTime;
    }

    public function getEndTime(): string
    {
        return $this->endTime;
    }
}
