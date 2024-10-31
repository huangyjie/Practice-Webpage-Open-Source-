<?php
session_start();

// 检查是否已登录
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $verification_password = "Wyy20060612";
    $input_password = $_POST['verification_password'];

    if ($input_password === $verification_password) {
        // 验证密码正确，处理创建管理员账号
        $new_username = $_POST['new_username'];
        $new_password = $_POST['new_password'];

        // 这里添加创建管理员账号的逻辑
        // 注意：实际应用中，应该进行更多的安全检查和验证

        include('db_config.php');

        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO admins (username, password) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $new_username, $hashed_password);

        if ($stmt->execute()) {
            $success_message = "新管理员账号创建成功！";
        } else {
            $error_message = "创建账号时出错：" . $conn->error;
        }

        $stmt->close();
        $conn->close();
    } else {
        $error_message = "验证密码错误！";
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>创建管理员账号 - H's blog circle</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --background-color: #ecf0f1;
            --card-color: #ffffff;
            --text-color: #333333;
            --error-color: #e74c3c;
            --success-color: #2ecc71;
        }

        body {
            font-family: 'Segoe UI', 'Arial', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: var(--background-color);
            color: var(--text-color);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: var(--card-color);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }

        h1 {
            text-align: center;
            color: var(--secondary-color);
            margin-bottom: 30px;
            font-size: 2em;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus, input[type="password"]:focus {
            border-color: var(--primary-color);
            outline: none;
        }

        input[type="submit"] {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #2980b9;
        }

        .error, .success {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }

        .error {
            background-color: rgba(231, 76, 60, 0.1);
            color: var(--error-color);
        }

        .success {
            background-color: rgba(46, 204, 113, 0.1);
            color: var(--success-color);
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 1em;
            transition: color 0.3s ease;
        }

        .back-link a:hover {
            color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-user-plus"></i> 创建管理员账号</h1>
        <?php
        if ($error_message) {
            echo "<p class='error'>$error_message</p>";
        }
        if ($success_message) {
            echo "<p class='success'>$success_message</p>";
        }
        ?>
        <form method="post" action="">
            <input type="password" name="verification_password" placeholder="管理员验证密码" required>
            <input type="text" name="new_username" placeholder="新管理员用户名" required>
            <input type="password" name="new_password" placeholder="新管理员密码" required>
            <input type="submit" value="创建账号">
        </form>
        <div class="back-link">
            <a href="admin_dashboard.php"><i class="fas fa-arrow-left"></i> 返回管理员后台</a>
        </div>
    </div>
</body>
</html>
