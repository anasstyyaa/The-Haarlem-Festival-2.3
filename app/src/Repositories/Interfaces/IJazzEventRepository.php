<?php

namespace App\Repositories\Interfaces;
use App\Models\JazzEventModel;

interface IJazzEventRepository{
    public function getAllActive(): array;
    public function getById(int $id): ?JazzEventModel;
    public function create(JazzEventModel $event): bool;
    public function update(int $id, JazzEventModel $event): bool;
    public function delete(int $id): bool;
}