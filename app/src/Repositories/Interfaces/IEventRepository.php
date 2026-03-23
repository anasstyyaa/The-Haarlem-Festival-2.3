<?php

namespace App\Repositories\Interfaces;

use App\Models\EventModel;

interface IEventRepository
{
     public function getAll(): array;

    public function getById(int $id): ?EventModel;

    public function create(EventModel $event): bool;

    public function update(EventModel $event): bool;

    public function delete(int $id): bool;

    private function mapToModel(array $row): EventModel;

    public function checkEventType(int $subEventId, string $eventType):int;

}