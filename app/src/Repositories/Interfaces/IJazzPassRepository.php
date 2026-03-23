<?php

namespace App\Repositories\Interfaces;

use App\Models\JazzPassModel;

interface IJazzPassRepository
{
    public function getAllActive(): array;

    public function getAllAdmin(): array;

    public function getById(int $id): ?JazzPassModel;

    public function create(JazzPassModel $pass): bool;

    public function update(int $id, JazzPassModel $pass): bool;

    public function decreaseTicketsLeft(int $id, int $amount): bool;

    public function delete(int $id): bool;
}