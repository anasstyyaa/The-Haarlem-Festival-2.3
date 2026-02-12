<?php

namespace App\Services;

use App\Models\UserModel;

interface IUserService
{
    public function register(string $name, string $email, string $password, string $passwordRepeat): array;

    public function authenticate(string $email, string $password): ?UserModel;
}
