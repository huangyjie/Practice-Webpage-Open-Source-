<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>提交留言</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+SC:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Noto Sans SC', 'Microsoft YaHei', Arial, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            width: 90%;
            max-width: 600px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            transition: all 0.3s ease;
        }
        .container:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }
        h2 {
            color: #3a7bd5;
            font-size: 28px;
            margin-bottom: 30px;
            text-align: center;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }
        .message {
            background-color: #f8f9fa;
            border-left: 4px solid #3a7bd5;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 0 8px 8px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: left;
            max-height: 100px;
            overflow: hidden;
            position: relative;
            transition: max-height 0.3s ease;
        }
        .message.expanded {
            max-height: 1000px;
        }
        .message-content {
            margin-bottom: 10px;
        }
        .read-more {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            text-align: center;
            margin: 0;
            padding: 30px 0;
            background-image: linear-gradient(to bottom, transparent, #f8f9fa);
        }
        .read-more-btn {
            background: none;
            border: none;
            color: #3a7bd5;
            cursor: pointer;
            font-weight: bold;
        }
        .submit-code {
            color: #3a7bd5;
            font-weight: bold;
            font-size: 18px;
        }
        .btn {
            display: inline-block;
            padding: 12px 25px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            background: linear-gradient(135deg, #3a7bd5, #00d2ff);
            color: #ffffff;
            border: none;
            border-radius: 25px;
            transition: all 0.3s ease;
            text-decoration: none;
            box-shadow: 0 4px 6px rgba(50,50,93,.11), 0 1px 3px rgba(0,0,0,.08);
            margin-top: 20px;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 14px rgba(50,50,93,.1), 0 3px 6px rgba(0,0,0,.08);
        }
        .btn-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .btn-secondary {
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            color: #3a7bd5;
        }
        .btn-secondary:hover {
            background: linear-gradient(135deg, #c3cfe2, #f5f7fa);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>提交留言</h2>
        <?php
        // 检查是否收到POST请求
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // 获取并过滤提交的留言内容
            $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

            // 生成随机的6位提交码
            $submit_code = bin2hex(random_bytes(3));

            // 检查留言是否为空
            if (!empty($message)) {
                // 连接MySQL数据库
                $conn = new mysqli("localhost", "root", "123456", "wy");
                if ($conn->connect_error) {
                    die("连接失败: " . $conn->connect_error);
                }
                $conn->set_charset("utf8mb4");

                // 使用预处理语句防止SQL注入
                $stmt = $conn->prepare("INSERT INTO messages (message, submit_code) VALUES (?, ?)");
                $stmt->bind_param("ss", $message, $submit_code);

                if ($stmt->execute()) {
                    echo "<div class='message' id='message'>";
                    echo "<div class='message-content'>留言提交成功！请将网页分享给对方，对方输入你的提交码即可查询留言。<br><br>";
                    echo "你的提交码是：<span class='submit-code'>" . htmlspecialchars($submit_code, ENT_QUOTES, 'UTF-8') . "</span><br><br>";
                    echo "留言内容：<br>" . nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')) . "</div>";
                    echo "<div class='read-more'><button class='read-more-btn' onclick='toggleMessage()'>阅读更多</button></div>";
                    echo "</div>";
                    echo "<div class='btn-container'>";
                    echo "<a href='view_message.html' class='btn'>查看留言</a>";
                    echo "<a href='index.html' class='btn btn-secondary'>返回提交页</a>";
                    echo "<a href='../index.html' class='btn btn-secondary'>返回首页</a>";
                    echo "</div>";
                } else {
                    echo "<div class='message'>错误: " . $stmt->error . "</div>";
                }

                // 关闭预处理语句和数据库连接
                $stmt->close();
                $conn->close();
            } else {
                echo "<div class='message'>留言不能为空！</div>";
            }
        } else {
            // 如果不是POST请求，返回错误消息
            echo "<div class='message'>无效的请求方法！</div>";
        }
        ?>
    </div>
    <script>
        function toggleMessage() {
            var message = document.getElementById('message');
            message.classList.toggle('expanded');
            var btn = message.querySelector('.read-more-btn');
            if (message.classList.contains('expanded')) {
                btn.textContent = '收起';
            } else {
                btn.textContent = '阅读更多';
            }
        }

        // 检查留言是否需要折叠
        window.onload = function() {
            var message = document.getElementById('message');
            if (message) {
                var content = message.querySelector('.message-content');
                var readMore = message.querySelector('.read-more');
                if (content.offsetHeight > 100) {
                    readMore.style.display = 'block';
                } else {
                    readMore.style.display = 'none';
                }
            }
        }
    </script>
</body>
</html>
