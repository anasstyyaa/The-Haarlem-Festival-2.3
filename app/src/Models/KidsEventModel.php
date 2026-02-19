<?php

namespace App\Models;

class KidsEventModel
{
    private int $id;
    private string $day;
    private string $startTime;
    private string $endTime;

    public function __construct(int $id, string $day, string $startTime, string $endTime)
    {
        $this->id = $id;
        $this->day = $day;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
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
