<?php
$local_socket = '/tmp/my-mysql.sock';
$is_local = file_exists($local_socket)
         || php_sapi_name() === 'cli'
         || ($_SERVER['SERVER_NAME'] ?? '') === 'localhost'
         || in_array($_SERVER['SERVER_ADDR'] ?? '', ['127.0.0.1', '::1']);

if ($is_local) {
    $pdo_dsn  = "mysql:unix_socket={$local_socket};dbname=nsbm_market";
    $pdo_user = 'root';
    $pdo_pass = '';
} else {
    $pdo_dsn  = "mysql:host=sql111.infinityfree.com;dbname=if0_42212623_nsbm_market";
    $pdo_user = 'if0_42212623';
    $pdo_pass = 'a3qpzusls8';
}

try {
    $pdo = new PDO($pdo_dsn, $pdo_user, $pdo_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
