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

public function __construct(int $id, string $day, string $startTime, string $endTime, string $type = 'Teylers Secret', string $location = 'Teylers Museum, Haarlem')
{
    $this->id = $id;
    $this->day = $day;
    $this->startTime = $startTime;
    $this->endTime = $endTime;
    $this->type = $type;
    $this->location = $location;
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
