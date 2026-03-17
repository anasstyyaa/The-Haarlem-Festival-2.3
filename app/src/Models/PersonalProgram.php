<?php
namespace App\Models;
class PersonalProgram
{
    /**
     * @var TicketModel[]
     */
    private array $tickets = [];

    public function __construct(array $tickets = [])
    {
        $this->tickets = $tickets;
    }

    public function addTicket(TicketModel $ticket): void
    {
        $ticket->setProgramItemId(count($this->tickets) + 1);
        $this->tickets[] = $ticket;
    }
    public function removeTicket(int $ticketId): void
    {
        foreach ($this->tickets as $index => $ticket) {
            if ($ticket->getProgramItemId() === $ticketId) {
                unset($this->tickets[$index]);
            }
        }

        $this->tickets = array_values($this->tickets);
    }

    public function getTickets(): array
    {
        return $this->tickets;
    }

    // public function getTotalPrice(): float
    // {
    //     $total = 0;

    //     foreach ($this->tickets as $ticket) {
    //         $total += $ticket->getPrice();
    //     }

    //     return $total;
    // }


    public function clear(): void
    {
        $this->tickets = [];
    }
}
