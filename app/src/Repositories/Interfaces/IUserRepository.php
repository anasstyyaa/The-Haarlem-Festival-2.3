<?php

namespace App\Repositories\Interfaces;

use App\Models\UserModel;

interface IUserRepository
{
    public function getAll(): array;
    public function getById(int $id): ?UserModel;
    public function create(UserModel $user): bool;
    public function update(UserModel $user): bool;
    public function delete(int $id): bool;
    public function restore(int $id): bool;
    public function findByEmail (string $email): ?array;
    public function adminGetAll(): array;
    public function updatePassword(int $userId, string $hashedPassword): bool;
    
    
}