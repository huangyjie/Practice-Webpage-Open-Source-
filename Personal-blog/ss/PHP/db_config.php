<?php
$servername = "localhost";
$username = "root";
$password = "123456";
$dbname = "tiku";

// 创建连接
$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接是否成功
if ($conn->connect_error) {
    die("连接失败检查数据库服务是否启动: " . $conn->connect_error);
}

// 设置字符集
mysqli_set_charset($conn, "utf8mb4");
?>
