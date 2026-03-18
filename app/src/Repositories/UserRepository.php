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
        $row['Created_at'] ?? '', 
        $row['Updated_at'] ?? null,
        $row['ProfilePicture'] ?? null,
        $row['Deleted_at'] ?? null
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

   /// public function updatePasswordHash(int $userId, string $passwordHash): void
//{
  //  $pdo = \App\Config\Config::getPDO(); // or use $this->pdo if you have it

   // $stmt = $pdo->prepare("
       // UPDATE users
        //SET password_hash = :ph
       // WHERE id = :id
    //");

   // $stmt->execute([
       // 'ph' => $passwordHash,
        //'id' => $userId,
    //]);
//}
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


}


