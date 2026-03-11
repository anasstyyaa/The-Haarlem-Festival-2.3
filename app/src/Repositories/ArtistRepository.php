<?php

namespace App\Repositories; 

use App\Framework\Repository;
use App\Models\ArtistModel;
use App\Repositories\Interfaces\IArtistRepository;
use PDO;

class ArtistRepository extends Repository implements IArtistRepository{

    //This method returns artists 
    public function getAllActive(): array
    {
        $stmt = $this->connection->prepare("SELECT * FROM Artist WHERE deleted_at IS NULL ORDER BY ArtistID ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, ArtistModel::class);
    }

    public function getById(int $id): ?ArtistModel
    {
        $stmt = $this->connection->prepare("SELECT * FROM Artist WHERE ArtistID = :id AND deleted_at IS NULL");
        $stmt->execute(['id' => $id]);
        $artist = $stmt->fetchObject(ArtistModel::class);
        return $artist ?: null;
    }

    //This method returns events (tickets) for a specific artist
    public function getJazzEventsForArtist(int $artistId): array
    {
    $sql = "
        SELECT
        e.id AS EventID,
        je.StartDateTime,
        je.EndDateTime,
        je.Price,
        v.VenueName,
        v.HallName
        FROM Event e
        JOIN JazzEvent je ON je.JazzEventID = e.subEventId
        JOIN JazzVenue v ON v.JazzVenueID = je.JazzVenueID
        WHERE e.eventType = 'jazz'
        AND je.ArtistID = :artistId
        AND je.deleted_at IS NULL
        ORDER BY je.StartDateTime
    ";

    $stmt = $this->connection->prepare($sql);
    $stmt->execute(['artistId' => $artistId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(ArtistModel $artist): bool
    {
        $sql = "INSERT INTO Artist (ArtistName, ShortDescription, Description, ImageURL)
                VALUES (:ArtistName, :ShortDescription, :Description, :ImageURL)";
        $stmt = $this->connection->prepare($sql);

        return $stmt->execute([
            'ArtistName'  => $artist->getName(),
            'ShortDescription' => $artist->getShortDescription(),
            'Description'  => $artist->getDescription(),
            'ImageURL'   => $artist->getImageUrl()
        ]);
    }

    public function update(int $id, ArtistModel $artist): bool
    {
        $sql = "UPDATE Artist
                SET ArtistName = :ArtistName,
                    ShortDescription = :ShortDescription,
                    Description = :Description,
                    ImageURL = :ImageURL,
                    updated_at = GETDATE()
                WHERE ArtistID = :ArtistID AND deleted_at IS NULL";

        $stmt = $this->connection->prepare($sql);

        return $stmt->execute([
            'ArtistID'    => $id,
            'ArtistName'  => $artist->getName(),
            'ShortDescription' => $artist->getShortDescription(),
            'Description'  => $artist->getDescription(),
            'ImageURL'   => $artist->getImageUrl(),
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->connection->prepare("UPDATE Artist SET deleted_at = GETDATE() WHERE ArtistID = :id AND deleted_at IS NULL");  //Ensuring it doesn't try to delete an already deleted row
        return $stmt->execute(['id' => $id]);
    }

    public function getJazzLineup(): array
    {
        $sql = "
            SELECT
                a.ArtistID,
                a.ArtistName,
                a.ShortDescription,
                a.ImageURL,
                je.StartDateTime,
                je.EndDateTime,
                je.Price,
                v.VenueName,
                v.HallName
            FROM Artist a
            JOIN JazzEvent je ON je.ArtistID = a.ArtistID
            JOIN JazzVenue v ON v.JazzVenueID = je.JazzVenueID
            WHERE a.deleted_at IS NULL
            AND je.deleted_at IS NULL
            ORDER BY je.StartDateTime ASC, a.ArtistName ASC
        ";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


}