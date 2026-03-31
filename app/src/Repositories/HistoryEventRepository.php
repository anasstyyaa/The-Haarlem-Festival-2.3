<?php

namespace App\Repositories;

use App\Models\HistoryEventModel;
use App\Framework\Repository;
use App\Repositories\Interfaces\IHistoryEventRepository;
use PDO;
use Throwable;

class HistoryEventRepository extends Repository implements IHistoryEventRepository
{
    public function getAll(): array
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
            ORDER BY h.slotDate, h.startTime, h.language
        ";

        $stmt = $this->connection->query($sql);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => $this->mapToModel($row), $results);
    }

    public function getByEventId(int $eventId): ?HistoryEventModel
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
        return $row ? $this->mapToModel($row) : null;
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

    public function create(HistoryEventModel $event): bool
    {
        try {
            $this->connection->beginTransaction();

            $sqlHistory = "
                INSERT INTO dbo.HistoryEvent
                    (slotDate, startTime, language, duration, minAge, capacity, priceIndividual, priceFamily)
                VALUES
                    (:slotDate, :startTime, :language, :duration, :minAge, :capacity, :priceIndividual, :priceFamily)
            ";

            $stmtHistory = $this->connection->prepare($sqlHistory);
            $stmtHistory->execute([
                'slotDate' => $event->getSlotDate(),
                'startTime' => $event->getStartTime(),
                'language' => $event->getLanguage(),
                'duration' => $event->getDuration(),
                'minAge' => $event->getMinAge(),
                'capacity' => $event->getCapacity(),
                'priceIndividual' => $event->getPriceIndividual(),
                'priceFamily' => $event->getPriceFamily()
            ]);

            $historyEventId = (int)$this->connection->lastInsertId();

            $sqlEvent = "
                INSERT INTO dbo.Event (eventType, subEventId)
                VALUES ('tour', :subEventId)
            ";

            $stmtEvent = $this->connection->prepare($sqlEvent);
            $stmtEvent->execute([
                'subEventId' => $historyEventId
            ]);

            $this->connection->commit();
            return true;
        } catch (Throwable $e) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }
            return false;
        }
    }

    public function update(HistoryEventModel $event): bool
    {
        $sql = "
            UPDATE dbo.HistoryEvent
            SET
                slotDate = :slotDate,
                startTime = :startTime,
                language = :language,
                duration = :duration,
                minAge = :minAge,
                capacity = :capacity,
                priceIndividual = :priceIndividual,
                priceFamily = :priceFamily
            WHERE id = :historyEventId
        ";

        $stmt = $this->connection->prepare($sql);

        return $stmt->execute([
            'historyEventId' => $event->getHistoryEventId(),
            'slotDate' => $event->getSlotDate(),
            'startTime' => $event->getStartTime(),
            'language' => $event->getLanguage(),
            'duration' => $event->getDuration(),
            'minAge' => $event->getMinAge(),
            'capacity' => $event->getCapacity(),
            'priceIndividual' => $event->getPriceIndividual(),
            'priceFamily' => $event->getPriceFamily()
        ]);
    }

    public function delete(int $eventId): bool
    {
        try {
            $this->connection->beginTransaction();

            $sqlGetHistoryId = "
                SELECT subEventId
                FROM dbo.Event
                WHERE id = :eventId
                  AND eventType = 'tour'
            ";

            $stmtGet = $this->connection->prepare($sqlGetHistoryId);
            $stmtGet->execute(['eventId' => $eventId]);
            $historyEventId = $stmtGet->fetchColumn();

            if (!$historyEventId) {
                $this->connection->rollBack();
                return false;
            }

            // Optional cleanup if stops exist for this event
            $sqlStops = "DELETE FROM dbo.HistoryEventStop WHERE eventId = :eventId";
            $stmtStops = $this->connection->prepare($sqlStops);
            $stmtStops->execute(['eventId' => $eventId]);

            $sqlEvent = "DELETE FROM dbo.Event WHERE id = :eventId";
            $stmtEvent = $this->connection->prepare($sqlEvent);
            $stmtEvent->execute(['eventId' => $eventId]);

            $sqlHistory = "DELETE FROM dbo.HistoryEvent WHERE id = :historyEventId";
            $stmtHistory = $this->connection->prepare($sqlHistory);
            $stmtHistory->execute(['historyEventId' => $historyEventId]);

            $this->connection->commit();
            return true;
        } catch (Throwable $e) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }
            return false;
        }
    }

    private function mapToModel(array $row): HistoryEventModel
    {
        return new HistoryEventModel(
            (int)($row['eventId'] ?? 0),
            (int)($row['historyEventId'] ?? 0),
            $row['slotDate'] ?? '',
            $row['startTime'] ?? '',
            $row['language'] ?? '',
            (int)($row['duration'] ?? 0),
            (int)($row['minAge'] ?? 0),
            (int)($row['capacity'] ?? 0),
            (float)($row['priceIndividual'] ?? 0),
            (float)($row['priceFamily'] ?? 0)
        );
    }
}