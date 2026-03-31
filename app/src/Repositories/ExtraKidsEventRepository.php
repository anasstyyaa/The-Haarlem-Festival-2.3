<?php
namespace App\Repositories;

use App\Framework\Repository;
use App\Models\ExtraKidsEventModel;
use App\Repositories\Interfaces\IExtraKidsEventRepository;
use PDO;

class ExtraKidsEventRepository extends Repository implements IExtraKidsEventRepository
{
    public function getAll(): array
    {
        $stmt = $this->connection->prepare("
            SELECT * FROM ExtraKidsEvent
            ORDER BY id ASC
        ");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_CLASS, ExtraKidsEventModel::class);
    }

    public function getById(int $id): ?ExtraKidsEventModel
    {
        $stmt = $this->connection->prepare("
            SELECT * FROM ExtraKidsEvent
            WHERE id = :id
        ");
        $stmt->execute(['id' => $id]);

        $event = $stmt->fetchObject(ExtraKidsEventModel::class);
        return $event ?: null;
    }

    public function create(ExtraKidsEventModel $event): bool
    {
        $stmt = $this->connection->prepare("
            INSERT INTO ExtraKidsEvent (name, description, imageURL)
            VALUES (:name, :description, :imageURL)
        ");

        return $stmt->execute([
            'name' => $event->getName(),
            'description' => $event->getDescription(),
            'imageURL' => $event->getImageUrl()
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->connection->prepare("
            DELETE FROM ExtraKidsEvent
            WHERE id = :id
        ");

        return $stmt->execute(['id' => $id]);
    }
}