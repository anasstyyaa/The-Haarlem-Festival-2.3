<?php

namespace App\Models\Yummy;

class YummyEventModel {
    private int $id;
    private int $restaurant_id;
    private string $date;
    private string $startTime;
    private int $duration;
    private float $price;
    private ?string $comment;

    public function getId(): int { return $this->id; }
    public function getRestaurantId(): int { return $this->restaurant_id; }
    public function getDate(): string { return $this->date; }
    public function getStartTime(): string { return $this->startTime; }
    public function getDuration(): int { return $this->duration; }
    public function getPrice(): float { return $this->price; }
    public function getComment(): ?string { return $this->comment; }

    public function setRestaurantId(int $id): void { $this->restaurant_id = $id; }
    public function setDate(string $date): void { $this->date = $date; }
    public function setStartTime(string $time): void { $this->startTime = $time; }
    public function setDuration(int $duration): void { $this->duration = $duration; }
    public function setPrice(float $price): void { $this->price = $price; }
    public function setComment(?string $comment): void { $this->comment = $comment; }
}