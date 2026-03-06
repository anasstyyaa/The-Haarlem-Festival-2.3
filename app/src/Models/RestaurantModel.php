<?php

namespace App\Models;

class RestaurantModel {
    private int $id;
    private string $name;
    private ?string $description;
    private ?string $location;
    private ?string $cuisine;
    private ?string $image_url;
    private string $created_at;
    private ?string $updated_at;
    private ?string $deleted_at;
    private ?string $long_description;
    private ?int $chef_id; 


    public function getId(): int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function getLocation(): ?string {
        return $this->location;
    }

    public function getCuisine(): ?string {
        return $this->cuisine;
    }

    public function getImageUrl(): ?string {
        return $this->image_url;
    }

    public function getLongDescription(): ?string {
        return $this->long_description;
    }

    public function getChefId(): ?int {
        return $this->chef_id;
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




    public function setName(string $name): void {
        $this->name = $name;
    }

    public function setDescription(?string $description): void {
        $this->description = $description;
    }

    public function setLocation(?string $location): void {
        $this->location = $location;
    }

    public function setCuisine(?string $cuisine): void {
        $this->cuisine = $cuisine;
    }

    public function setImageUrl(string $image_url): void {
        $this->image_url = $image_url;
    }

    public function setLongDescription(?string $long_description): void {
        $this->long_description = $long_description;
    }

    public function setChefId(?int $chef_id): void {
        $this->chef_id = $chef_id;
    }
}