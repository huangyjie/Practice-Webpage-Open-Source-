<?php
header('Content-Type: text/html; charset=utf-8');

require_once 'config/database.php';

try {
    // 创建管理员表
    $pdo->exec("CREATE TABLE IF NOT EXISTS admins (
        admin_id INT PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 添加管理员账户
    $admins = [
        ['root', 'wyy'],
        ['wyy20060612', 'wyy20060612']
    ];

    $stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?) ON DUPLICATE KEY UPDATE password = VALUES(password)");
    foreach ($admins as $admin) {
        $stmt->execute($admin);
    }

    // 检查列是否存在，如果不存在则添加
    try {
        $pdo->query("SELECT is_hidden FROM app_categories LIMIT 1");
    } catch(PDOException $e) {
        $pdo->exec("ALTER TABLE app_categories ADD COLUMN is_hidden TINYINT(1) DEFAULT 0");
    }

    try {
        $pdo->query("SELECT is_hidden FROM apps LIMIT 1");
    } catch(PDOException $e) {
        $pdo->exec("ALTER TABLE apps ADD COLUMN is_hidden TINYINT(1) DEFAULT 0");
    }

    echo "管理员账户和表结构创建成功！";

} catch(PDOException $e) {
    die("错误: " . $e->getMessage());
}
?> 