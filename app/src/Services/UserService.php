<?php

namespace App\Services;

use App\Models\UserModel;
use App\Repositories\Interfaces\IUserRepository;
use App\Services\Interfaces\IUserService;


class UserService implements IUserService
{
    private IUserRepository $userRepository;

    public function __construct(IUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers(): array
    {
        return $this->userRepository->getAll();
    }

    public function getUserById(int $id): ?UserModel
    {
        return $this->userRepository->getById($id);
    }

    public function createUser(UserModel $user): bool
    {
        return $this->userRepository->create($user);
    }

    public function updateUser(UserModel $user): bool
    {
        return $this->userRepository->update($user);
    }

    public function deleteUser(int $id): bool
    {
        return $this->userRepository->delete($id);
    }

    public function restoreUser(int $id): bool
    {
        return $this->userRepository->restore($id);
    }

    public function adminGetAll(): array
    {
        return $this->userRepository->adminGetAll();
    }
}
