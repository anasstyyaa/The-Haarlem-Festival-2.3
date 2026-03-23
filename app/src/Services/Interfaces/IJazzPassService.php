<?php

namespace App\Services\Interfaces;

use App\Models\JazzPassModel;

interface IJazzPassService
{
    public function getAllActivePasses(): array;

    public function getAllAdminPasses(): array;

    public function getPassById(int $id): ?JazzPassModel;

    public function createPass(JazzPassModel $pass): bool;

    public function updatePass(int $id, JazzPassModel $pass): bool;
    public function decreaseTicketsLeft(int $jazzPassId, int $quantity): bool;

    public function deletePass(int $id): bool;
}