<?php
session_start(); // 启动会话

// 包含数据库配置
include('db_config.php');

// 获取客户端IP地址
$ip_address = $_SERVER['REMOTE_ADDR'];

// 检查IP是否被锁定，同时删除过期记录
$stmt = $conn->prepare("DELETE FROM login_attempts WHERE attempt_time < DATE_SUB(NOW(), INTERVAL 10 MINUTE)");
$stmt->execute();

$stmt = $conn->prepare("SELECT * FROM login_attempts WHERE ip_address = ? AND is_locked = 1 AND attempt_time > DATE_SUB(NOW(), INTERVAL 10 MINUTE)");
$stmt->bind_param("s", $ip_address);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $lock_time = strtotime($row['attempt_time']) + 600 - time(); // 计算剩余锁定时间（秒）
    $minutes = ceil($lock_time / 60);
    $lock_message = "您的IP已被锁定，请在{$minutes}分钟后再试或联系管理员。";
    include('locked.php'); // 包含锁定页面模板
    exit();
} else {
    // 如果锁定时间已过，解除锁定
    $stmt = $conn->prepare("UPDATE login_attempts SET is_locked = 0, attempts = 0 WHERE ip_address = ? AND is_locked = 1");
    $stmt->bind_param("s", $ip_address);
    $stmt->execute();
}

// 检查表单提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input_username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $input_password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    // 防止SQL注入
    $input_username = $conn->real_escape_string($input_username);
    $input_password = $conn->real_escape_string($input_password);

    // 查询数据库中的管理员账户
    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->bind_param("s", $input_username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // 验证密码
        if (password_verify($input_password, $row['password'])) {
            // 登录成功，重置登录尝试次数
            $stmt = $conn->prepare("DELETE FROM login_attempts WHERE ip_address = ?");
            $stmt->bind_param("s", $ip_address);
            $stmt->execute();

            // 创建会话
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $row['username'];
            header("Location: admin_dashboard.php"); // 重定向到新的管理员后台主页
            exit();
        } else {
            // 密码错误，增加登录尝试次数
            $stmt = $conn->prepare("INSERT INTO login_attempts (ip_address, attempt_time) VALUES (?, NOW()) ON DUPLICATE KEY UPDATE attempts = attempts + 1, attempt_time = NOW()");
            $stmt->bind_param("s", $ip_address);
            $stmt->execute();

            // 检查登录尝试次数
            $stmt = $conn->prepare("SELECT attempts FROM login_attempts WHERE ip_address = ?");
            $stmt->bind_param("s", $ip_address);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if ($row['attempts'] >= 3) {
                // 锁定IP 10分钟
                $stmt = $conn->prepare("UPDATE login_attempts SET is_locked = 1 WHERE ip_address = ?");
                $stmt->bind_param("s", $ip_address);
                $stmt->execute();
                die("您的IP已被锁定10分钟，请稍后再试或联系管理员。");
            }

            header("Location: login.php?error=true"); // 密码错误时跳转回登录页面并显示错误
            exit();
        }
    } else {
        // 用户名不存在，增加登录尝试次数
        $stmt = $conn->prepare("INSERT INTO login_attempts (ip_address, attempt_time) VALUES (?, NOW()) ON DUPLICATE KEY UPDATE attempts = attempts + 1, attempt_time = NOW()");
        $stmt->bind_param("s", $ip_address);
        $stmt->execute();

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
    <title>管理员登录 - H's blog circle</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* 初始化样式 */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* 设置主体样式 */
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background: url('https://picsum.photos/1920/1080') no-repeat center center/cover;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }

        .login-container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 40px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            border-radius: 20px;
            width: 350px;
            transition: all 0.3s ease;
            position: relative;
            z-index: 2;
        }

        .login-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
        }

        .login-container h2 {
            margin-bottom: 30px;
            text-align: center;
            color: #35424a;
            font-weight: 600;
            font-size: 28px;
        }

        .login-container form {
            display: flex;
            flex-direction: column;
        }

        .input-group {
            position: relative;
            margin-bottom: 25px;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #35424a;
            font-size: 18px;
        }

        .login-container input {
            padding: 15px 15px 15px 45px;
            font-size: 16px;
            border: none;
            border-radius: 50px;
            background-color: #f4f4f4;
            width: 100%;
            transition: all 0.3s ease;
        }

        .login-container input:focus {
            outline: none;
            box-shadow: 0 0 0 2px #e8491d;
        }

        .login-container button {
            padding: 15px;
            background: #e8491d;
            color: #ffffff;
            border: none;
            cursor: pointer;
            font-size: 18px;
            border-radius: 50px;
            transition: all 0.3s ease;
            width: 100%;
            font-weight: 600;
        }

        .login-container button:hover {
            background: #ff6a3c;
        }

        .error {
            color: #e74c3c;
            text-align: center;
            margin-bottom: 20px;
            font-size: 14px;
            background-color: #fceaea;
            padding: 10px;
            border-radius: 50px;
        }

        .back-link {
            text-align: center;
            margin-top: 25px;
        }

        .back-link a {
            color: #35424a;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .back-link a:hover {
            color: #e8491d;
        }

        /* 添加动画效果 */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .fade-in {
            animation: fadeIn 1s ease-in;
        }
    </style>
</head>
<body>
    <div class="login-container fade-in">
        <h2>管理员登录</h2>
        <?php
        if (isset($_GET['error'])) {
            echo '<p class="error"><i class="fas fa-exclamation-circle"></i> 用户名或密码错误</p>';
        }
        ?>
        <form action="login.php" method="POST">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="username" placeholder="用户名" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="密码" required>
            </div>
            <button type="submit">登录</button>
        </form>
        <div class="back-link">
            <a href="../index.php"><i class="fas fa-arrow-left"></i> 返回主页</a>
        </div>
    </div>
</body>
</html>
