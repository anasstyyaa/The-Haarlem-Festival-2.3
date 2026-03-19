<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Repositories\Interfaces\IPasswordResetRepository;
use PDO;

class PasswordResetRepository extends Repository implements IPasswordResetRepository
{
    public function create(int $userId, string $tokenHash, string $expiresAt): bool
    {
        $stmt = $this->connection->prepare("
            INSERT INTO password_resets (user_id, token_hash, expires_at, used_at, created_at)
            VALUES (:user_id, :token_hash, :expires_at, NULL, CURRENT_TIMESTAMP)
        ");

        return $stmt->execute([
            'user_id' => $userId,
            'token_hash' => $tokenHash,
            'expires_at' => $expiresAt
        ]);
    }

    public function findValidToken(string $tokenHash): ?array
    {
        $stmt = $this->connection->prepare("
            SELECT *
            FROM password_resets
            WHERE token_hash = :token_hash
              AND used_at IS NULL
              AND expires_at > CURRENT_TIMESTAMP
        ");

        $stmt->execute([
            'token_hash' => $tokenHash
        ]);

        $reset = $stmt->fetch(PDO::FETCH_ASSOC);

        return $reset ?: null;
    }

    public function markAsUsed(int $id): void
    {
        $stmt = $this->connection->prepare("
            UPDATE password_resets
            SET used_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ");

        $stmt->execute([
            'id' => $id
        ]);
    }
}