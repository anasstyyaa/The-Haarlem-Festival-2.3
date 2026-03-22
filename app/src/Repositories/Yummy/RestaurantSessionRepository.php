<?php

namespace App\Repositories\Yummy;

use App\Framework\Repository;
use App\Models\Yummy\RestaurantSessionModel;
use App\Repositories\Interfaces\Yummy\IRestaurantSessionRepository;
use PDO;

class RestaurantSessionRepository extends Repository implements IRestaurantSessionRepository {

    public function getSessionById(int $id): ?RestaurantSessionModel {
        $sql = "SELECT id, restaurant_id, [date], startTime, available_slots 
                FROM RestaurantSessions 
                WHERE id = :id";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        $stmt->setFetchMode(\PDO::FETCH_CLASS, RestaurantSessionModel::class);
        $session = $stmt->fetch();

        return $session ?: null;
    }

    public function getSessionsByRestaurantId(int $restaurantId): array {
        $sql = "SELECT id, restaurant_id, [date], startTime, available_slots 
                FROM RestaurantSessions 
                WHERE restaurant_id = :rid 
                AND available_slots > 0 
                ORDER BY [date] ASC, startTime ASC";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['rid' => $restaurantId]);
        
        return $stmt->fetchAll(PDO::FETCH_CLASS, RestaurantSessionModel::class);
    }

    public function updateCapacity(int $sessionId, int $count): bool {
        $sql = "UPDATE RestaurantSessions 
                SET available_slots = available_slots - :count 
                WHERE id = :id AND available_slots >= :count";
                
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute([
            'id' => $sessionId,
            'count' => $count
        ]);
    }

    public function addSessions(RestaurantSessionModel $session): bool {
        $sql = "INSERT INTO RestaurantSessions (restaurant_id, [date], startTime, available_slots) 
                VALUES (:rid, :date, :time, :slots)";
                
        $stmt = $this->connection->prepare($sql);

        foreach ($session->getSelectedTimes() as $time) {
            $stmt->execute([
                'rid'   => $session->getRestaurantId(),
                'date'  => $session->getDate(),
                'time'  => $time,
                'slots' => $session->getAvailableSlots()
            ]);
        }
        
        return true; 
    }

    public function deleteSession(int $id): bool {
        $sql = "DELETE FROM RestaurantSessions WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function updateSession(RestaurantSessionModel $session): bool {

        $stmt = $this->connection->prepare("
            UPDATE RestaurantSessions 
            SET 
                [date] = :date, 
                startTime = :start_time, 
                available_slots = :slots
            WHERE id = :id
        ");

        return $stmt->execute([
            'date'       => $session->getDate(),
            'start_time' => $session->getStartTime(),
            'slots'      => $session->getAvailableSlots(),
            'id'         => $session->getId()
        ]);
    }

    public function existsAtTime(RestaurantSessionModel $session, int $duration, int $excludeId = 0): bool {
        $query = "SELECT COUNT(*) FROM RestaurantSessions
                WHERE restaurant_id = :rid 
                AND [date]= :sdate 
                AND id != :excludeId 
                AND (
                    CAST(:newStart1 AS TIME) < CAST(DATEADD(minute, CAST(:dur1 AS INT), startTime) AS TIME)
                    AND 
                    CAST(DATEADD(minute, CAST(:dur2 AS INT), CAST(:newStart2 AS TIME)) AS TIME) > startTime
                )";
                
        $stmt = $this->connection->prepare($query);

        $stmt->execute([
            'rid'       => $session->getRestaurantId(),
            'sdate'     => $session->getDate(),
            'excludeId' => $excludeId,
            'newStart1' => $session->getStartTime(),
            'dur1'      => $duration,
            'dur2'      => $duration,
            'newStart2' => $session->getStartTime()
        ]);
        
        return (int)$stmt->fetchColumn() > 0;
    }

    // helpers for transaction management in the service layer

    public function beginTransaction() { $this->connection->beginTransaction(); }
    public function commit() { $this->connection->commit(); }
    public function rollBack() { $this->connection->rollBack(); }
    
    public function saveSingleSession(RestaurantSessionModel $session) {
        $query = "INSERT INTO RestaurantSessions (restaurant_id, [date], startTime, available_slots) 
                VALUES (?, ?, ?, ?)";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            $session->getRestaurantId(),
            $session->getDate(),
            $session->getStartTime(),
            $session->getAvailableSlots()
        ]);
    }
} 