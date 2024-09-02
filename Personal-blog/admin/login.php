<?php
session_start(); // 启动会话

// 包含数据库配置
include('db_config.php');

// 检查表单提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input_username = $_POST['username'];
    $input_password = $_POST['password'];

    // 防止SQL注入
    $input_username = $conn->real_escape_string($input_username);
    $input_password = $conn->real_escape_string($input_password);

    // 查询数据库中的管理员账户
    $sql = "SELECT * FROM admins WHERE username = '$input_username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // 验证密码
        if (password_verify($input_password, $row['password'])) {
            // 登录成功，创建会话
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $row['username'];
            header("Location: admin_feedback.php"); // 重定向到管理员后台页面
            exit();
        } else {
            // 密码错误
            header("Location: login.php?error=true"); // 密码错误时跳转回登录页面并显示错误
            exit();
        }
    } else {
        // 用户名不存在
        header("Location: login.php?error=true"); // 用户名错误时跳转回登录页面并显示错误
        exit();
    }
}

// 关闭数据库连接
$conn->close();
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理员登录 - 我的个人博客</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .login-container h2 {
            margin-bottom: 20px;
            text-align: center;
        }
        .login-container form {
            display: flex;
            flex-direction: column;
        }
        .login-container input {
            margin-bottom: 15px;
            padding: 10px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .login-container button {
            padding: 10px;
            background-color: #444;
            color: #fff;
            border: none;
            cursor: pointer;
            font-size: 1em;
            border-radius: 5px;
        }
        .login-container button:hover {
            background-color: #555;
        }
        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>管理员登录</h2>
        <form action="login.php" method="POST">
            <input type="text" name="username" placeholder="用户名" required>
            <input type="password" name="password" placeholder="密码" required>
            <button type="submit">登录</button>
        </form>
        <?php
        if (isset($_GET['error'])) {
            echo '<p class="error">用户名或密码错误</p>';
        }
        ?>
    </div>

</body>
</html>
