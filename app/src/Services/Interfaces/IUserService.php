<?php 

namespace App\Services\Interfaces;

use App\Models\UserModel;

interface IUserService
{
    public function getAllUsers(): array;
    public function getUserById(int $id): ?UserModel;
    public function createUser(UserModel $user): bool;
    public function updateUser(UserModel $user): bool;
    public function deleteUser(int $id): bool;
    public function restoreUser(int $id): bool;
}