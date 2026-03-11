<?php 

namespace App\Models\Yummy;

class ChefModel {
    private int $id;
    private string $name;
    private ?string $description;
    private ?string $image_url;
    private ?int $experience_years;

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
        return $this->image_url; 
    }

    public function setId(int $id): void { 
        $this->id = $id; 
    }

    public function setName(string $name): void { 
        $this->name = $name; 
    }

    public function setDescription(?string $description): void {
        $this->description = $description;
    }

    public function setImageUrl(?string $image_url): void { 
        $this->image_url = $image_url; 
    }

    public function getExperienceYears(): ?int {
        return $this->experience_years;
    }

    public function setExperienceYears(?int $experience_years): void {
        $this->experience_years = $experience_years;
    }
}