<?php

namespace App\Services\Interfaces;

interface IPasswordResetService
{
    public function forgotPasswordPage(array $session): array;

    public function requestPassword(string $email): void;

    public function resetPasswordPage(string $token): array;

    public function resetPassword(string $token, string $password, string $confirm): array;
}
