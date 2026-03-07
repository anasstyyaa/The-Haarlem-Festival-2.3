<?php

namespace App\Repositories; 
use App\Framework\Repository;
use App\Models\JazzEventModel;
use App\Repositories\Interfaces\IJazzEventRepository;
use PDO;
use Throwable;

class JazzEventRepository extends Repository implements IJazzEventRepository
{
    public function getAllActive(): array
    {
        $stmt = $this->connection->prepare("
            SELECT * 
            FROM JazzEvent 
            WHERE deleted_at IS NULL
            ORDER BY StartDateTime ASC
        ");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_CLASS, JazzEventModel::class);
    }

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

    public function create(JazzEventModel $event): bool
    {
        $this->connection->beginTransaction();

        try {
            $stmt = $this->connection->prepare("
                INSERT INTO JazzEvent (ArtistID, JazzVenueID, StartDateTime, EndDateTime, Price)
                VALUES (:ArtistID, :JazzVenueID, :StartDateTime, :EndDateTime, :Price)
            ");

            $success = $stmt->execute([
                'ArtistID' => $event->getArtistId(),
                'JazzVenueID' => $event->getJazzVenueId(),
                'StartDateTime' => $event->getStartDateTime(),
                'EndDateTime' => $event->getEndDateTime(),
                'Price' => $event->getPrice()
            ]);

            if (!$success) {
                $this->connection->rollBack();
                return false;
            }
            
            //When inserting a new row into a table with an auto-increment / IDENTITY primary key, the database automatically generates the ID.
            $jazzEventId = (int)$this->connection->lastInsertId();

            $wrapperStmt = $this->connection->prepare("
                INSERT INTO Event (eventType, subEventId)
                VALUES (:eventType, :subEventId)
            ");

            $wrapperSuccess = $wrapperStmt->execute([
                'eventType' => 'jazz',
                'subEventId' => $jazzEventId
            ]);

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

    public function update(int $id, JazzEventModel $event): bool
    {
        $stmt = $this->connection->prepare("
            UPDATE JazzEvent
            SET ArtistID = :ArtistID,
                JazzVenueID = :JazzVenueID,
                StartDateTime = :StartDateTime,
                EndDateTime = :EndDateTime,
                Price = :Price,
                updated_at = GETDATE()
            WHERE JazzEventID = :JazzEventID AND deleted_at IS NULL
        ");

        return $stmt->execute([
            'JazzEventID' => $id,
            'ArtistID' => $event->getArtistId(),
            'JazzVenueID' => $event->getJazzVenueId(),
            'StartDateTime' => $event->getStartDateTime(),
            'EndDateTime' => $event->getEndDateTime(),
            'Price' => $event->getPrice()
        ]);
    }

    public function delete(int $id): bool
    {
        $this->connection->beginTransaction();

        try {
            $stmt = $this->connection->prepare("
                UPDATE JazzEvent
                SET deleted_at = GETDATE()
                WHERE JazzEventID = :JazzEventID
            ");

            $success = $stmt->execute([
                'JazzEventID' => $id
            ]);

            if (!$success) {
                $this->connection->rollBack();
                return false;
            }

            $wrapperStmt = $this->connection->prepare("
                DELETE FROM Event
                WHERE eventType = :eventType AND subEventId = :subEventId
            ");

            $wrapperSuccess = $wrapperStmt->execute([
                'eventType' => 'jazz',
                'subEventId' => $id
            ]);

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
}