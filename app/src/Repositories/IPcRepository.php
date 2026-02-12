<?php

namespace App\Repositories;

use App\Models\PcModel;

interface IPcRepository
{
    /** @return PcModel[] */
    public function getAll(): array;

    public function findById(int $id): ?PcModel;
    
    public function insertPc(
        string $name,
        string $specs,
        ?float $pricePerHour,
        bool $isActive
    ): void;

    public function updatePc(
        int $id,
        string $name,
        string $specs,
        ?float $pricePerHour
    ): void;

    public function toggleActive(int $id): void;

    public function deleteById(int $id): void;
}
