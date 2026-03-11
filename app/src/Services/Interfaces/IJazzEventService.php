<?php

namespace App\Services\Interfaces;

use App\Models\JazzEventModel;

interface IJazzEventService
{
    public function getAllJazzEvents(): array;

    public function getJazzEventById(int $id): ?JazzEventModel;

    public function createJazzEvent(JazzEventModel $event): bool;

    public function updateJazzEvent(int $id, JazzEventModel $event): bool;

    public function deleteJazzEvent(int $id): bool;
}