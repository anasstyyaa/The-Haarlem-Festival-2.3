<?php

namespace App\Models\Yummy;

use JsonSerializable;

class RestaurantSessionModel implements JsonSerializable {
    private int $id = 0;
    private int $restaurant_id = 0;
    private string $date = '';
    private string $startTime = '';
    private int $available_slots = 0;
    private array $selectedTimes = [];

    public function getRestaurantId(): int { return $this->restaurant_id; }
    public function getId(): int { return $this->id; }
    public function getStartTime(): string { return $this->startTime; }
    public function getDate(): string { return $this->date; }
    public function getAvailableSlots(): int { return $this->available_slots; }
    public function getSelectedTimes(): array { return $this->selectedTimes; }

    public function setRestaurantId(int $id): void { $this->restaurant_id = $id; }
    public function setStartTime(string $startTime): void {$this->startTime = $startTime;}
    public function setId(int $id): void { $this->id = $id;}
    public function setDate(string $date): void { $this->date = $date; }
    public function setAvailableSlots(int $slots): void { $this->available_slots = $slots; }
    public function setSelectedTimes(array $times): void { $this->selectedTimes = $times; }
    
    public function jsonSerialize(): mixed {
        return [
            'id'             => $this->id,
            'startTime'      => $this->startTime,
            'availableSlots' => $this->available_slots 
        ];
    }

}