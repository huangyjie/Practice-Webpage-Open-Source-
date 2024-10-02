<?php
session_start();

// 检查是否已登录
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// 包含数据库配置
include('db_config.php'); // 修改为相对路径

// 从数据库获取用户反馈数据
$sql = "SELECT id, name, email, suggestion, submission_date FROM suggestions ORDER BY submission_date DESC";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理员 - 查看用户反馈</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        header {
            background-color: #333;
            color: #fff;
            padding: 10px 0;
            text-align: center;
        }
        .container {
            flex: 1;
            max-width: 1400px; /* 增加最大宽度 */
            margin: 20px auto;
            padding: 30px; /* 增加内边距 */
            background-color: #fff;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            font-size: 1.1em; /* 增加字体大小 */
            position: relative; /* 使返回链接定位于容器内部 */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 15px; /* 增加单元格内边距 */
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .back-link {
            position: absolute;
            bottom: 20px; /* 放置在容器底部 */
            right: 30px; /* 右侧边距 */
            text-decoration: none;
            color: #007BFF;
            font-weight: bold;
            font-size: 1.1em;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        footer {
            text-align: center;
            padding: 10px 0;
            background-color: #333;
            color: #fff;
        }
    </style>
</head>
<body>

    <header>
        <h1>用户反馈管理</h1>
    </header>

    <div class="container">
        <h2>用户反馈列表</h2>
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>姓名</th>
                        <th>邮箱</th>
                        <th>建议</th>
                        <th>提交时间</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row["id"]; ?></td>
                        <td><?php echo $row["name"]; ?></td>
                        <td><?php echo $row["email"]; ?></td>
                        <td><?php echo $row["suggestion"]; ?></td>
                        <td><?php echo $row["submission_date"]; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>没有用户反馈信息。</p>
        <?php endif; ?>
        
        <a href="../index.html" class="back-link">返回首页</a>
        
        <?php
        // 关闭数据库连接
        $conn->close();
        ?>
    </div>

    <footer>
        <p>&copy; 2024 huang yu jie. 保留所有权利。</p>
    </footer>

</body>
</html>
