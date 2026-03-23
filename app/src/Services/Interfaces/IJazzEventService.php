<?php

namespace App\Services\Interfaces;
use App\Models\Enums\EventTypeEnum;
use App\Models\JazzEventModel;

interface IJazzEventService
{
    public function getAllJazzEvents(): array;

    public function getJazzEventById(int $id): ?JazzEventModel;

    public function getEventsForArtist(int $artistId, EventTypeEnum $eventType): array;
    
    public function getVenueInfoByJazzEventId(int $jazzEventId): ?array;

    public function createJazzEvent(JazzEventModel $event): bool;

    public function updateJazzEvent(int $id, JazzEventModel $event): bool;
    public function decreaseTicketsLeft(int $jazzEventId, int $quantity): bool;

    public function deleteJazzEvent(int $id): bool;
}