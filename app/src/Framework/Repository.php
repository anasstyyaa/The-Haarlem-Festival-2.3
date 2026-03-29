<?php

namespace App\Framework;


use PDO;
use PDOException;

class Repository
{
    protected PDO $connection;

    public function __construct()
    {
        $server = getenv('DB_SERVER');
        $port   = getenv('DB_PORT') ?: '1433';
        $db     = getenv('DB_NAME');
        $user   = getenv('DB_USER');
        $pass   = getenv('DB_PASS');

        $dsn = "sqlsrv:Server=$server,$port;Database=$db;Encrypt=yes;TrustServerCertificate=no;LoginTimeout=30;";

        $this->connection = new PDO($dsn, $user, $pass, [

        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }

    public function getConnection():PDO 
    {
        return $this->connection;
    }

    // to handle transactions for a restaurant sessions checks and updates
    public function beginTransaction(): bool {
        return $this->connection->beginTransaction();
    }

    public function commit(): bool {
        return $this->connection->commit();
    }

    public function rollBack(): bool {
        return $this->connection->rollBack();
    }
}
