<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\HistoryVenueModel;
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
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => $this->mapToModel($row), $results);
    }

    public function getById(int $venueId): ?HistoryVenueModel
    {
        $sql = "
        SELECT
            id AS venueId,
            venueName,
            details,
            location,
            imageId
        FROM dbo.HistoryVenue
        WHERE id = :venueId
    ";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['venueId' => $venueId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapToModel($row) : null;
    }
    public function create(HistoryVenueModel $venue): bool
    {
        $sql = "
        INSERT INTO dbo.HistoryVenue (venueName, details, location, imageId)
        VALUES (:venueName, :details, :location, :imageId)
    ";

        $stmt = $this->connection->prepare($sql);

        return $stmt->execute([
            'venueName' => $venue->getVenueName(),
            'details' => $venue->getDetails(),
            'location' => $venue->getLocation(),
            'imageId' => $venue->getImageId()
        ]);
    }
    public function update(HistoryVenueModel $venue): bool
    {
        $sql = "
        UPDATE dbo.HistoryVenue
        SET venueName = :venueName,
            details = :details,
            location = :location,
            imageId = :imageId
        WHERE id = :venueId
    ";

        $stmt = $this->connection->prepare($sql);

        return $stmt->execute([
            'venueId' => $venue->getVenueId(),
            'venueName' => $venue->getVenueName(),
            'details' => $venue->getDetails(),
            'location' => $venue->getLocation(),
            'imageId' => $venue->getImageId()
        ]);
        
    }
    public function delete(int $venueId): bool
    {
        $sql = "DELETE FROM dbo.HistoryVenue WHERE id = :venueId";

        $stmt = $this->connection->prepare($sql);

        return $stmt->execute(['venueId' => $venueId]);
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
    private function mapToModel(array $row): HistoryVenueModel
    {
        return new HistoryVenueModel(
            (int)($row['venueId'] ?? 0),
            $row['venueName'] ?? '',
            $row['details'] ?? null,
            $row['location'] ?? null,
            isset($row['imageId']) ? (int)$row['imageId'] : null
        );
    }
}
