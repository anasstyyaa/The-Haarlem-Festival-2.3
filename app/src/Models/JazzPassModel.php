<?php

namespace App\Models;

class JazzPassModel
{
    private int $JazzPassID;
    private string $Title;
    private ?string $Description;
    private float $Price;
    private ?string $ImageURL;
    private bool $IsActive;
    private int $Capacity; 
    private int $TicketsLeft; 
    private string $Created_At;
    private ?string $Updated_At;
    private ?string $Deleted_At;

    public function getId(): int
    {
        return $this->JazzPassID;
    }

    public function getTitle(): string
    {
        return $this->Title;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function getPrice(): float
    {
        return $this->Price;
    }

    public function getImageUrl(): ?string
    {
        return $this->ImageURL;
    }

    public function isActive(): bool
    {
        return $this->IsActive;
    }

    public function getCapacity(): int
    {
        return $this->Capacity;
    }

    public function getTicketsLeft(): int
    {
        return $this->TicketsLeft;
    }

    public function getCreatedAt(): string
    {
        return $this->Created_At;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->Updated_At;
    }

    public function getDeletedAt(): ?string
    {
        return $this->Deleted_At;
    }

    public function setTitle(string $title): void
    {
        $this->Title = $title;
    }

    public function setDescription(?string $description): void
    {
        $this->Description = $description;
    }

    public function setPrice(float $price): void
    {
        $this->Price = $price;
    }

    public function setImageUrl(?string $imageUrl): void
    {
        $this->ImageURL = $imageUrl;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->IsActive = $isActive;
    }

    public function setCapacity(int $capacity): void
    {
        $this->Capacity = $capacity;
    }

    public function setTicketsLeft(int $ticketsLeft): void
    {
        $this->TicketsLeft = $ticketsLeft;
    }
}