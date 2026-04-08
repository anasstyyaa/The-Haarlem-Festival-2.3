<?php

namespace App\Repositories\Interfaces;

use App\Models\UserModel;

interface IUserRepository
{
    public function getAll(): array;
    public function getById(int $id): ?UserModel;
    public function getByUsername(string $username): ?UserModel; 
    public function create(UserModel $user): bool;
    public function update(UserModel $user): bool;
    public function delete(int $id): bool;
    public function restore(int $id): bool;
    public function findByEmail (string $email): ?array;
    public function adminGetAll(): array;
    public function updatePassword(int $userId, string $hashedPassword): bool; 
    public function updateProfile(UserModel $user): bool;
    public function getAllFiltered(string $search = '', string $role = '', string $sort = '', int $page = 1, int $limit = 10): array;
    public function countAllFiltered(string $search = '', string $role = ''): int;
}