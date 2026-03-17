<?php

namespace App\Repositories\Interfaces;
use App\Models\JazzEventModel;
use App\Models\Enums\EventTypeEnum; 

interface IJazzEventRepository{
    public function getAllActive(): array;
    public function getById(int $id): ?JazzEventModel;
    public function getEventsForArtist(int $artistId, EventTypeEnum $eventType): array;
    public function create(JazzEventModel $event): bool;
    public function update(int $id, JazzEventModel $event): bool;
    public function delete(int $id): bool;
}