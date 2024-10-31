<?php
session_start();

// 检查是否已登录
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include('db_config.php');

if (!isset($_GET['id'])) {
    header("Location: manage_surveys.php");
    exit();
}

$id = $_GET['id'];
$sql = "SELECT * FROM survey_responses WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$survey = $result->fetch_assoc();

if (!$survey) {
    header("Location: manage_surveys.php");
    exit();
}

$conn->set_charset("utf8mb4");

?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>查看问卷 - H's blog circle</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #35424a;
        }
        .survey-details {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .survey-details p {
            margin-bottom: 10px;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>查看问卷</h1>
        <div class="survey-details">
            <p><strong>姓名:</strong> <?php echo htmlspecialchars($survey['name']); ?></p>
            <p><strong>电子邮件:</strong> <?php echo htmlspecialchars($survey['email']); ?></p>
            <p><strong>手机号:</strong> <?php echo htmlspecialchars($survey['phone']); ?></p>
            <p><strong>QQ号:</strong> <?php echo htmlspecialchars($survey['qq']); ?></p>
            <p><strong>年龄:</strong> <?php echo htmlspecialchars($survey['age']); ?></p>
            <p><strong>性别:</strong> <?php echo htmlspecialchars($survey['gender']); ?></p>
            <p><strong>国家:</strong> <?php echo htmlspecialchars($survey['country']); ?></p>
            <p><strong>家庭住址:</strong> <?php echo htmlspecialchars($survey['address']); ?></p>
            <p><strong>学校:</strong> <?php echo htmlspecialchars($survey['school']); ?></p>
            <p><strong>喜欢的编程语言:</strong> <?php echo htmlspecialchars($survey['programming_languages']); ?></p>
            <p><strong>额外意见:</strong> <?php echo nl2br(htmlspecialchars($survey['comments'])); ?></p>
            <p><strong>条款同意:</strong> <?php echo htmlspecialchars($survey['terms']); ?></p>
        </div>
        <a href="manage_surveys.php" class="back-link">返回问卷列表</a>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
