<?php
session_start();

// 检查是否已登录
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// 连接数据库
$conn = new mysqli("localhost", "root", "123456", "wy");

// 检查连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

// 处理删除请求
if (isset($_POST['delete'])) {
    $id = $_POST['delete'];
    $stmt = $conn->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// 处理更新请求
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $message = $_POST['message'];
    $stmt = $conn->prepare("UPDATE messages SET message = ? WHERE id = ?");
    $stmt->bind_param("si", $message, $id);
    $stmt->execute();
    $stmt->close();
}

// 获取所有留言
$result = $conn->query("SELECT * FROM messages ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>留言板管 - H's blog circle</title>
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: var(--primary-color);
            color: white;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        .btn {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 14px;
        }

        .btn-edit {
            background-color: #2ecc71;
            color: white;
        }

        .btn-delete {
            background-color: #e74c3c;
            color: white;
        }

        .btn:hover {
            opacity: 0.8;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: var(--primary-color);
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .message-textarea {
            width: 100%;
            min-height: 60px;
            resize: vertical;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
            font-size: 14px;
            line-height: 1.4;
        }

        .btn-container {
            display: flex;
            gap: 10px;
        }

        .btn-edit {
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-envelope"></i> 留言板管理</h1>
        <table>
            <tr>
                <th>ID</th>
                <th>留言内容</th>
                <th>提交码</th>
                <th>操作</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td>
                    <textarea name="message" class="message-textarea" id="message-<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['message']); ?></textarea>
                </td>
                <td><?php echo $row['submit_code']; ?></td>
                <td>
                    <div class="btn-container">
                        <button onclick="updateMessage(<?php echo $row['id']; ?>)" class="btn btn-edit">更新</button>
                        <form method="post" style="display: inline;">
                            <button type="submit" name="delete" value="<?php echo $row['id']; ?>" class="btn btn-delete" onclick="return confirm('确定要删除这条留言吗？');">删除</button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
        <a href="admin_dashboard.php" class="back-link">返回管理员后台</a>
    </div>

    <script>
    function updateMessage(id) {
        var message = document.getElementById('message-' + id).value;
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "update_message.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                alert("留言已更新");
            }
        }
        xhr.send("id=" + id + "&message=" + encodeURIComponent(message));
    }
    </script>
</body>
</html>

<?php
$conn->close();
?>
