<?php

namespace App\Repositories;

use App\Models\HistoryEventModel;
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

    /*
    // Old booking methods.
    // Not needed right now because the current History feature uses
    // PersonalProgramService instead of writing to HistoryBooking tables.

    public function createBooking(int $eventId, ?int $userId): int
    {
        $sql = "
            INSERT INTO dbo.HistoryBooking (eventId, userId, createdAt, status, totalAmount)
            VALUES (:eventId, :userId, SYSDATETIME(), 'Pending', 0)
        ";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'eventId' => $eventId,
            'userId' => $userId
        ]);

        return (int)$this->connection->lastInsertId();
    }

    public function addBookingItem(int $bookingId, string $ticketType, int $quantity): bool
    {
        $unitPrice = 0;

        if ($ticketType === 'individual') {
            $unitPrice = 17.50;
        } elseif ($ticketType === 'family') {
            $unitPrice = 60.00;
        }

        $sql = "
            INSERT INTO dbo.HistoryBookingItem (bookingId, ticketType, quantity, unitPrice)
            VALUES (:bookingId, :ticketType, :quantity, :unitPrice)
        ";

        $stmt = $this->connection->prepare($sql);
        return $stmt->execute([
            'bookingId' => $bookingId,
            'ticketType' => ucfirst($ticketType),
            'quantity' => $quantity,
            'unitPrice' => $unitPrice
        ]);
    }
    */

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