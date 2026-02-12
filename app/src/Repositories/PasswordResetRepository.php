<?php

namespace App\Repositories;

use App\Framework\Repository;
use PDO;
use PDOException;
use RuntimeException;

class PasswordResetRepository extends Repository implements IPasswordResetRepository
{
    public function createReset(int $userId, string $tokenHash, string $expiresAt): void
    {
        try {
            $sql = "
                INSERT INTO password_resets (user_id, token_hash, expires_at, created_at)
                VALUES (:user_id, :token_hash, :expires_at, :created_at)
            ";

            $stmt = $this->connection->prepare($sql);
            $stmt->execute([
                ':user_id'    => $userId,
                ':token_hash' => $tokenHash,
                ':expires_at' => $expiresAt,
                ':created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException('Failed to create password reset.');
        }
    }

    public function findValidByTokenHash(string $tokenHash): ?array
    {
        try {
            $sql = "
                SELECT *
                FROM password_resets
                WHERE token_hash = :token_hash
                  AND used_at IS NULL
                  AND expires_at > NOW()
                ORDER BY id DESC
                LIMIT 1
            ";

            $stmt = $this->connection->prepare($sql);
            $stmt->execute([':token_hash' => $tokenHash]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (PDOException $e) {
            throw new RuntimeException('Failed to load password reset.');
        }
    }

    public function markUsed(int $id): void
    {
        try {
            $sql = "
                UPDATE password_resets
                SET used_at = :used_at
                WHERE id = :id
            ";

            $stmt = $this->connection->prepare($sql);
            $stmt->execute([
                ':used_at' => date('Y-m-d H:i:s'),
                ':id'      => $id,
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException('Failed to mark password reset as used.');
        }
    }
}
