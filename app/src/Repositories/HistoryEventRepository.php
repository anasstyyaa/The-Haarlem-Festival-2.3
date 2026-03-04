<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Repositories\Interfaces\IHistoryEventRepository;
use PDO;

class HistoryEventRepository extends Repository implements IHistoryEventRepository
{
    public function getAll(): array
    {
        $sql = "
            SELECT 
                e.id AS eventId,
                h.slotDate,
                h.startTime,
                h.language,
                h.duration,
                h.minAge,
                h.capacity,
                h.priceIndividual,
                h.priceFamily
            FROM dbo.Event e
            INNER JOIN dbo.HistoryEvent h ON h.id = e.subEventId
            WHERE e.eventType = 'tour'
            ORDER BY h.slotDate, h.startTime, h.language
        ";

        $stmt = $this->connection->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByEventId(int $eventId): ?array
    {
        $sql = "
            SELECT 
                e.id AS eventId,
                h.id AS historyEventId,
                h.slotDate,
                h.startTime,
                h.language,
                h.duration,
                h.minAge,
                h.capacity,
                h.priceIndividual,
                h.priceFamily
            FROM dbo.Event e
            INNER JOIN dbo.HistoryEvent h ON h.id = e.subEventId
            WHERE e.eventType = 'tour'
              AND e.id = :eventId
        ";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['eventId' => $eventId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
    public function getEventIdBySlot(string $slotDate, string $startTime, string $language): ?int
    {
        $sql = "
        SELECT e.id AS eventId
        FROM dbo.Event e
        INNER JOIN dbo.HistoryEvent h ON h.id = e.subEventId
        WHERE e.eventType = 'tour'
          AND h.slotDate = :slotDate
          AND h.startTime = :startTime
          AND h.language = :language
    ";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'slotDate' => $slotDate,
            'startTime' => $startTime,
            'language' => $language
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['eventId'] : null;
    }
}
