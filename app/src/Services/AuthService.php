<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\PasswordResetRepository;
use Throwable;

class AuthService implements IAuthService
{
    private UserRepository $userRepo;
    private PasswordResetRepository $resetRepo;

    public function __construct()
    {
        $this->userRepo  = new UserRepository();
        $this->resetRepo = new PasswordResetRepository();
    }

    public function requestPasswordReset(string $email): array
    {
        $email = trim(strtolower($email));

        if ($email === '') {
            return ['ok' => false, 'message' => 'Email is required.'];
        }

        $genericMessage = 'If the email exists, a reset link has been created.';

        try {
            $user = $this->userRepo->findByEmail($email);

            if ($user === null) {
                return ['ok' => true, 'message' => $genericMessage];
            }

            $rawToken  = bin2hex(random_bytes(32));         // token shown to user (demo)
            $tokenHash = hash('sha256', $rawToken);         // store hash in DB
            $expiresAt = date('Y-m-d H:i:s', time() + 60 * 30); // 30 minutes

            $this->resetRepo->createReset((int)$user->id, $tokenHash, $expiresAt);

            // Demo/testing
            $link = '/reset-password/' . $rawToken;

            return ['ok' => true, 'message' => $genericMessage, 'reset_link' => $link];
        } catch (Throwable $e) {
            return ['ok' => true, 'message' => $genericMessage];
        }
    }

    public function resetPassword(string $rawToken, string $newPassword): array
    {
        $rawToken = trim($rawToken);
        if ($rawToken === '' || strlen($rawToken) < 20) {
            return ['ok' => false, 'message' => 'Invalid token.'];
        }

        if (strlen($newPassword) < 6) {
            return ['ok' => false, 'message' => 'Password must be at least 6 characters.'];
        }

        try {
            $tokenHash = hash('sha256', $rawToken);
            $reset     = $this->resetRepo->findValidByTokenHash($tokenHash);

            if ($reset === null) {
                return ['ok' => false, 'message' => 'Token is invalid or expired.'];
            }
            $userId = (int)$reset['user_id'];
            $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
            $this->userRepo->updatePassword($userId, $hashed);
            $this->resetRepo->markUsed((int)$reset['id']);
            return ['ok' => true, 'message' => 'Password updated. You can now log in.'];
        } catch (Throwable $e) {
            return ['ok' => false, 'message' => 'Something went wrong. Please try again.'];
        }
    }
}
