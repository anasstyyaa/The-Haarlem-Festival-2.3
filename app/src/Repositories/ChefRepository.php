<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Repositories\Interfaces\IChefRepository;
use App\Models\ChefModel;
use PDO;

class ChefRepository extends Repository implements IChefRepository {

    public function getAll(): array {
        $stmt = $this->connection->prepare("SELECT * FROM Chefs");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, ChefModel::class);
    }

    public function getById(int $id): ?ChefModel {
        $stmt = $this->connection->prepare("SELECT * FROM Chefs WHERE id = :id");
        $stmt->setFetchMode(PDO::FETCH_CLASS, ChefModel::class);
        $stmt->execute(['id' => $id]);
        
        $chef = $stmt->fetch();
        return $chef ?: null;
    }

    public function create(ChefModel $chef): bool {
        $sql = "INSERT INTO Chefs (name, experience_years, description, image_url) VALUES (:name, :experience_years, :desc, :img)";
        return $this->connection->prepare($sql)->execute([
            'name' => $chef->getName(),
            'experience_years' => $chef->getExperienceYears(),
            'desc' => $chef->getDescription(),
            'img'  => $chef->getImageUrl()
        ]);
    }

    public function update(ChefModel $chef): bool {
        $sql = "UPDATE Chefs SET name = :name, description = :desc, image_url = :img, experience_years = :experience_years WHERE id = :id";
        return $this->connection->prepare($sql)->execute([
            'id'   => $chef->getId(),
            'name' => $chef->getName(),
            'desc' => $chef->getDescription(),
            'img'  => $chef->getImageUrl(),
            'experience_years' => $chef->getExperienceYears()
        ]);
    } 
    
    public function delete(int $id): bool {
        $stmt = $this->connection->prepare("DELETE FROM Chefs WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}