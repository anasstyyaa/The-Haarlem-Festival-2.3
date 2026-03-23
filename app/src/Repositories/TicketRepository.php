<?php 

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\TicketModel;
use PDO;

class TicketRepository extends Repository
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
                    is_scanned
                ) 
                VALUES (:uid, :eid, :subid, :count, :uprice, :tprice, 'paid', :stripe, :token, 0)";

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
            'token'  => $token
        ]);
    }

    public function savePendingTicket(TicketModel $ticket, string $tempOrderId): bool 
    {
        $sql = "INSERT INTO Tickets (
                    user_id, event_id, sub_event_id, number_of_people, 
                    unit_price, total_price, [status], stripe_session_id,
                    unique_ticket_token, is_scanned, created_at
                ) 
                VALUES (:uid, :eid, :subid, :count, :uprice, :tprice, 'pending', :orderId, :token, 0, GETDATE())";

        $stmt = $this->connection->prepare($sql);
        $token = bin2hex(random_bytes(16));

        return $stmt->execute([
            'uid'     => $ticket->getUser() ? $ticket->getUser()->getId() : null,
            'eid'     => $ticket->getEvent()->getId(),
            'subid'   => $ticket->getEvent()->getSubEventId(),
            'count'   => $ticket->getNumberOfPeople(),
            'uprice'  => $ticket->getUnitPrice(),
            'tprice'  => $ticket->getTotalPrice(),
            'orderId' => $tempOrderId, 
            'token'   => $token
        ]);
    }

    public function updateTicketsToPaid(string $orderId, string $actualStripeId): bool 
    {
        $sql = "UPDATE Tickets SET [status] = 'paid', stripe_session_id = :stripe 
                WHERE stripe_session_id = :orderId AND [status] = 'pending'";
        
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute([
            'stripe'  => $actualStripeId,
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

    public function getAll(): array
    {
        $sql = "SELECT * FROM Tickets ORDER BY id DESC";
        $stmt = $this->connection->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTicketsByOrderId(string $orderId): array
    {
        $sql = "SELECT * FROM Tickets WHERE stripe_session_id = :orderId AND [status] = 'pending'";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['orderId' => $orderId]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}