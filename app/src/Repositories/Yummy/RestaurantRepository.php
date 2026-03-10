<?php 

namespace App\Repositories\Yummy;

use App\Framework\Repository;
use App\Models\Yummy\RestaurantModel;
use App\Repositories\Interfaces\Yummy\IRestaurantRepository;
use PDO;

class RestaurantRepository extends Repository implements IRestaurantRepository{

    public function getAllActive(): array {
        $stmt = $this->connection->prepare("SELECT * FROM Restaurants WHERE deleted_at IS NULL");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, RestaurantModel::class);
    }

    public function getById(int $id): ? RestaurantModel {
        $stmt = $this->connection->prepare("SELECT * FROM Restaurants WHERE id = :id AND deleted_at IS NULL");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetchObject(RestaurantModel::class);
        return $result ?: null;
    }

    public function create(RestaurantModel $restaurant): bool {
        $sql = "INSERT INTO Restaurants (name, description, location, cuisine, image_url, long_description, chef_id, session_duration, reservation_fee, total_slots) 
                VALUES (:name, :description, :location, :cuisine, :image_url, :long_description, :chef_id, :duration, :fee, :slots)";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute([
            'name' => $restaurant->getName(),
            'description' => $restaurant->getDescription(),
            'location' => $restaurant->getLocation(),
            'cuisine' => $restaurant->getCuisine(),
            'image_url' => $restaurant->getImageUrl(),
            'long_description' => $restaurant->getLongDescription(),
            'chef_id' => $restaurant->getChefId(),
            'duration' => $restaurant->getSessionDuration(),
            'fee' => $restaurant->getReservationFee(),
            'slots' => $restaurant->getTotalSlots()
        ]);
    }

    public function update(RestaurantModel $restaurant): bool {
        $sql = "UPDATE Restaurants SET name = :name, description = :description, 
                location = :location, cuisine = :cuisine, image_url = :image_url, long_description = :long_description, chef_id = :chef_id, session_duration = :duration, reservation_fee = :fee, total_slots = :slots
                WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute([
            'id' => $restaurant->getId(),
            'name' => $restaurant->getName(),
            'description' => $restaurant->getDescription(),
            'location' => $restaurant->getLocation(),
            'cuisine' => $restaurant->getCuisine(),
            'image_url' => $restaurant->getImageUrl(),
            'long_description' => $restaurant->getLongDescription(),
            'chef_id' => $restaurant->getChefId(), 
            'duration' => $restaurant->getSessionDuration(),
            'fee' => $restaurant->getReservationFee(),
            'slots' => $restaurant->getTotalSlots()
        ]);
    }

    public function delete(int $id): bool {
       $stmt = $this->connection->prepare("UPDATE Restaurants SET deleted_at = GETDATE() WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}