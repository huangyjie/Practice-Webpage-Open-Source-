<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>查看留言</title>
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
        }
        .error {
            color: #dc3545;
            font-weight: bold;
            margin-top: 20px;
            padding: 10px;
            background-color: #fff5f5;
            border-radius: 8px;
            border: 1px solid #dc3545;
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
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 14px rgba(50,50,93,.1), 0 3px 6px rgba(0,0,0,.08);
        }
        .btn-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .btn-home {
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            color: #3a7bd5;
        }

        .btn-home:hover {
            background: linear-gradient(135deg, #c3cfe2, #f5f7fa);
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
                $conn = new mysqli("localhost", "root", "123456", "wy");

                // 检查连接是否成功
                if ($conn->connect_error) {
                    die("<div class='error'>连接失败: " . $conn->connect_error . "</div>");
                }

                // 准备SQL语句并执行查询操作
                $sql = "SELECT message FROM messages WHERE submit_code=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $submit_code);
                $stmt->execute();
                $result = $stmt->get_result();

                // 输出查询结果
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<div class='message'>" . nl2br(htmlspecialchars($row["message"])) . "</div>";
                    }
                } else {
                    echo "<div class='error'>留言不存在！</div>";
                }

                // 关闭数据库连接
                $stmt->close();
                $conn->close();
            } else {
                echo "<div class='error'>提交码不能为空！</div>";
            }
        } else {
            // 如果不是GET请求，返回错误消息
            echo "<div class='error'>无效的请求方法！</div>";
        }
        ?>
        <div class="btn-container">
            <a href="view_message.html" class="btn">返回查询页面</a>
            <a href="../index.html" class="btn btn-home">返回首页</a>
        </div>
    </div>
</body>
</html>
