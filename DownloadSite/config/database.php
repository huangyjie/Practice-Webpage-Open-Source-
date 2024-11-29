<?php
$db_config = [
    'host' => 'localhost',
    'user' => 'root',
    'pass' => '123456',
    'name' => 'app_download_center'
];

try {
    $pdo = new PDO(
        "mysql:host={$db_config['host']};dbname={$db_config['name']};charset=utf8mb4",
        $db_config['user'],
        $db_config['pass'],
        array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4")
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die(json_encode(['error' => '数据库连接失败: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE));
}
?> 