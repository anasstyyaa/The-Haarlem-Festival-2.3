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
        $sql = "INSERT INTO dbo.Users (Email, Password, UserName, FullName, PhoneNumber, Role, Created_At,     ProfilePicture) 
        VALUES (:email, :password, :userName, :fullName, :phoneNumber, :role, :created_at, :profilePicture)";

        $stmt = $this->connection->prepare($sql);
        return $stmt->execute([
            'email'          => $user->getEmail(),
            'password'       => $user->getPassword(),
            'userName'       => $user->getUserName(),
            'fullName'       => $user->getFullName(),
            'phoneNumber'    => $user->getPhoneNumber(),
            'role'           => $user->getRole(),
            'created_at'     => $user->getCreatedAt(),
            'profilePicture' => $user->getProfilePicture()  //should be fileName 
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
            (int)($row['Id'] ?? 0),
            $row['Email'] ?? '',
            $row['Password'] ?? '',
            $row['UserName'] ?? '',
            $row['FullName'] ?? '',
            $row['PhoneNumber'] ?? '',
            $row['Role'] ?? '',
            $row['Created_At'] ?? '',
            $row['Updated_At'] ?? null,
            $row['ProfilePicture'] ?? null,
            $row['Deleted_At'] ?? null
        );
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->connection->prepare("SELECT * FROM Users WHERE Email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch() ?: null;
    }

    /*public function findByUserName(string $userName): ?array
    {
        $stmt = $this->connection->prepare("SELECT * FROM Users WHERE userName = :userName");
        $stmt->execute(['userName' => $userName]);
        return $stmt->fetch() ?: null;
    }
    */

    public function adminGetAll(): array
    {
        $stmt = $this->connection->query("SELECT * FROM users");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => $this->mapToModel($row), $results);
    }

    public function updateProfile(\App\Models\UserModel $user): bool
    {
        $sql = "UPDATE dbo.Users
            SET Email = :email,
                UserName = :userName,
                FullName = :fullName,
                PhoneNumber = :phoneNumber,
                Password = :password,
                ProfilePicture = :profilePicture,
                Updated_At = GETDATE()
            WHERE Id = :id AND Deleted_At IS NULL";

        $stmt = $this->connection->prepare($sql);

        return $stmt->execute([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'userName' => $user->getUserName(),
            'fullName' => $user->getFullName(),
            'phoneNumber' => $user->getPhoneNumber(),
            'password' => $user->getPassword(),
            'profilePicture' => $user->getProfilePicture()
        ]);
    }

    public function getAllFiltered(string $search = '', string $role = '', string $sort = ''): array
    {
        $query = "SELECT * FROM [Users] WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            // Use unique placeholders for each column
            $query .= " AND (FullName LIKE :search1 OR Email LIKE :search2 OR UserName LIKE :search3)";
            $params['search1'] = "%$search%";
            $params['search2'] = "%$search%";
            $params['search3'] = "%$search%";
        }

        if (!empty($role)) {
            $query .= " AND Role = :role";
            $params['role'] = $role;
        }

        // Apply Sorting (Using the correct DB column names)
        switch ($sort) {
            case 'name_asc': 
                $query .= " ORDER BY FullName ASC"; 
                break;
            case 'created_at_asc': 
                $query .= " ORDER BY Created_At ASC"; 
                break;
            default: 
                $query .= " ORDER BY Created_At DESC"; 
                break;
        }

        $stmt = $this->connection->prepare($query);
        $stmt->execute($params); 
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => $this->mapToModel($row), $results);
    }
}
