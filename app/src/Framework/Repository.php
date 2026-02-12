<?php

namespace App\Framework;

use App\Config;
use PDO;
use PDOException;

class Repository
{
    protected PDO $connection;

    public function __construct()
    {
        $host = Config::DB_SERVER_NAME;
        $db   = Config::DB_NAME;
        $user = Config::DB_USERNAME;
        $pass = Config::DB_PASSWORD;

        $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

        try {
            $this->connection = new PDO($dsn, $user, $pass);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
}
