<?php

namespace App\ViewModels;

class JazzEventViewModel
{
    private ?int $EventID = null;
    private int $JazzEventID;
    private string $StartDateTime;
    private ?string $EndDateTime = null;
    private float $Price;
    private ?string $VenueName = null;
    private ?string $HallName = null;

    public function getEventID(): ?int
    {
        return $this->EventID;
    }

    public function getJazzEventID(): int
    {
        return $this->JazzEventID;
    }

    public function getStartDateTime(): string
    {
        return $this->StartDateTime;
    }

    public function getEndDateTime(): ?string
    {
        return $this->EndDateTime;
    }

    public function getPrice(): float
    {
        return $this->Price;
    }

    public function getVenueName(): ?string
    {
        return $this->VenueName;
    }

    public function getHallName(): ?string
    {
        return $this->HallName;
    }
}