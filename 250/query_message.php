<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>查看留言</title>
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
        .error {
            color: #dc3545; /* 设置错误消息颜色 */
            font-weight: bold; /* 设置错误消息加粗 */
            margin-top: 20px; /* 设置错误消息与上方元素的间距 */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>查看留言</h2>
        <?php
        // 检查是否收到GET请求
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            // 获取查询的提交码
            $submit_code = $_GET['code'];

            // 检查提交码是否为空
            if (!empty($submit_code)) {
                // 连接MySQL数据库
                $conn = new mysqli("localhost", "root", "123456", "message_board");

                // 检查连接是否成功
                if ($conn->connect_error) {
                    die("连接失败: " . $conn->connect_error);
                }

                // 准备SQL语句并执行查询操作
                $sql = "SELECT message FROM messages WHERE submit_code='$submit_code'";
                $result = $conn->query($sql);

                // 输出查询结果
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<div class='message'>留言内容：<br>" . $row["message"] . "</div>";
                    }
                } else {
                    echo "<div class='error'>留言不存在！</div>";
                }

                // 关闭数据库连接
                $conn->close();
            } else {
                echo "<div class='error'>提交码不能为空！</div>";
            }
        } else {
            // 如果不是GET请求，返回错误消息
            echo "<div class='error'>无效的请求方法！</div>";
        }
        ?>
    </div>
</body>
</html>
