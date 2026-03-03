<?php
namespace App\Services\Interfaces;

interface IPasswordResetService
{
    public function requestReset(string $email): array;
    public function validateToken(string $rawToken): array;
    public function resetPassword(string $rawToken, string $password, string $confirm): array;
}