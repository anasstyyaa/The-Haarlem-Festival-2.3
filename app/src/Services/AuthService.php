<?php

namespace App\Services;

use App\Repositories\UserRepository;

class AuthService
{
    public function __construct(private UserRepository $users) {}

    public function register(array $input): array
    {
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';
        $userName = trim($input['userName'] ?? '');
        $fullName = trim($input['fullName'] ?? '');
        $phoneNumber = trim($input['phoneNumber'] ?? '');

        if ($email === '' || $password === '' || $userName === '' || $fullName === '' || $phoneNumber === '') {
            return ['ok' => false, 'error' => 'Please fill in all fields.'];
        }

        if ($this->users->findByEmail($email)) {
            return ['ok' => false, 'error' => 'Email already exists.'];
        }

        if ($this->users->findByUserName($userName)) {
            return ['ok' => false, 'error' => 'Username already exists.'];
        }

        $id = $this->users->create([
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'userName' => $userName,
            'fullName' => $fullName,
            'phoneNumber' => $phoneNumber,
            'role' => 'User',
        ]);

        $_SESSION['user'] = [
            'id' => $id,
            'email' => $email,
            'userName' => $userName,
            'role' => 'User',
        ];

        return ['ok' => true];
    }

    public function login(string $email, string $password): array
    {
        $email = trim($email);

        if ($email === '' || $password === '') {
            return ['ok' => false, 'error' => 'Enter email and password.'];
        }

        $user = $this->users->findByEmail($email);

        if (!$user || !password_verify($password, $user['Password'])) {
            return ['ok' => false, 'error' => 'Invalid credentials.'];
        }

        $_SESSION['user'] = [
            'id' => (int)$user['Id'],
            'email' => $user['Email'],
            'userName' => $user['UserName'],
            'role' => $user['Role'],
        ];

        return ['ok' => true];
    }

    public function logout(): void
    {
        session_destroy();
    }
}
