<?php
// db_config.php

$servername = "localhost"; // 数据库服务器
$db_username = "root"; // 数据库用户名
$db_password = "123456"; // 数据库密码
$dbname = "wy"; // 数据库名

// 创建连接
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// 检查连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}
?>
