<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Repositories\Interfaces\IHistoryVenueRepository;
use PDO;

class HistoryVenueRepository extends Repository implements IHistoryVenueRepository
{
    public function getAll(): array
    {
        $sql = "
            SELECT
                id AS venueId,
                venueName,
                details,
                location,
                imageId
            FROM dbo.HistoryVenue
            ORDER BY id
        ";

        $stmt = $this->connection->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStopsByEventId(int $eventId): array
    {
        $sql = "
            SELECT
                s.stopOrder,
                v.id AS venueId,
                v.venueName,
                v.details,
                v.location,
                v.imageId
            FROM dbo.HistoryEventStop s
            INNER JOIN dbo.HistoryVenue v ON v.id = s.venueId
            WHERE s.eventId = :eventId
            ORDER BY s.stopOrder
        ";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['eventId' => $eventId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}