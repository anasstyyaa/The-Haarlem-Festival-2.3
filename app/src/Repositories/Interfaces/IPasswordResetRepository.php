<?php
declare(strict_types=1);

namespace App\Repositories\Interfaces;

interface IPasswordResetRepository
{
    public function create(int $userId, string $tokenHash, string $expiresAt): bool;

    public function findValidToken(string $tokenHash): ?array;

    public function markAsUsed(int $id): void;
}