<?php

namespace App\Repositories\Interfaces;

use App\Models\KidsEventModel;

interface IKidsEventRepository
{
  public function getAll(): array;
    public function getById(int $id): ?KidsEventModel;

    public function getIdBySchedule(string $day, string $startTime, string $endTime): ?KidsEventModel;

    public function create(KidsEventModel $event): bool;

    public function update(KidsEventModel $event): bool;

    public function delete(int $id): bool;

}