<?php

namespace App\Services\Interfaces;

interface IPasswordResetService
{
    public function sendResetLink(string $email): void;
}