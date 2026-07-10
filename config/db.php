<?php
$local_socket = '/tmp/my-mysql.sock';
$is_local = file_exists($local_socket)
         || php_sapi_name() === 'cli'
         || ($_SERVER['SERVER_NAME'] ?? '') === 'localhost'
         || in_array($_SERVER['SERVER_ADDR'] ?? '', ['127.0.0.1', '::1']);

if ($is_local) {
    $pdo_dsn  = getenv('DB_DSN_LOCAL') ?: "mysql:unix_socket={$local_socket};dbname=nsbm_market";
    $pdo_user = getenv('DB_USER_LOCAL') ?: 'root';
    $pdo_pass = getenv('DB_PASS_LOCAL') ?: '';
} else {
    $pdo_dsn  = getenv('DB_DSN');
    $pdo_user = getenv('DB_USER');
    $pdo_pass = getenv('DB_PASS');
}

if (!$pdo_dsn || !$pdo_user) {
    die('Database configuration error: missing required environment variables.');
}

try {
    $pdo = new PDO($pdo_dsn, $pdo_user, $pdo_pass ?: '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
