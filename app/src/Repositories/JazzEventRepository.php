<?php

namespace App\Repositories; 
use App\Framework\Repository;
use App\Models\JazzEventModel;
use App\ViewModels\JazzEventViewModel;
use App\Models\Enums\EventTypeEnum; 
use App\Repositories\Interfaces\IJazzEventRepository;
use PDO;
use Throwable;

class JazzEventRepository extends Repository implements IJazzEventRepository
{
    //Getting all active events (not deleted) ordered by start datetime
    public function getAllActive(): array
    {
        $stmt = $this->connection->prepare("
            SELECT * 
            FROM JazzEvent 
            WHERE deleted_at IS NULL    
            ORDER BY StartDateTime ASC
        ");
        $stmt->execute();
        //fetchAll retrieves all rows from the query.
        //PDO::FETCH_CLASS -> This tells PDO to convert each row into an object of this class.
        //JazzEventModel::class ->Create objects using the JazzEventModel class. 
        return $stmt->fetchAll(PDO::FETCH_CLASS, JazzEventModel::class);
    }

    //It gets one specific Jazz event by its id.
    public function getById(int $id): ?JazzEventModel
    {
        $stmt = $this->connection->prepare("
            SELECT * 
            FROM JazzEvent 
            WHERE JazzEventID = :JazzEventID AND deleted_at IS NULL
        ");
        $stmt->execute([
            'JazzEventID' => $id
        ]);

        $event = $stmt->fetchObject(JazzEventModel::class);
        return $event ?: null;
    }

    //combining the data from Event, JazzEvent and JazzVenue tables to get all the necessary info for the personal program page. (view model is used here to hold the combined data from multiple tables)
    public function getEventsForArtist(int $artistId, EventTypeEnum $eventType): array
    {
            $sql = "
            SELECT
            e.id AS EventID,
            je.JazzEventID,
            je.StartDateTime,
            je.EndDateTime,
            je.Price,
            v.VenueName,
            v.HallName
            FROM Event e
            JOIN JazzEvent je ON je.JazzEventID = e.subEventId
            JOIN JazzVenue v ON v.JazzVenueID = je.JazzVenueID
            WHERE e.eventType = :eventType
            AND je.ArtistID = :artistId
            AND je.deleted_at IS NULL
            ORDER BY je.StartDateTime ASC
        ";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'eventType' => $eventType->value,
            'artistId' => $artistId
        ]);

        return $stmt->fetchAll(PDO::FETCH_CLASS, JazzEventViewModel::class);

    }

    //this method is for showing the correct location info in the personal program when the event is a jazz event. 
    public function getVenueInfoByJazzEventId(int $jazzEventId): ?array
    {
        $sql = "
            SELECT
                v.VenueName,
                v.HallName
            FROM JazzEvent je
            JOIN JazzVenue v ON v.JazzVenueID = je.JazzVenueID
            WHERE je.JazzEventID = :jazzEventId
            AND je.deleted_at IS NULL
        ";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'jazzEventId' => $jazzEventId
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    //creating a new jazz event and linking it to the general Event table using a wrapper method.
    public function create(JazzEventModel $event): bool
    {
        // Start a transaction to ensure both inserts succeed or fail together
        $this->connection->beginTransaction();

        try {
            $stmt = $this->connection->prepare("
                INSERT INTO JazzEvent (ArtistID, JazzVenueID, StartDateTime, EndDateTime, Price, Capacity, TicketsLeft)
                VALUES (:ArtistID, :JazzVenueID, :StartDateTime, :EndDateTime, :Price, :Capacity, :TicketsLeft)
            ");

            $success = $stmt->execute([
                'ArtistID' => $event->getArtistId(),
                'JazzVenueID' => $event->getJazzVenueId(),
                'StartDateTime' => $event->getStartDateTime(),
                'EndDateTime' => $event->getEndDateTime(),
                'Price' => $event->getPrice(),
                'Capacity' => $event->getCapacity(),
                'TicketsLeft' => $event->getTicketsLeft()
            ]);

            if (!$success) {
                $this->connection->rollBack();
                return false;
            }
            //get the new JazzEvent id
            $jazzEventId = (int)$this->connection->lastInsertId();
            //create the wrapper entry in the Event table (eventType = jazz, subEventId = 12)
            $wrapperSuccess = $this->createEventWrapper($jazzEventId);

            if (!$wrapperSuccess) {
                $this->connection->rollBack();
                return false;
            }
            //if both inserts succeeded, commit the transaction
            $this->connection->commit();
            return true;

        } catch (Throwable $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }

    public function update(int $id, JazzEventModel $event): bool
    {
        $stmt = $this->connection->prepare("
            UPDATE JazzEvent
            SET ArtistID = :ArtistID,
                JazzVenueID = :JazzVenueID,
                StartDateTime = :StartDateTime,
                EndDateTime = :EndDateTime,
                Price = :Price,
                Capacity = :Capacity,
                TicketsLeft = :TicketsLeft,
                updated_at = GETDATE()
            WHERE JazzEventID = :JazzEventID AND deleted_at IS NULL
        ");

        return $stmt->execute([
            'JazzEventID' => $id,
            'ArtistID' => $event->getArtistId(),
            'JazzVenueID' => $event->getJazzVenueId(),
            'StartDateTime' => $event->getStartDateTime(),
            'EndDateTime' => $event->getEndDateTime(),
            'Price' => $event->getPrice(), 
            'Capacity' => $event->getCapacity(),
            'TicketsLeft' => $event->getTicketsLeft()
        ]);
    }

    public function delete(int $id): bool
    {
        $this->connection->beginTransaction();

        try {
            $stmt = $this->connection->prepare("
                UPDATE JazzEvent
                SET deleted_at = GETDATE()
                WHERE JazzEventID = :JazzEventID AND deleted_at IS NULL
            ");

            $success = $stmt->execute([
                'JazzEventID' => $id
            ]);

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

    //for decreasing the ticket amounts 
    public function decreaseTicketsLeft(int $jazzEventId, int $quantity): bool
    {
        $stmt = $this->connection->prepare("
            UPDATE JazzEvent
            SET TicketsLeft = TicketsLeft - :quantityToSubtract
            WHERE JazzEventID = :id
            AND TicketsLeft >= :minimumRequired
            AND deleted_at IS NULL
        ");

        $stmt->execute([
            'id' => $jazzEventId,
            'quantityToSubtract' => $quantity,
            'minimumRequired' => $quantity
        ]);
        
        return $stmt->rowCount() > 0;
    }

    //Helper method (It inserts the generic row into Event table)
    private function createEventWrapper(int $subEventId): bool
    {
        $stmt = $this->connection->prepare("
            INSERT INTO Event (eventType, subEventId)
            VALUES (:eventType, :subEventId)
        ");

        return $stmt->execute([
            'eventType' => EventTypeEnum::JazzEvent->value,
            'subEventId' => $subEventId
        ]);
    }

    //It deletes the generic row from Event table
    private function deleteEventWrapper(int $subEventId): bool
    {
        $stmt = $this->connection->prepare("
            DELETE FROM Event
            WHERE eventType = :eventType AND subEventId = :subEventId
        ");

        return $stmt->execute([
            'eventType' => EventTypeEnum::JazzEvent->value,
            'subEventId' => $subEventId
        ]);
    }
}