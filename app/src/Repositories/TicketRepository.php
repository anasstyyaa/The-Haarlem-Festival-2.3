<?php

namespace App\Repositories;

use App\Repositories\Interfaces\ITicketRepository;
use App\Framework\Repository;
use App\Models\TicketModel;
use App\Models\EventModel;
use PDO;

class TicketRepository extends Repository implements ITicketRepository
{
    public function savePaidTicket(TicketModel $ticket, string $stripeId): bool
    {
        $sql = "INSERT INTO Tickets (
                    user_id, 
                    event_id, 
                    sub_event_id, 
                    number_of_people, 
                    unit_price, 
                    total_price, 
                    [status], 
                    stripe_session_id,
                    unique_ticket_token, 
                    is_scanned, 
                    program_item_id
                ) 
                VALUES (:uid, :eid, :subid, :count, :uprice, :tprice, 'paid', :stripe, :token, 0, :program_id)";

        $stmt = $this->connection->prepare($sql);

        $token = bin2hex(random_bytes(16));

        return $stmt->execute([
            'uid'    => $ticket->getUser() ? $ticket->getUser()->getId() : null,
            'eid'    => $ticket->getEvent()->getId(),
            'subid'  => $ticket->getEvent()->getSubEventId(),
            'count'  => $ticket->getNumberOfPeople(),
            'uprice' => $ticket->getUnitPrice(),
            'tprice' => $ticket->getTotalPrice(),
            'stripe' => $stripeId,
            'token'  => $token,
            'program_id' => $ticket->getProgramItemId()
        ]);
    }

    public function getByToken(string $token): ?array
    {
        if ($token === '') {
            return null;
        }

        try {
            error_log('REPO getByToken TOKEN: [' . $token . ']');

            $sql = "SELECT TOP 1 * 
                FROM Tickets 
                WHERE unique_ticket_token = :token";

            $stmt = $this->connection->prepare($sql);
            $stmt->execute(['token' => $token]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);


            return $row ?: null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function markAsScanned(string $token): bool  //marks tickets as scanned preventing it from being used again
    {
        try {
            $sql = "UPDATE Tickets
                SET is_scanned = 1
                WHERE unique_ticket_token = :token";

            $stmt = $this->connection->prepare($sql);

            return $stmt->execute(['token' => $token]);
        } catch (\Exception $e) {
            error_log("Error marking ticket as scanned: " . $e->getMessage());
            return false;
        }
    }

    public function savePendingTicket(TicketModel $ticket, string $tempOrderId): bool
    {
        $sql = "INSERT INTO Tickets (
                    user_id, event_id, sub_event_id, number_of_people, 
                    unit_price, total_price, [status], stripe_session_id,
                    unique_ticket_token, is_scanned, created_at,
                    program_item_id
                ) 
                VALUES (:uid, :eid, :subid, :count, :uprice, :tprice, 'pending', :orderId, :token, 0, GETDATE(), :program_id)";

        $stmt = $this->connection->prepare($sql);
        $token = bin2hex(random_bytes(16));

        return $stmt->execute([
            'uid'        => $ticket->getUser() ? $ticket->getUser()->getId() : null,
            'eid'        => $ticket->getEvent()->getId(),
            'subid'      => $ticket->getEvent()->getSubEventId(),
            'count'      => $ticket->getNumberOfPeople(),
            'uprice'     => $ticket->getUnitPrice(),
            'tprice'     => $ticket->getTotalPrice(),
            'orderId'    => $tempOrderId,
            'token'      => $token,
            'program_id' => $ticket->getProgramItemId()
        ]);
    }

    public function updateTicketsToPaid(string $orderId, string $actualStripeId): bool
    {
        $sql = "UPDATE Tickets SET [status] = 'paid'
                WHERE stripe_session_id = :orderId AND [status] = 'pending'";

        $stmt = $this->connection->prepare($sql);
        return $stmt->execute([
            'orderId' => $orderId
        ]);
    }

    public function markAsExpired(string $orderId): bool
    {
        $sql = "UPDATE Tickets 
                SET [status] = 'expired' 
                WHERE stripe_session_id = :orderId 
                AND [status] = 'pending'";

        $stmt = $this->connection->prepare($sql);
        return $stmt->execute(['orderId' => $orderId]);
    }

 public function getAllWithDetailsPaginated(int $page, int $limit): array
{
    $offset = ($page - 1) * $limit;

    $sql = "
        SELECT 
            t.id,
            t.user_id,
            u.Email,
            u.FullName,
            t.event_id,
            e.eventType,
            t.sub_event_id,
            t.number_of_people,
            t.unit_price,
            t.total_price,
            t.status,
            t.is_scanned,
            t.created_at
        FROM Tickets t
        LEFT JOIN Users u ON u.Id = t.user_id
        LEFT JOIN Event e ON e.id = t.event_id
        WHERE 1=1
    ";


    $sql .= " ORDER BY t.id DESC
              OFFSET :offset ROWS FETCH NEXT :limit ROWS ONLY";

    $stmt = $this->connection->prepare($sql);

    $stmt->bindValue('offset', $offset, \PDO::PARAM_INT);
    $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);

    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}
