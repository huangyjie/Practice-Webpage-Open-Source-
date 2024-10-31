<?php
session_start();

// 检查是否已登录
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include('db_config.php');

// 处理删除操作
if (isset($_POST['delete'])) {
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    $delete_sql = "DELETE FROM blog_posts WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// 处理更新操作
if (isset($_POST['update'])) {
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING);
    $update_sql = "UPDATE blog_posts SET title = ?, content = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssi", $title, $content, $id);
    $stmt->execute();
    $stmt->close();
}

// 获取所有文章
$sql = "SELECT * FROM blog_posts ORDER BY date DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>文章管理 - H's blog circle</title>
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
            max-width: 1200px;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        th {
            background-color: #34495e;
            color: white;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .btn {
            display: inline-block;
            padding: 8px 15px;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
            font-size: 14px;
        }
        .btn-delete {
            background-color: #e74c3c;
        }
        .btn-delete:hover {
            background-color: #c0392b;
        }
        .btn-edit {
            background-color: #3498db;
        }
        .btn-edit:hover {
            background-color: #2980b9;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 30px;
            border: 1px solid #888;
            width: 60%;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            transition: color 0.3s ease;
        }
        .close:hover,
        .close:focus {
            color: #2c3e50;
            text-decoration: none;
            cursor: pointer;
        }
        textarea, input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        textarea {
            height: 200px;
            resize: vertical;
        }
        .btn-back {
            display: inline-block;
            padding: 10px 20px;
            background-color: #2c3e50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            margin-top: 20px;
            transition: background-color 0.3s;
        }
        .btn-back:hover {
            background-color: #34495e;
        }
        .button-container {
            text-align: center;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-newspaper"></i> 文章管理</h1>
        <table>
            <thead>
                <tr>
                    <th>标题</th>
                    <th>作者</th>
                    <th>日期</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($row['author'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($row['date'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>
                        <button class="btn btn-edit" onclick="openEditModal(<?php echo $row['id']; ?>, '<?php echo addslashes($row['title']); ?>', '<?php echo addslashes($row['content']); ?>')"><i class="fas fa-edit"></i> 编辑</button>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('确定要删除这篇文章吗？');">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="delete" class="btn btn-delete"><i class="fas fa-trash-alt"></i> 删除</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <div class="button-container">
            <a href="admin_dashboard.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> 返回管理员后台
            </a>
        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2><i class="fas fa-edit"></i> 编辑文章</h2>
            <form id="editForm" method="POST">
                <input type="hidden" id="editId" name="id">
                <label for="editTitle">标题：</label>
                <input type="text" id="editTitle" name="title" required>
                <label for="editContent">内容：</label>
                <textarea id="editContent" name="content" required></textarea>
                <button type="submit" name="update" class="btn btn-edit"><i class="fas fa-save"></i> 更新文章</button>
            </form>
        </div>
    </div>

    <script>
        var modal = document.getElementById("editModal");
        var span = document.getElementsByClassName("close")[0];

        function openEditModal(id, title, content) {
            document.getElementById("editId").value = id;
            document.getElementById("editTitle").value = title;
            document.getElementById("editContent").value = content;
            modal.style.display = "block";
        }

        span.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
