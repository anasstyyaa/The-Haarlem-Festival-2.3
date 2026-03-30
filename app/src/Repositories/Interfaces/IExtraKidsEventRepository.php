<?php

namespace App\Repositories\Interfaces;

use App\Models\ExtraKidsEventModel;

interface IExtraKidsEventRepository
{
       public function getAll(): array;

    public function getById(int $id): ?ExtraKidsEventModel;

    public function create(ExtraKidsEventModel $event): bool;

    public function delete(int $id): bool;
}