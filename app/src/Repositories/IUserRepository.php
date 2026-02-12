<?php

namespace App\Repositories;

use App\Models\UserModel;

interface IUserRepository
{
    public function create(UserModel $user): void;

    public function findByEmail(string $email): ?UserModel;

    public function findById(int $id): ?UserModel;

    public function updatePassword(int $userId, string $passwordHash): void; //for forgot password functionality

    public function updateProfile(int $userId, string $name, string $email): void; //for username and email update

}
