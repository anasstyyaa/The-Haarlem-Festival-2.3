<?php
namespace App\Models; 

class ArtistModel{
    private int $ArtistID;
    private string $ArtistName;
    private ?string $ShortDescription;
    private ?string $Description;
    private ?string $ImageURL;
    private string $created_at; 
    private ?string $updated_at;
    private ?string $deleted_at;

    public function getId(): int { 
        return $this->ArtistID; 
    }
    public function getName(): string { 
        return $this->ArtistName; 
    }
    public function getShortDescription(): ?string { 
        return $this->ShortDescription; 
    }
    public function getDescription(): ?string { 
        return $this->Description; 
    }
    public function getImageUrl(): ?string { 
        return $this->ImageURL; 
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
        $this->ArtistName = $name;
    }
    public function setShortDescription(?string $shortDescription): void { 
        $this->ShortDescription = $shortDescription; 
    }
    public function setDescription(?string $description): void { 
        $this->Description = $description; 
    }
    public function setImageUrl(?string $image_url): void { 
        $this->ImageURL = $image_url; 
    }
}