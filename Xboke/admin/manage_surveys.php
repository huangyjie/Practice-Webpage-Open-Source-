<?php
header('Content-Type: text/html; charset=UTF-8');
mb_internal_encoding('UTF-8');

session_start();

// 检查是否已登录
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include('db_config.php');
$conn->set_charset("utf8mb4");

// 处理删除操作
if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $delete_sql = "DELETE FROM survey_responses WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// 处理导出操作
if (isset($_POST['export'])) {
    $export_sql = "SELECT id, name, email, phone, qq, age, gender, country, address, school, programming_languages, comments, terms FROM survey_responses ORDER BY id DESC";
    $result = $conn->query($export_sql);

    // 设置文件头，使浏览器下载文件
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=survey_responses.csv');

    // 创建一个文件指针连接到 PHP 输出流
    $output = fopen('php://output', 'w');

    // 添加 BOM 以正确显示 UTF-8 编码的 CSV 文件
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    // 输出列标题
    fputcsv($output, array('ID', '姓名', '电子邮件', '手机号', 'QQ号', '年龄', '性别', '国家', '家庭住址', '学校', '喜欢的编程语言', '额外意见', '条款同意'));

    // 循环遍历数据并输出每一行
    while ($row = $result->fetch_assoc()) {
        $exportRow = array(
            $row['id'],
            $row['name'],
            $row['email'],
            $row['phone'],
            $row['qq'],
            $row['age'],
            $row['gender'],
            $row['country'],
            $row['address'],
            $row['school'],
            $row['programming_languages'],
            $row['comments'],
            $row['terms']
        );
        fputcsv($output, $exportRow);
    }

    fclose($output);
    exit();
}

// 获取所有问卷
$stmt = $conn->prepare("SELECT * FROM survey_responses ORDER BY id DESC");
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>问卷管理 - H's blog circle</title>
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
            max-width: 100%;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            overflow-x: auto;
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
            font-size: 14px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        th {
            background-color: #34495e;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        tr:hover {
            background-color: #e9ecef;
        }
        .btn {
            display: inline-block;
            padding: 8px 12px;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }
        .btn-delete {
            background-color: #e74c3c;
        }
        .btn-delete:hover {
            background-color: #c0392b;
        }
        .btn-back {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            margin-top: 20px;
            transition: background-color 0.3s;
        }
        .btn-back:hover {
            background-color: #2980b9;
        }
        .button-container {
            text-align: center;
            margin-top: 30px;
        }
        .btn-export {
            background-color: #27ae60;
            margin-right: 10px;
        }
        .btn-export:hover {
            background-color: #2ecc71;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-clipboard-list"></i> 问卷管理</h1>
        <div class="button-container">
            <form method="POST" style="display: inline;">
                <button type="submit" name="export" class="btn btn-export">
                    <i class="fas fa-file-export"></i> 导出为 Excel
                </button>
            </form>
            <a href="admin_dashboard.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> 返回管理员后台
            </a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>姓名</th>
                    <th>电子邮件</th>
                    <th>手机号</th>
                    <th>QQ号</th>
                    <th>年龄</th>
                    <th>性别</th>
                    <th>国家</th>
                    <th>家庭住址</th>
                    <th>学校</th>
                    <th>喜欢的编程语言</th>
                    <th>额外意见</th>
                    <th>条款同意</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo $row['phone']; ?></td>
                    <td><?php echo $row['qq']; ?></td>
                    <td><?php echo $row['age']; ?></td>
                    <td><?php echo $row['gender']; ?></td>
                    <td><?php echo $row['country']; ?></td>
                    <td><?php echo $row['address']; ?></td>
                    <td><?php echo $row['school']; ?></td>
                    <td><?php echo $row['programming_languages']; ?></td>
                    <td><?php 
                        $comments = htmlspecialchars($row['comments'], ENT_QUOTES, 'UTF-8');
                        echo mb_substr($comments, 0, 50, 'UTF-8') . (mb_strlen($comments, 'UTF-8') > 50 ? '...' : ''); 
                    ?></td>
                    <td><?php echo htmlspecialchars($row['terms'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('确定要删除这份问卷吗？');">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="delete" class="btn btn-delete"><i class="fas fa-trash-alt"></i> 删除</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
