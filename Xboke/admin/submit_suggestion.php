<?php
// 包含数据库配置
include('db_config.php'); // 使用相对路径引入 db_config.php

// 检查请求方法
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 获取表单数据
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $suggestion = $conn->real_escape_string($_POST['suggestion']);

    // 插入数据到数据库
    $sql = "INSERT INTO suggestions (name, email, suggestion) VALUES ('$name', '$email', '$suggestion')";

    if ($conn->query($sql) === TRUE) {
        echo "提交成功";
    } else {
        echo "错误: " . $conn->error;
    }

    // 关闭数据库连接
    $conn->close();
}
?>
