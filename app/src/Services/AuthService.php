<?php

namespace App\Services;

use App\Models\UserModel; 
use App\Repositories\UserRepository;
use App\Services\Interfaces\IAuthService; 

class AuthService implements IAuthService
{
    public function __construct(private UserRepository $userRepository) 
    {
         $this->userRepository = $userRepository;
    }

    public function emailExists(string $email): bool
    {
        return $this->userRepository->findByEmail($email) !== null;
    }

    public function createUser(UserModel $user): bool
    {
        return $this->userRepository->create($user);
    }

    public function getUserByEmail(string $email): ?array
    {
        return $this->userRepository->findByEmail($email);
    }
    public function logout(): void
    {
        session_destroy();
    }

    
}
