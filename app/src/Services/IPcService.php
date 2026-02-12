<?php

namespace App\Services;

use App\Models\PcModel;

interface IPcService
{
    /** @return PcModel[] */
    public function getAllPcs(): array;

    public function getAllPcsAdmin(): array;

    public function getPcById(int $id);

    public function createPc(string $name, string $specs, string $priceRaw): array;

    public function updatePc(int $id, string $name, string $specs, string $priceRaw): array;

    public function togglePcActive(int $id): void;

    public function deletePc(int $id): void;
}
