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

    public function getByUsername(string $username): ?UserModel
    {
        $stmt = $this->connection->prepare(
            "SELECT id, email, password, userName, fullName, phoneNumber, role, created_at, updated_at, profilePicture, deleted_at 
             FROM Users WHERE userName = :username");
        $stmt->execute(['username' => $username]);
        
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }
        return new UserModel(
            (int)($data['id'] ?? 0),
            (string)($data['email'] ?? ''),
            (string)($data['password'] ?? ''),
            (string)($data['userName'] ?? ''),
            (string)($data['fullName'] ?? ''),
            (string)($data['phoneNumber'] ?? ''),
            (string)($data['role'] ?? 'User'),
            (string)($data['created_at'] ?? date('Y-m-d H:i:s')),
            $data['updated_at'] ?? null,
            $data['profilePicture'] ?? null,
            $data['deleted_at'] ?? null
        );
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
        $stmt = $this->connection->prepare("
            SELECT *
            FROM Users
            WHERE Email = :email
            AND Deleted_At IS NULL
        ");

        $stmt->execute([
            'email' => $email
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return [
            'Id' => $row['Id'],
            'Email' => $row['Email'],
            'Password' => $row['Password'],
            'UserName' => $row['UserName'],
            'Role' => $row['Role'],
        ];
    }

    public function adminGetAll(): array
    {
        $stmt = $this->connection->query("SELECT * FROM users");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => $this->mapToModel($row), $results);
    }


    public function updatePassword(int $userId, string $hashedPassword): bool
    {
        $stmt = $this->connection->prepare("
            UPDATE users
            SET password = :password,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
            AND deleted_at IS NULL
        ");

        return $stmt->execute([
            'password' => $hashedPassword,
            'id' => $userId
        ]);
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

    public function getAllFiltered(string $search = '', string $role = '', string $sort = '', int $page = 1, int $limit = 10): array
    {
        $query = "SELECT * FROM [Users] WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $query .= " AND (FullName LIKE :search1 OR Email LIKE :search2 OR UserName LIKE :search3)";
            $searchParam = "%$search%";
            $params['search1'] = $searchParam;
            $params['search2'] = $searchParam;
            $params['search3'] = $searchParam;
        }

        if (!empty($role)) {
            $query .= " AND Role = :role";
            $params['role'] = $role;
        }

        // required for OFFSET in SQL Server as well 
        switch ($sort) {
            case 'name_asc': $query .= " ORDER BY FullName ASC"; break;
            case 'created_at_asc': $query .= " ORDER BY Created_At ASC"; break;
            default: $query .= " ORDER BY Created_At DESC"; break;
        }


        $offset = ($page - 1) * $limit;
        $query .= " OFFSET :offset ROWS FETCH NEXT :limit ROWS ONLY";

        $stmt = $this->connection->prepare($query);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }

        // PDO::PARAM_INT is required for OFFSET/FETCH in some SQL Server drivers
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);

        $stmt->execute(); 
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => $this->mapToModel($row), $results);
    }

    public function countAllFiltered(string $search = '', string $role = ''): int
    {
        $query = "SELECT COUNT(*) FROM [Users] WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $query .= " AND (FullName LIKE :search1 OR Email LIKE :search2 OR UserName LIKE :search3)";
            $searchParam = "%$search%";
            $params['search1'] = $searchParam;
            $params['search2'] = $searchParam;
            $params['search3'] = $searchParam;
        }

        if (!empty($role)) {
            $query .= " AND Role = :role";
            $params['role'] = $role;
        }

        $stmt = $this->connection->prepare($query);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }
}
