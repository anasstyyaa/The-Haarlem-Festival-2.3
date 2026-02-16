<?php
namespace App\Models;
class UserModel
{
    private int $id;
    private string $email;
    private string $password;
    private string $userName;
    private string $fullName;
    private string $phoneNumber;
    private string $role;
    private string $created_at;
    private ?string $updated_at;
    private ?string $profilePicture;

    public function __construct(
        int $id,
        string $email,
        string $password,
        string $userName,
        string $fullName,
        string $phoneNumber,
        string $role,
        string $created_at,
        ?string $updated_at = null,
        ?string $profilePicture = null
    ) {
        $this->id = $id;
        $this->setEmail($email);
        $this->setPassword($password);
        $this->userName = $userName;
        $this->fullName = $fullName;
        $this->phoneNumber = $phoneNumber;
        $this->role = $role;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
        $this->profilePicture = $profilePicture;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updated_at;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function setUserName(string $userName): void
    {
        $this->userName = $userName;
    }

    public function setFullName(string $fullName): void
    {
        $this->fullName = $fullName;
    }

    public function setPhoneNumber(string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    public function setUpdatedAt(?string $updated_at): void
    {
        $this->updated_at = $updated_at;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }
}
