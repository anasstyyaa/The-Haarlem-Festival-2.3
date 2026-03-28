<?php

namespace App\Services\Interfaces;

use App\Models\TicketModel; 

interface IPersonalProgramService
{
      public function addTicketToProgram(int $eventId, int $numberOfPeople, ?int $userId, ?int $programItemId = null): void;
      public function syncUserToTicket(TicketModel $ticket, int $userId): void; 
}