<?php
$servername = "localhost";
$username = "root";
$password = "123456";
$port = 3306;
$dbname = "user_login";

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = $_POST['new_username'];
    $new_password = $_POST['new_password'];

    // 验证用户名是否为邮箱或手机号格式
    if (filter_var($new_username, FILTER_VALIDATE_EMAIL)) {
        // 邮箱格式
        $check_username_sql = "SELECT * FROM users WHERE username='$new_username'";
    } elseif (preg_match('/^\d{11}$/', $new_username)) {
        // 手机号格式
        // 这里你需要根据你的数据库结构来匹配手机号字段，下面是一个示例
        // $check_username_sql = "SELECT * FROM users WHERE phone_number='$new_username'";
    } else {
        // 不是邮箱也不是手机号
        echo "<script>alert('请输入有效的邮箱或手机号。');</script>";
        exit(); // 中止脚本执行
    }

    // 查询数据库，检查用户名是否已存在
    $result = $conn->query($check_username_sql);
    if ($result->num_rows > 0) {
        echo "<script>alert('用户名已存在，请选择其他用户名。');</script>";
    } else {
        // 将用户数据插入到数据库中
        $sql = "INSERT INTO users (username, password) VALUES ('$new_username', '$new_password')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Registration successful!');</script>";
            echo "<script>window.location.href='login.html';</script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

$conn->close();
?>
