<?php 
namespace App\Models;

class ExtraKidsEventModel
{
    private int $id;
    private string $name;
    private ?string $description;
    private ?string $imageURL = null;

    public function getId(): int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function getImageUrl(): ?string {
        return $this->imageURL;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function setDescription(?string $description): void {
        $this->description = $description;
    }

    public function setImageUrl(?string $imageUrl): void {
        $this->imageURL = $imageUrl;
    }
}