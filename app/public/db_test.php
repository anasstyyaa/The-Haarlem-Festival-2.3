<?php
$server = getenv('DB_SERVER');
$port   = getenv('DB_PORT') ?: '1433';
$db     = getenv('DB_NAME');
$user   = getenv('DB_USER');
$pass   = getenv('DB_PASS');

header('Content-Type: text/plain');

echo "Server=$server\nDB=$db\nUser=$user\n\n";

$dsn = "sqlsrv:Server=$server,$port;Database=$db;Encrypt=yes;TrustServerCertificate=no;LoginTimeout=30;";

try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "✅ CONNECTED OK\n";

    $row = $pdo->query("SELECT DB_NAME() AS dbname")->fetch(PDO::FETCH_ASSOC);
    echo "DB_NAME() = " . ($row['dbname'] ?? 'NULL') . "\n";
} catch (PDOException $e) {
    echo "❌ CONNECT FAILED\n";
    echo $e->getMessage();
}
