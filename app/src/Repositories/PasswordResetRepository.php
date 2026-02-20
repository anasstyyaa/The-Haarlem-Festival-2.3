<?php

namespace App\Repositories;

use App\Config\Config; // adjust if your PDO class is elsewhere

class PasswordResetRepository
{
    public function createForUserId(int $userId): string
    {
        $pdo = Config::getPDO();

        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expiresAt = date('Y-m-d H:i:s', time() + 3600);

        // optional: delete old tokens for same user
        $pdo->prepare("DELETE FROM password_resets WHERE user_id = :uid")
            ->execute(['uid' => $userId]);

        $stmt = $pdo->prepare("
            INSERT INTO password_resets (user_id, token_hash, expires_at)
            VALUES (:user_id, :token_hash, :expires_at)
        ");

        $stmt->execute([
            'user_id'    => $userId,
            'token_hash' => $tokenHash,
            'expires_at' => $expiresAt,
        ]);

        return $token; // return plain token so you can email it
    }

    public function findValidByToken(string $token): ?array
    {
        $pdo = Config::getPDO();

        $tokenHash = hash('sha256', $token);

        $stmt = $pdo->prepare("
            SELECT id, user_id, token_hash, expires_at
            FROM password_resets
            WHERE token_hash = :token_hash
              AND expires_at > NOW()
            LIMIT 1
        ");

        $stmt->execute(['token_hash' => $tokenHash]);

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function consumeById(int $id): void
    {
        $pdo = Config::getPDO();

        $stmt = $pdo->prepare("DELETE FROM password_resets WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }
}
