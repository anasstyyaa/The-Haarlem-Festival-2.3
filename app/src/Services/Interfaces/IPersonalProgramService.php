<?php

namespace App\Services\Interfaces;

interface IPersonalProgramService
{
      public function addTicketToProgram(int $eventId, int $numberOfPeople, ?int $userId, ?int $programItemId = null): void;
}