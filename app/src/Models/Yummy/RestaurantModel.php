<?php

namespace App\Models\Yummy;

class RestaurantModel {
    private int $id = 0;
    private string $name = ""; 
    private ?string $description = null;
    private ?string $location = null;
    private ?string $cuisine = null;
    private ?string $image_url = null;
    private ?string $long_description = null;
    
    private ?int $chef_id = null; 
    private string $created_at = "";
    private ?string $updated_at = null;
    private ?string $deleted_at = null;

    private ?int $session_duration = 90;
    private ?float $reservation_fee = 10.00;
    private ?int $total_slots = 35;


    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getDescription(): ?string { return $this->description;}
    public function getLocation(): ?string { return $this->location;}
    public function getCuisine(): ?string { return $this->cuisine;}
    public function getImageUrl(): ?string { return $this->image_url; }
    public function getLongDescription(): ?string { return $this->long_description; }
    public function getChefId(): ?int { return $this->chef_id; }

    public function getSessionDuration(): ?int { return $this->session_duration; }
    public function getReservationFee(): ?float { return $this->reservation_fee; }
    public function getTotalSlots(): ?int { return $this->total_slots; }

    public function getCreatedAt(): string { return $this->created_at;}
    public function getUpdatedAt(): ?string { return $this->updated_at;}
    public function getDeletedAt(): ?string { return $this->deleted_at; }


    public function setName(string $name): void { $this->name = $name; }
    public function setDescription(?string $description): void { $this->description = $description; }
    public function setLocation(?string $location): void { $this->location = $location;}
    public function setCuisine(?string $cuisine): void { $this->cuisine = $cuisine; }
    public function setImageUrl(string $image_url): void { $this->image_url = $image_url;}
    public function setLongDescription(?string $long_description): void { $this->long_description = $long_description;}
    public function setChefId(?int $chef_id): void { $this->chef_id = $chef_id;}

    public function setSessionDuration(?int $duration): void { $this->session_duration = $duration; }
    public function setReservationFee(?float $fee): void { $this->reservation_fee = $fee; }
    public function setTotalSlots(?int $slots): void { $this->total_slots = $slots; }
}