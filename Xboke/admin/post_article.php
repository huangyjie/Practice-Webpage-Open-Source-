<?php
session_start();

// 检查是否已登录
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include('db_config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING);
    $admin_username = isset($_SESSION['admin_username']) ? htmlspecialchars($_SESSION['admin_username']) : '';
    $date = date("Y-m-d H:i:s");

    $sql = "INSERT INTO blog_posts (title, content, author, date) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $title, $content, $admin_username, $date);

    if ($stmt->execute()) {
        $message = "文章发表成功！";
    } else {
        $message = "发生错误，请重试。";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>发表文章 - H's blog circle</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
            font-size: 2.5em;
        }
        form {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
        }
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
            color: #34495e;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #bdc3c7;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        input[type="text"]:focus, textarea:focus {
            border-color: #3498db;
            outline: none;
        }
        textarea {
            height: 300px;
            resize: vertical;
        }
        button {
            background-color: #3498db;
            color: #fff;
            border: none;
            padding: 12px 24px;
            cursor: pointer;
            border-radius: 4px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #2980b9;
        }
        .message {
            text-align: center;
            margin-bottom: 20px;
            padding: 12px;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            color: #155724;
        }
        .btn-back {
            display: inline-block;
            padding: 12px 24px;
            background-color: #34495e;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
            margin-top: 20px;
            transition: background-color 0.3s ease;
            text-align: center;
        }
        
        .btn-back:hover {
            background-color: #2c3e50;
        }
        
        .button-container {
            text-align: center;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-pen-fancy"></i> 发表新文章</h1>
        <?php if (isset($message)) echo "<p class='message'>$message</p>"; ?>
        <form action="post_article.php" method="POST">
            <label for="title"><i class="fas fa-heading"></i> 标题：</label>
            <input type="text" id="title" name="title" required placeholder="请输入文章标题">
            
            <label for="content"><i class="fas fa-paragraph"></i> 内容：</label>
            <textarea id="content" name="content" required placeholder="请输入文章内容"></textarea>
            
            <button type="submit"><i class="fas fa-paper-plane"></i> 发表文章</button>
        </form>
        <div class="button-container">
            <a href="admin_dashboard.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> 返回管理员后台
            </a>
        </div>
    </div>
</body>
</html>
