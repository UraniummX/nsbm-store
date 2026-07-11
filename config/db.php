<?php

// Backend Logic

$env_file = __DIR__ . '/../.env';
$env = [];
if (file_exists($env_file)) {
    $env = parse_ini_file($env_file);
}

$local_socket = $env['DB_LOCAL_SOCKET'] ?? '/opt/lampp/var/mysql/mysql.sock';
$is_local = file_exists($local_socket)
         || php_sapi_name() === 'cli'
         || ($_SERVER['SERVER_NAME'] ?? '') === 'localhost'
         || in_array($_SERVER['SERVER_ADDR'] ?? '', ['127.0.0.1', '::1']);

if ($is_local) {
    $dbname = $env['DB_LOCAL_NAME'] ?? 'nsbm_market';
    $pdo_dsn  = getenv('DB_DSN_LOCAL') ?: "mysql:unix_socket={$local_socket};dbname={$dbname}";
    $pdo_user = $env['DB_LOCAL_USER'] ?? getenv('DB_USER_LOCAL') ?: 'root';
    $pdo_pass = $env['DB_LOCAL_PASS'] ?? getenv('DB_PASS_LOCAL') ?: '';
} else {
    $host = $env['DB_REMOTE_HOST'] ?? 'localhost';
    $dbname = $env['DB_REMOTE_NAME'] ?? '';
    $pdo_dsn  = getenv('DB_DSN') ?: "mysql:host={$host};dbname={$dbname}";
    $pdo_user = $env['DB_REMOTE_USER'] ?? getenv('DB_USER') ?: '';
    $pdo_pass = $env['DB_REMOTE_PASS'] ?? getenv('DB_PASS') ?: '';
}

try {
    $pdo = new PDO($pdo_dsn, $pdo_user, $pdo_pass ?: '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    
    error_log("Connection failed: " . $e->getMessage());
    die("Database connection failed. Please check server logs.");
}
?>
