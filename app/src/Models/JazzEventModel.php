<?php

namespace App\Models; 

class JazzEventModel{
    private int $JazzEventID;
    private int $ArtistID;
    private int $JazzVenueID;
    private string $StartDateTime;
    private ?string $EndDateTime;
    private float $Price;
    private int $Capacity; 
    private int $TicketsLeft; 
    private ?string $deleted_at;
    private ?string $updated_at;
    private string $created_at;


    public function getId(): int {
        return $this->JazzEventID;
    }

    public function getArtistId(): int {
        return $this->ArtistID;
    }

    public function getJazzVenueId(): int {
        return $this->JazzVenueID;
    }

    public function getStartDateTime(): string {
        return $this->StartDateTime;
    }

    public function getEndDateTime(): ?string {
        return $this->EndDateTime;
    }

    public function getPrice(): float {
        return $this->Price;
    }

    public function getCapacity(): int {
        return $this->Capacity;
    }
    public function getTicketsLeft(): int {
        return $this->TicketsLeft;
    }

    public function getCreatedAt(): string {
    return $this->created_at;
}

    public function getUpdatedAt(): ?string {
        return $this->updated_at;
    }

    public function getDeletedAt(): ?string {
        return $this->deleted_at;
    }


    
    public function setArtistId(int $artistId): void {
        $this->ArtistID = $artistId;
    }

    public function setJazzVenueId(int $jazzVenueId): void {
        $this->JazzVenueID = $jazzVenueId;
    }

    public function setStartDateTime(string $startDateTime): void {
        $this->StartDateTime = $startDateTime;
    }

    public function setEndDateTime(?string $endDateTime): void {
        $this->EndDateTime = $endDateTime;
    }

    public function setPrice(float $price): void {
        $this->Price = $price;
    }

    public function setCapacity(int $capacity): void {
        $this->Capacity = $capacity;
    }

    public function setTicketsLeft(int $ticketsLeft): void {
        $this->TicketsLeft = $ticketsLeft;
    }
}
