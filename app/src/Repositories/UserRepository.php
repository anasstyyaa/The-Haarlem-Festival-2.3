<?php
namespace App\Repositories;

use App\Framework\Repository;
use App\Models\UserModel;
use App\Repositories\Interfaces\IUserRepository;
use PDO;

class UserRepository extends Repository implements IUserRepository
{
    public function getAll(): array
    {
        $stmt = $this->connection->query("SELECT * FROM users WHERE deleted_at IS NULL");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return array_map(fn($row) => $this->mapToModel($row), $results);
    }

    public function getById(int $id): ?UserModel
    {
        $stmt = $this->connection->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapToModel($row) : null;
    }

    public function create(UserModel $user): bool
    {
        $sql = "INSERT INTO users (email, password, userName, fullName, phoneNumber, role, created_at, profilePicture) 
                VALUES (:email, :password, :userName, :fullName, :phoneNumber, :role, :created_at, :profilePicture)";
        
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute([
            'email'          => $user->getEmail(),
            'password'       => password_hash($user->getPassword(), PASSWORD_DEFAULT),
            'userName'       => $user->getUserName(),
            'fullName'       => $user->getFullName(),
            'phoneNumber'    => $user->getPhoneNumber(),
            'role'           => $user->getRole(),
            'created_at'     => $user->getCreatedAt(),
            'profilePicture' => $user->getProfilePicture()
        ]);
    }

    public function update(UserModel $user): bool
    {
        $sql = "UPDATE users SET 
                email = :email, userName = :userName, fullName = :fullName, 
                phoneNumber = :phoneNumber, role = :role, updated_at = GETDATE(), 
                profilePicture = :pic 
                WHERE id = :id";
        
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute([
            'email'       => $user->getEmail(),
            'userName'    => $user->getUserName(),
            'fullName'    => $user->getFullName(),
            'phoneNumber' => $user->getPhoneNumber(),
            'role'        => $user->getRole(),
            'pic'         => $user->getProfilePicture(),
            'id'          => $user->getId()
        ]);
    }

    public function delete(int $id): bool
    {
        $sql = "UPDATE users SET deleted_at = GETDATE() WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function restore(int $id): bool
    {
        $sql = "UPDATE users SET deleted_at = NULL WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    private function mapToModel(array $row): UserModel
    {
        return new UserModel(
            (int)$row['id'],
            $row['email'],
            $row['password'],
            $row['userName'],
            $row['fullName'],
            $row['phoneNumber'],
            $row['role'],
            $row['created_at'],
            $row['updated_at'],
            $row['profilePicture'],
            $row['deleted_at']
        );
    }

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
}


