<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Repositories\Interfaces\IPasswordResetRepository;
use PDO;

class PasswordResetRepository extends Repository implements IPasswordResetRepository
{
  

    public function create(int $userId, string $tokenHash, string $expiresAt): bool
    {
        $sql = "INSERT INTO password_resets (user_id, token_hash, expires_at)
                VALUES (:user_id, :token_hash, :expires_at)";

        $stmt = $this->connection->prepare($sql);

        return $stmt->execute([
            ':user_id' => $userId,
            ':token_hash' => $tokenHash,
            ':expires_at' => $expiresAt
        ]);
    }

    public function findValidToken (string $tokenHash): ?array
    {
        $sql = "SELECT TOP 1 *
                FROM password_resets
                WHERE token_hash = :token_hash
                AND used_at IS NULL
                AND expires_at > GETDATE()";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':token_hash' => $tokenHash]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function markAsUsed(int $id): void
    {
        $sql = "UPDATE password_resets
                SET used_at = GETDATE()
                WHERE id = :id";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':id' => $id]);
    }

}