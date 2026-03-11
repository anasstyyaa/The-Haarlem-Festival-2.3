<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\EventModel;
use App\Models\Enums\EventTypeEnum;
use PDO;

class EventRepository extends Repository
{
    public function getAll(): array
    {
        $stmt = $this->connection->query("SELECT * FROM Event");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => $this->mapToModel($row), $results);
    }

    public function getById(int $id): ?EventModel
    {
        $stmt = $this->connection->prepare("SELECT * FROM Event WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapToModel($row) : null;
    }

    public function create(EventModel $event): bool
    {
        $sql = "INSERT INTO Event (eventType, subEventId) 
                VALUES (:eventType, :subEventId)";

        $stmt = $this->connection->prepare($sql);

        return $stmt->execute([
            'eventType' => $event->getEventType()->value,
            'subEventId' => $event->getSubEventId()
        ]);
    }

    public function update(EventModel $event): bool
    {
        $sql = "UPDATE Event 
                SET eventType = :eventType, 
                    subEventId = :subEventId
                WHERE id = :id";

        $stmt = $this->connection->prepare($sql);

        return $stmt->execute([
            'eventType' => $event->getEventType()->value,
            'subEventId' => $event->getSubEventId(),
            'id' => $event->getId()
        ]);
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM Event WHERE id = :id";
        $stmt = $this->connection->prepare($sql);

        return $stmt->execute(['id' => $id]);
    }

    private function mapToModel(array $row): EventModel
    {
        return new EventModel(
            (int)($row['id'] ?? 0),
            EventTypeEnum::from($row['eventType']),
            (int)($row['subEventId'] ?? 0)
        );
    }

    public function checkEventType(int $subEventId, string $eventType):int{
    $sql = "SELECT id FROM Event WHERE subEventId = :subEventId AND eventType = :eventType ";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute([
        'subEventId' => $subEventId,
        'eventType'  => $eventType
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        return (int)$row['id'];
    }

    $tempEvent = new EventModel(0, EventTypeEnum::from($eventType), $subEventId); // id = 0 because the create method will assign the new ID after insertion

    $this->create($tempEvent);

    return (int)$this->connection->lastInsertId();
    }
}
