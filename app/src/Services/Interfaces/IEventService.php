<?php

namespace App\Services\Interfaces;

use App\Models\EventModel; 

interface IEventService
{
     public function getAll(): array;

    public function getById(int $id): ?EventModel;

    public function create(EventModel $event): bool;

    public function update(EventModel $event): bool;

    public function delete(int $id): bool;


    public function checkEventType(int $subEventId, string $eventType):int;
    }