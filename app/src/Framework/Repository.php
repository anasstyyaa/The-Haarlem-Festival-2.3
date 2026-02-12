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
        $server = getenv('DB_SERVER');
        $port   = getenv('DB_PORT') ?: '1433';
        $db     = getenv('DB_NAME');
        $user   = getenv('DB_USER');
        $pass   = getenv('DB_PASS');

        $dsn = "sqlsrv:Server=$server,$port;Database=$db;Encrypt=yes;TrustServerCertificate=no;LoginTimeout=30;";

        $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }
}
