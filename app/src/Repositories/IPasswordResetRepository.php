<?php

namespace App\Repositories;

interface IPasswordResetRepository
{
    public function createReset(int $userId, string $tokenHash, string $expiresAt): void;

    public function findValidByTokenHash(string $tokenHash): ?array;

    public function markUsed(int $id): void;
}
