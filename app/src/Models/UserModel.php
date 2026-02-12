<?php
namespace App\Models;

class UserModel
{
    public int $id;
    public string $name;
    public string $email;
    public string $password_hash;
    public string $role;
    public string $created_at;
}
