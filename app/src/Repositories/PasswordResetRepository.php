<?php
namespace App\Repositories;

use App\Framework\Repository;
use PDO;

class PasswordResetRepository extends Repository
{
    public function create(int $userId, string $tokenHash, string $expiresAt): bool
    {
        $sql = "INSERT INTO password_resets (user_id, token_hash, expires_at)
                VALUES (:user_id, :token_hash, :expires_at)";

        $stmt = $this->connection->prepare($sql);
        return $stmt->execute([
            'user_id'    => $userId,
            'token_hash' => $tokenHash,
            'expires_at' => $expiresAt,
        ]);
    }

    public function findValidByTokenHash(string $tokenHash): ?array
    {
        $sql = "SELECT TOP 1 *
                FROM password_resets
                WHERE token_hash = :token_hash
                  AND used_at IS NULL
                  AND expires_at > SYSDATETIME()
                ORDER BY id DESC";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['token_hash' => $tokenHash]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function markUsed(int $id): bool
    {
        $sql = "UPDATE password_resets
                SET used_at = SYSDATETIME()
                WHERE id = :id";

        $stmt = $this->connection->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
