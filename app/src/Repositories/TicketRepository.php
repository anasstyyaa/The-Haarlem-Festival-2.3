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

    public function getByToken(string $token): ?array //retrieves a ticket by its unique id, used while scanning to validate the tickets.
    {
        try {
            $sql = "SELECT * FROM Tickets WHERE unique_ticket_token = :token";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute(['token' => $token]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row ?: null;
        } catch (\Exception $e) {
            error_log("Error fetching ticket by token: " . $e->getMessage());
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
}
