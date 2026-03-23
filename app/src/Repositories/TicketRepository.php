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
    public function getAll(): array
{
    $sql = "SELECT * FROM Tickets ORDER BY id DESC";
    $stmt = $this->connection->query($sql);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}