<?php
// 包含数据库配置
include('db_config.php');

// 管理员账户信息
$admin_username = "2024131831"; // 管理员用户名
$admin_password = password_hash("Wyy20060612", PASSWORD_DEFAULT); // 管理员密码哈希

// 插入管理员账户
$sql = "INSERT INTO admins (username, password) VALUES ('$admin_username', '$admin_password')";

if ($conn->query($sql) === TRUE) {
    echo "管理员账户创建成功";
} else {
    echo "错误: " . $conn->error;
}

// 关闭数据库连接
$conn->close();
?>
