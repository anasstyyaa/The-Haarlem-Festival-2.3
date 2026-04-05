<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\KidsEventModel;
use App\Repositories\Interfaces\IKidsEventRepository;
use PDO;

class KidsEventRepository extends Repository implements IKidsEventRepository
{
    public function getAll(): array
    {
        $stmt = $this->connection->query("SELECT * FROM KidsEvent");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => $this->mapToModel($row), $results);
    }

    public function getById(int $id): ?KidsEventModel
    {
        $stmt = $this->connection->prepare("SELECT * FROM KidsEvent WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapToModel($row) : null;
    }

    public function getIdBySchedule(string $day, string $startTime, string $endTime): ?KidsEventModel
    {
        $sql = "SELECT * FROM KidsEvent WHERE day = :day AND startTime = :startTime AND endTime = :endTime";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'day'       => $day,
            'startTime' => $startTime,
            'endTime'   => $endTime
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapToModel($row) : null;
    }

    public function create(KidsEventModel $event): bool
    {
        $sql = "INSERT INTO KidsEvent (day, startTime, endTime, type, location, [limit], eventDate) 
                VALUES (:day, :startTime, :endTime, :type, :location, :limit, :eventDate)";

        $stmt = $this->connection->prepare($sql);
       return $stmt->execute([
    'day'       => $event->getDay(),
    'startTime' => $event->getStartTime(),
    'endTime'   => $event->getEndTime(),
    'type'      => $event->getType(),
    'location'  => $event->getLocation(),
    'limit'     => $event->getLimit(),
    'eventDate' => $event->getEventDate()
      ]);
    }

    public function update(KidsEventModel $event): bool
    {
      $sql = "UPDATE KidsEvent 
              SET day = :day, startTime = :startTime, endTime = :endTime, 
              type = :type, location = :location, [limit] = :limit,
              eventDate = :eventDate
              WHERE id = :id";

        $stmt = $this->connection->prepare($sql);
        return $stmt->execute([
            'day'       => $event->getDay(),
            'startTime' => $event->getStartTime(),
            'endTime'   => $event->getEndTime(),
            'type'      => $event->getType(),
            'location'  => $event->getLocation(),
            'id'        => $event->getId(),
            'limit' => $event->getLimit(), 
             'eventDate' => $event->getEventDate()
        ]);
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM KidsEvent WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    private function mapToModel(array $row): KidsEventModel
    {
    return new KidsEventModel(
    (int)$row['id'],
    $row['day'],
    $row['startTime'],
    $row['endTime'],
    $row['type'],
    $row['location'],
    (int)$row['limit'],
    $row['eventDate'] 
);
    }
}