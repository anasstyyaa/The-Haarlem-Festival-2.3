<?php

namespace App\Repositories;

use App\Framework\Repository; 
use App\Models\UserModel;
use PDO;
use PDOException;
use RuntimeException;

class UserRepository extends Repository implements IUserRepository
{
    public function create(UserModel $user): void
    {
        try {
            $sql = "
                INSERT INTO users (name, email, password_hash, role)
                VALUES (:name, :email, :password_hash, :role)
            ";

            $stmt = $this->connection->prepare($sql);
            $stmt->execute([
                ':name'          => $user->name,
                ':email'         => $user->email,
                ':password_hash' => $user->password_hash,
                ':role'          => $user->role,
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException('Failed to create user.');
        }
    }

    public function findByEmail(string $email): ?UserModel
    {
        try {
            $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";

            $stmt = $this->connection->prepare($sql);
            $stmt->execute([':email' => $email]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? $this->mapRowToUser($row) : null;
        } catch (PDOException $e) {
            throw new RuntimeException('Failed to load user by email.');
        }
    }

    public function findById(int $id): ?UserModel
    {
        try {
            $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";

            $stmt = $this->connection->prepare($sql);
            $stmt->execute([':id' => $id]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? $this->mapRowToUser($row) : null;
        } catch (PDOException $e) {
            throw new RuntimeException('Failed to load user.');
        }
    }

    public function updatePassword(int $userId, string $passwordHash): void
    {
        try {
            $sql = "UPDATE users SET password_hash = :hash WHERE id = :id";

            $stmt = $this->connection->prepare($sql);
            $stmt->execute([
                ':hash' => $passwordHash,
                ':id'   => $userId,
            ]);

            if ($stmt->rowCount() === 0) {
                throw new RuntimeException('User not found.');
            }
        } catch (PDOException $e) {
            throw new RuntimeException('Failed to update password.');
        }
    }

    public function updateProfile(int $userId, string $name, string $email): void
    {
        try {
            $sql = "
                UPDATE users
                SET name = :name, email = :email
                WHERE id = :id
            ";

            $stmt = $this->connection->prepare($sql);
            $stmt->execute([
                ':name'  => $name,
                ':email' => $email,
                ':id'    => $userId,
            ]);

            if ($stmt->rowCount() === 0) {
                throw new RuntimeException('User not found or no changes applied.');
            }
        } catch (PDOException $e) {
            throw new RuntimeException('Failed to update profile.');
        }
    }

    private function mapRowToUser(array $row): UserModel
    {
        $user = new UserModel();
        $user->id            = (int)($row['id'] ?? 0);
        $user->name          = (string)($row['name'] ?? '');
        $user->email         = (string)($row['email'] ?? '');
        $user->password_hash = (string)($row['password_hash'] ?? '');
        $user->role          = (string)($row['role'] ?? 'customer');
        $user->created_at    = $row['created_at'] ?? null;

        return $user;
    }
}