public function countAllWithDetails(): int
{
    $sql = "SELECT COUNT(*) 
            FROM Tickets t
            LEFT JOIN Users u ON u.Id = t.user_id
            WHERE 1=1";

    $stmt = $this->connection->prepare($sql);
    $stmt->execute();

    return (int)$stmt->fetchColumn();
}


    public function getAllWithDetails(): array // limit and offset + kidsevent duplicate event checker
    {
        $sql = "
            SELECT 
                t.id,
                t.user_id,
                u.Email,
                u.FullName,
                t.event_id,
                e.eventType,
                t.sub_event_id,
                t.number_of_people,
                t.unit_price,
                t.total_price,
                t.status,
                t.is_scanned, 
                t.created_at
            FROM Tickets t
            LEFT JOIN Users u ON u.Id = t.user_id
            LEFT JOIN Event e ON e.id = t.event_id
            ORDER BY t.id DESC
        ";

        $stmt = $this->connection->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTicketsByOrderId(string $orderId): array
    {
        $sql = "SELECT * FROM Tickets 
                WHERE stripe_session_id = :orderId 
                AND [status] = 'pending'";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['orderId' => $orderId]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


    // tickets for profile view 


    public function getTicketsByUserId(int $userId): array
    {
        $sql = "SELECT t.*, e.eventType, e.subEventId 
            FROM tickets t
            JOIN [event] e ON t.event_id = e.id
            WHERE t.user_id = :userId";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['userId' => $userId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $tickets = [];
        foreach ($rows as $row) {
            // if the DB has 'jazz', this returns EventTypeEnum::JazzEvent
            $enumType = \App\Models\Enums\EventTypeEnum::tryFrom($row['eventType']);

            //passing null for details because TicketService->hydrateTickets() will fill them
            $event = new EventModel(
                (int)$row['event_id'],
                $enumType,
                (int)$row['sub_event_id']
            );

            $ticket = new \App\Models\TicketModel(
                (int)$row['id'],
                $event,
                null,
                (int)$row['number_of_people'],
                $row['unique_ticket_token'] ?? null
            );

            $tickets[] = $ticket;
        }

        return $tickets;
    }

    public function getTicketsByUserIdPaginated(int $userId, int $page = 1, int $limit = 5): array
    {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT t.*, e.eventType, e.subEventId 
                FROM tickets t
                JOIN [event] e ON t.event_id = e.id
                WHERE t.user_id = :userId
                ORDER BY t.id DESC
                OFFSET :offset ROWS FETCH NEXT :limit ROWS ONLY";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue('userId', $userId, \PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, \PDO::PARAM_INT);
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $tickets = [];
        foreach ($rows as $row) {
            $enumType = \App\Models\Enums\EventTypeEnum::tryFrom($row['eventType']);
            $event = new \App\Models\EventModel((int)$row['event_id'], $enumType, (int)$row['subEventId']);
            $tickets[] = new \App\Models\TicketModel(
                (int)$row['id'],
                $event,
                null,
                (int)$row['number_of_people'],
                $row['unique_ticket_token'] ?? null
            );
        }
        return $tickets;
    }

    public function countTicketsByUserId(int $userId): int
    {
        $sql = "SELECT COUNT(*) FROM tickets WHERE user_id = :userId";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['userId' => $userId]);
        return (int)$stmt->fetchColumn();
    }
}
