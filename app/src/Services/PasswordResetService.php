<?php

namespace App\Services;

use App\Services\Interfaces\IPasswordResetService;
use App\Repositories\UserRepository;
use App\Repositories\PasswordResetRepository;

class PasswordResetService implements IPasswordResetService
{
    public function __construct(
        private UserRepository $userRepository,
        private PasswordResetRepository $passwordResetRepository
    ) {}

    public function forgotPasswordPage(array $session): array
    {
        return [
            'data' => [
                'title'   => 'Forgot Password',
                'error'   => $session['error'] ?? null,
                'success' => $session['success'] ?? null,
                'oldEmail'=> $session['old_email'] ?? '',
            ]
        ];
    }

    public function requestPassword(string $email): void
    {
        $email = trim($email);

        // optional basic validation
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Please enter a valid email.';
            $_SESSION['old_email'] = $email;
            return;
        }

        $user = $this->userRepository->findByEmail($email);

        // security: never reveal if user exists
        if ($user !== null) {
            // Repo generates token, stores hash, sets expiry
            // It returns the plain token so you can email it (later).
            $token = $this->passwordResetRepository->createForUserId((int)$user['id']);

            // TODO: send email containing link:
            // /resetPassword?token=YOUR_TOKEN
            // (For now you can log it or test manually)
            // error_log("RESET LINK: /resetPassword?token=" . $token);
        }

        $_SESSION['success'] = 'If the email exists, a reset link has been sent.';
        unset($_SESSION['error']);
    }

    public function resetPasswordPage(string $token): array
    {
        $token = trim($token);

        if ($token === '') {
            $_SESSION['error'] = 'Reset link is invalid.';
            return ['redirect' => '/forgetPassword', 'data' => []];
        }

        $row = $this->passwordResetRepository->findValidByToken($token);

        if ($row === null) {
            $_SESSION['error'] = 'Reset link is invalid or expired.';
            return ['redirect' => '/forgetPassword', 'data' => []];
        }

        return [
            'redirect' => null,
            'data' => [
                'title' => 'Reset Password',
                'error' => $_SESSION['error'] ?? null,
                'token' => $token,
            ],
        ];
    }

    public function resetPassword(string $token, string $password, string $confirm): array
    {
        $token = trim($token);

        if ($token === '' || $password === '' || $confirm === '') {
            $_SESSION['error'] = 'All fields are required.';
            return ['redirect' => '/resetPassword?token=' . urlencode($token)];
        }

        if ($password !== $confirm) {
            $_SESSION['error'] = 'Passwords do not match.';
            return ['redirect' => '/resetPassword?token=' . urlencode($token)];
        }

        if (strlen($password) < 8) {
            $_SESSION['error'] = 'Password must be at least 8 characters.';
            return ['redirect' => '/resetPassword?token=' . urlencode($token)];
        }

        $row = $this->passwordResetRepository->findValidByToken($token);

        if ($row === null) {
            $_SESSION['error'] = 'Reset link is invalid or expired.';
            return ['redirect' => '/forgetPassword'];
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $this->userRepository->updatePasswordHash((int)$row['user_id'], $passwordHash);
        $this->passwordResetRepository->consumeById((int)$row['id']);

        $_SESSION['success'] = 'Password updated successfully. Please login.';
        unset($_SESSION['error']);

        return ['redirect' => '/login'];
    }
}
