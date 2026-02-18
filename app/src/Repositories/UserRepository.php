<?php

namespace App\Repositories;

use App\Framework\Repository;
use PDO; 

class UserRepository extends Repository
{
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->connection->prepare("SELECT * FROM Users WHERE Email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch() ?: null;
    }

    public function findByUserName(string $userName): ?array
    {
        $stmt = $this->connection->prepare("SELECT * FROM Users WHERE UserName = :userName");
        $stmt->execute(['userName' => $userName]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->connection->prepare("
            INSERT INTO Users (Email, Password, UserName, FullName, PhoneNumber, Role)
            VALUES (:email, :password, :userName, :fullName, :phoneNumber, :role)
        ");

        $stmt->execute([
            'email' => $data['email'],
            'password' => $data['password'], // hashed
            'userName' => $data['userName'],
            'fullName' => $data['fullName'],
            'phoneNumber' => $data['phoneNumber'],
            'role' => $data['role'] ?? 'User',
        ]);

        return (int)$this->connection->query("SELECT SCOPE_IDENTITY()")->fetchColumn();
    }
}
