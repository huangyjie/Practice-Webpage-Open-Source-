<?php
session_start();

// 检查是否已登录
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// 包含数据库配置
include('db_config.php');

// 处理删除操作
if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $delete_sql = "DELETE FROM suggestions WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            color: #333;
        }
        header {
            background-color: #35424a;
            color: #fff;
            padding: 20px 0;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .container {
            flex: 1;
            max-width: 100%;
            padding: 30px;
            background-color: #fff;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border-radius: 8px;
            position: relative;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            box-shadow: 0 2px 3px rgba(0,0,0,0.1);
        }
        table, th, td {
            border: 1px solid #e0e0e0;
        }
        th, td {
            padding: 15px;
            text-align: left;
        }
        th {
            background-color: #f8f8f8;
            font-weight: bold;
            color: #35424a;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .btn-back {
            display: inline-block;
            padding: 10px 20px;
            background-color: #35424a;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            margin-top: 20px;
            transition: background-color 0.3s;
            text-align: center;
        }
        
        .btn-back:hover {
            background-color: #e8491d;
        }
        
        .button-container {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        footer {
            text-align: center;
            padding: 15px 0;
            background-color: #35424a;
            color: #fff;
            font-size: 0.9em;
        }
        .delete-btn {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .delete-btn:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>

    <header>
        <h1><i class="fas fa-comments"></i> 用户反馈管理</h1>
    </header>

    <div class="container">
        <h2><i class="fas fa-list"></i> 用户反馈列表</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>姓名</th>
                    <th>邮箱</th>
                    <th>建议</th>
                    <th>提交时间</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row["id"]; ?></td>
                        <td><?php echo htmlspecialchars($row["name"]); ?></td>
                        <td><?php echo htmlspecialchars($row["email"]); ?></td>
                        <td><?php echo htmlspecialchars($row["suggestion"]); ?></td>
                        <td><?php echo $row["submission_date"]; ?></td>
                        <td>
                            <form method="POST" onsubmit="return confirm('确定要删除这条反馈吗？');">
                                <input type="hidden" name="id" value="<?php echo $row["id"]; ?>">
                                <button type="submit" name="delete" class="delete-btn">
                                    <i class="fas fa-trash-alt"></i> 删除
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">暂无用户反馈信息</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <div class="button-container">
            <a href="admin_dashboard.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> 返回管理员后台
            </a>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 huang yu jie. 保留所有权利。</p>
    </footer>

</body>
</html>

<?php
// 关闭数据库连接
$conn->close();
?>
