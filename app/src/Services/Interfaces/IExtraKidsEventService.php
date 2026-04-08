<?php

namespace App\Services\Interfaces;

use App\Models\ExtraKidsEventModel; 

interface IExtraKidsEventService
{
public function getAllEvents(): array;

    public function getEventById(int $id): ?ExtraKidsEventModel;

    public function createEvent(ExtraKidsEventModel $event): bool;
    public function deleteEvent(int $id): bool;
     public function updateEvent(ExtraKidsEventModel $event): bool;
    }