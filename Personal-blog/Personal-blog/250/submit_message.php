<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>提交留言</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f8f9fa; /* 设置页面背景颜色 */
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 400px; /* 设置容器最大宽度 */
            margin: 100px auto; /* 上下居中 */
            padding: 20px;
            background-color: #fff; /* 设置容器背景颜色 */
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* 添加阴影效果 */
        }
        h2 {
            margin-bottom: 20px;
            color: #007bff; /* 设置标题颜色 */
        }
        .message {
            text-align: left; /* 设置留言内容左对齐 */
            margin-bottom: 20px; /* 设置留言内容与下方元素的间距 */
        }
        .submit-code {
            color: #007bff; /* 设置提交码颜色 */
            font-weight: bold; /* 设置提交码加粗 */
        }
        .link {
            display: inline-block; /* 设置链接为块级元素 */
            margin-top: 20px; /* 设置链接与上方元素的间距 */
            color: #007bff; /* 设置链接颜色 */
            text-decoration: none; /* 去除链接下划线 */
            transition: color 0.3s; /* 添加链接颜色过渡效果 */
        }
        .link:hover {
            color: #0056b3; /* 设置鼠标悬停时的链接颜色 */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>提交留言</h2>
        <?php
        // 检查是否收到POST请求
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // 获取提交的留言内容
            $message = $_POST['message'];

            // 生成随机的6位提交码
            $submit_code = generateSubmitCode();

            // 检查留言是否为空
            if (!empty($message)) {
                // 连接MySQL数据库
                $conn = new mysqli("localhost", "root", "123456", "wy");

                // 检查连接是否成功
                if ($conn->connect_error) {
                    die("连接失败: " . $conn->connect_error);
                }

                // 准备SQL语句并执行插入操作
                $sql = "INSERT INTO messages (message, submit_code) VALUES ('$message', '$submit_code')";
                if ($conn->query($sql) === TRUE) {
                    echo "<div class='message'>留言提交成功！请将网页分享给对方对方输入你的提交码即可进行查询留言</div>";
                    echo "<div class='message'>你的提交码是：<span class='submit-code'>$submit_code</span></div>";
                    echo "<a href='view_message.html' class='link'>查看留言</a>";
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }

                // 关闭数据库连接
                $conn->close();
            } else {
                echo "<div class='message'>留言不能为空！</div>";
            }
        } else {
            // 如果不是POST请求，返回错误消息
            echo "<div class='message'>无效的请求方法！</div>";
        }

        // 生成随机的6位提交码
        function generateSubmitCode() {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $submit_code = '';
            for ($i = 0; $i < 6; $i++) {
                $submit_code .= $characters[rand(0, strlen($characters) - 1)];
            }
            return $submit_code;
        }
        ?>
    </div>
</body>
</html>
