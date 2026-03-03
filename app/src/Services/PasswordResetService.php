<?php
declare(strict_types=1);

namespace App\Services;
use App\Services\Mailer;
use App\Repositories\UserRepository;
use App\Repositories\Interfaces\IPasswordResetRepository;

final class PasswordResetService
{
    public function __construct(
        private UserRepository $users,
        private IPasswordResetRepository $resets,
        private Mailer $mailer,
        private string $appUrl
    ) {}

    public function sendResetLink(string $email): void
    {
        $user = $this->users->findByEmail($email);

        // do not leak whether email exists
        if (!$user) {
            return;
        }

        $rawToken  = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $rawToken);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        //  User row key might be Id or id depending on your query
        $userId = (int)($user['Id'] ?? $user['id'] ?? 0);
        if ($userId <= 0) {
            throw new \RuntimeException('User id not found.');
        }

        $this->resets->create($userId, $tokenHash, $expiresAt);

        $link = rtrim($this->appUrl, '/') . '/resetPassword?token=' . urlencode($rawToken);

        // Testing no email yet
       $this->mailer->send(
    $email,
    'Reset your password',
    "Click this link to reset your password:\n\n{$link}\n\nIf you did not request this, ignore this email."
);
        
        
    }

    public function resetPasswordByToken(string $rawToken, string $newPassword): void
    {
        $tokenHash = hash('sha256', $rawToken);

        $row = $this->resets->findValidToken($tokenHash);
        if (!$row) {
            throw new \RuntimeException('Token is invalid or expired.');
        }

        $hash = password_hash($newPassword, PASSWORD_DEFAULT);

        
        // updatePassword int $userId, string $hash
        $this->users->updatePassword((int)$row['user_id'], $hash);

        $this->resets->markAsUsed((int)$row['id']);
    }
}