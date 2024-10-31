<?php
session_start();

// 检查是否已登录
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理员后台 - H's blog circle</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --background-color: #ecf0f1;
            --card-color: #ffffff;
            --text-color: #333333;
            --hover-color: #2980b9;
        }

        body {
            font-family: 'Segoe UI', 'Arial', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: var(--background-color);
            color: var(--text-color);
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 30px;
            background-color: var(--card-color);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            color: var(--secondary-color);
            margin-bottom: 30px;
            font-size: 2.5em;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .dashboard-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }

        .dashboard-button {
            background-color: var(--primary-color);
            color: var(--card-color);
            border: none;
            padding: 25px;
            font-size: 1.1em;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            overflow: hidden;
            position: relative;
        }

        .dashboard-button:hover {
            background-color: var(--hover-color);
            transform: translateY(-5px) scale(1.03);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }

        .dashboard-button::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: rgba(255,255,255,0.1);
            transform: rotate(45deg);
            pointer-events: none;
            transition: all 0.6s ease;
            opacity: 0;
        }

        .dashboard-button:hover::before {
            top: -75%;
            left: -75%;
            opacity: 1;
        }

        .dashboard-button.disabled {
            background-color: #bdc3c7;
            cursor: not-allowed;
        }

        .dashboard-button.disabled:hover {
            transform: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .dashboard-button i {
            font-size: 2.5em;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }

        .dashboard-button:hover i {
            transform: scale(1.1);
        }

        .logout-link {
            text-align: center;
            margin-top: 40px;
        }

        .logout-link a {
            color: #e74c3c;
            text-decoration: none;
            font-size: 1.1em;
            transition: all 0.3s ease;
            padding: 10px 20px;
            border-radius: 30px;
            background-color: rgba(231, 76, 60, 0.1);
        }

        .logout-link a:hover {
            color: #c0392b;
            background-color: rgba(231, 76, 60, 0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-user-shield"></i> 欢迎来到管理员后台：<?php echo htmlspecialchars($_SESSION['admin_username']); ?></h1>
        <div class="dashboard-buttons">
            <a href="admin_feedback.php" class="dashboard-button">
                <i class="fas fa-comments"></i>
                <span>用户反馈管理</span>
            </a>
            <a href="post_article.php" class="dashboard-button">
                <i class="fas fa-pen"></i>
                <span>发表文章</span>
            </a>
            <a href="manage_articles.php" class="dashboard-button">
                <i class="fas fa-tasks"></i>
                <span>文章管理</span>
            </a>
            <a href="manage_surveys.php" class="dashboard-button">
                <i class="fas fa-poll"></i>
                <span>问卷管理</span>
            </a>
            
            <a href="manage_messages.php" class="dashboard-button">
                <i class="fas fa-envelope"></i>
                <span>留言板管理</span>
            </a>

            <a href="create_admin_form.php" class="dashboard-button">
                <i class="fas fa-user-plus"></i>
                <span>创建管理员账号</span>
            </a>
            


            <button class="dashboard-button disabled" disabled>
                <i class="fas fa-cog"></i>
                <span>功能待更新</span>
            </button>
            <button class="dashboard-button disabled" disabled>
                <i class="fas fa-cog"></i>
                <span>功能待更新</span>
            </button>
            <button class="dashboard-button disabled" disabled>
                <i class="fas fa-cog"></i>
                <span>功能待更新</span>
            </button>
            <button class="dashboard-button disabled" disabled>
                <i class="fas fa-cog"></i>
                <span>功能待更新</span>
            </button>
            <button class="dashboard-button disabled" disabled>
                <i class="fas fa-cog"></i>
                <span>功能待更新</span>
            </button>
            <button class="dashboard-button disabled" disabled>
                <i class="fas fa-cog"></i>
                <span>功能待更新</span>
            </button>
            <!-- 在 dashboard-buttons div 中添加这个新按钮 -->

        </div>
        <div class="logout-link">
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> 退出登录</a>
        </div>
    </div>
</body>
</html>
