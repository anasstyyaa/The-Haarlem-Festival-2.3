<?php

namespace App\Services;

interface IAuthService
{
    /**
     * Request a password reset.
     * Returns a generic success message even if email does not exist.
     */
    public function requestPasswordReset(string $email): array;

    /**
     * Reset password using a raw reset token.
     */
    public function resetPassword(string $rawToken, string $newPassword): array;
}
