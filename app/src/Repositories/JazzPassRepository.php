<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\JazzPassModel;
use App\Models\Enums\EventTypeEnum;
use App\Repositories\Interfaces\IJazzPassRepository;
use PDO;
use Throwable;

class JazzPassRepository extends Repository implements IJazzPassRepository
{
    public function getAllActive(): array
    {
        $stmt = $this->connection->prepare("
            SELECT *
            FROM JazzPass
            WHERE Deleted_At IS NULL
              AND IsActive = 1
            ORDER BY JazzPassID ASC
        ");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_CLASS, JazzPassModel::class);
    }

    public function getAllAdmin(): array
    {
        $stmt = $this->connection->prepare("
            SELECT *
            FROM JazzPass
            WHERE Deleted_At IS NULL
            ORDER BY JazzPassID ASC
        ");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_CLASS, JazzPassModel::class);
    }

    public function getById(int $id): ?JazzPassModel
    {
        $stmt = $this->connection->prepare("
            SELECT *
            FROM JazzPass
            WHERE JazzPassID = :id
              AND Deleted_At IS NULL
        ");
        $stmt->execute(['id' => $id]);

        $pass = $stmt->fetchObject(JazzPassModel::class);
        return $pass ?: null;
    }

    public function create(JazzPassModel $pass): bool
    {
        $this->connection->beginTransaction();

        try {
            $stmt = $this->connection->prepare("
                INSERT INTO JazzPass (Title, Description, Price, ImageURL, IsActive, Capacity, TicketsLeft)
                VALUES (:title, :description, :price, :imageUrl, :isActive, :capacity, :ticketsLeft)
            ");

            $success = $stmt->execute([
                'title' => $pass->getTitle(),
                'description' => $pass->getDescription(),
                'price' => $pass->getPrice(),
                'imageUrl' => $pass->getImageUrl(),
                'isActive' => $pass->isActive() ? 1 : 0,
                'capacity' => $pass->getCapacity(),
                'ticketsLeft' => $pass->getTicketsLeft()
            ]);

            if (!$success) {
                $this->connection->rollBack();
                return false;
            }

            $jazzPassId = (int)$this->connection->lastInsertId();

            $wrapperSuccess = $this->createEventWrapper($jazzPassId);

            if (!$wrapperSuccess) {
                $this->connection->rollBack();
                return false;
            }

            $this->connection->commit();
            return true;
        } catch (Throwable $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }

    public function update(int $id, JazzPassModel $pass): bool
    {
        $stmt = $this->connection->prepare("
            UPDATE JazzPass
            SET Title = :title,
                Description = :description,
                Price = :price,
                ImageURL = :imageUrl,
                IsActive = :isActive,
                Capacity = :capacity,
                TicketsLeft = :ticketsLeft,
                Updated_At = GETDATE()
                WHERE JazzPassID = :id
              AND Deleted_At IS NULL
        ");

        return $stmt->execute([
            'id' => $id,
            'title' => $pass->getTitle(),
            'description' => $pass->getDescription(),
            'price' => $pass->getPrice(),
            'imageUrl' => $pass->getImageUrl(),
            'isActive' => $pass->isActive() ? 1 : 0,
            'capacity' => $pass->getCapacity(),
            'ticketsLeft' => $pass->getTicketsLeft()
        ]);
    }

    //decrease tickets left when a pass is purchased
    public function decreaseTicketsLeft(int $jazzPassId, int $quantity): bool
    {
        $stmt = $this->connection->prepare("
            UPDATE JazzPass
            SET TicketsLeft = TicketsLeft - :quantity
            WHERE JazzPassID = :id
            AND TicketsLeft >= :quantity
            AND Deleted_At IS NULL
        ");

        return $stmt->execute([
            'id' => $jazzPassId,
            'quantity' => $quantity
        ]);
    }

    public function delete(int $id): bool
    {
        $this->connection->beginTransaction();

        try {
            $stmt = $this->connection->prepare("
                UPDATE JazzPass
                SET Deleted_At = GETDATE()
                WHERE JazzPassID = :id
                  AND Deleted_At IS NULL
            ");

            $success = $stmt->execute(['id' => $id]);

            if (!$success) {
                $this->connection->rollBack();
                return false;
            }

            $wrapperSuccess = $this->deleteEventWrapper($id);

            if (!$wrapperSuccess) {
                $this->connection->rollBack();
                return false;
            }

            $this->connection->commit();
            return true;
        } catch (Throwable $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }

    private function createEventWrapper(int $subEventId): bool
    {
        $stmt = $this->connection->prepare("
            INSERT INTO Event (eventType, subEventId)
            VALUES (:eventType, :subEventId)
        ");

        return $stmt->execute([
            'eventType' => EventTypeEnum::JazzPass->value,
            'subEventId' => $subEventId
        ]);
    }

    private function deleteEventWrapper(int $subEventId): bool
    {
        $stmt = $this->connection->prepare("
            DELETE FROM Event
            WHERE eventType = :eventType
              AND subEventId = :subEventId
        ");

        return $stmt->execute([
            'eventType' => EventTypeEnum::JazzPass->value,
            'subEventId' => $subEventId
        ]);
    }
}