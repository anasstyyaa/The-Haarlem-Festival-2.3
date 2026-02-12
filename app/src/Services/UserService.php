<?php

namespace App\Services;

use App\Models\UserModel;
use App\Repositories\UserRepository;
use Throwable;

class UserService implements IUserService
{
    private UserRepository $repository;

    public function __construct()
    {
        $this->repository = new UserRepository();
    }

    public function register(string $name, string $email, string $password, string $passwordRepeat): array
    {
        $errors = [];

        $name  = trim($name);
        $email = trim(strtolower($email));

        if ($name === '') {
            $errors[] = 'Name is required.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'A valid email is required.';
        }

        if ($password === '' || strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters.';
        }

        if ($password !== $passwordRepeat) {
            $errors[] = 'Passwords do not match.';
        }

        if (!empty($errors)) {
            return $errors;
        }

        try {
            if ($this->repository->findByEmail($email) !== null) {
                return ['This email is already registered.'];
            }

            $user = new UserModel();
            $user->name          = $name;
            $user->email         = $email;
            $user->password_hash = password_hash($password, PASSWORD_DEFAULT);
            $user->role          = 'customer';

            $this->repository->create($user);

            return [];
        } catch (Throwable $e) {
            return ['Something went wrong. Please try again later.'];
        }
    }

    public function authenticate(string $email, string $password): ?UserModel
    {
        $email = trim(strtolower($email));

        try {
            $user = $this->repository->findByEmail($email);
        } catch (Throwable $e) {
            return null;
        }

        if ($user === null) {
            return null;
        }

        return password_verify($password, $user->password_hash) ? $user : null;
    }

    public function getById(int $id): ?UserModel
    {
        try {
            return $this->repository->findById($id);
        } catch (Throwable $e) {
            return null;
        }
    }

    public function updateProfile(int $userId, string $name, string $email): array
    {
        $name  = trim($name);
        $email = trim(strtolower($email));

        $errors = [];

        if ($name === '') {
            $errors[] = 'Name is required.';
        } elseif (strlen($name) < 2) {
            $errors[] = 'Name must be at least 2 characters.';
        }

        if ($email === '') {
            $errors[] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email format is invalid.';
        }

        if (!empty($errors)) {
            return $errors;
        }

        try {
            $existing = $this->repository->findByEmail($email);
            if ($existing !== null && (int)$existing->id !== $userId) {
                return ['That email is already in use.'];
            }

            $this->repository->updateProfile($userId, $name, $email);

            return [];
        } catch (Throwable $e) {
            return ['Something went wrong while updating your profile. Please try again.'];
        }
    }
}
