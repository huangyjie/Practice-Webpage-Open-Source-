<?php
$servername = "localhost";
$username = "root";
$password = "123456";
$port = 3306;
$dbname = "wy";

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 如果用户之前选择了记住密码并且有保存的 Cookie，则直接重定向到 index.html 页面
if (isset($_COOKIE['remember_username']) && isset($_COOKIE['remember_password'])) {
    $username = $_COOKIE['remember_username'];
    $password = $_COOKIE['remember_password'];

    // 查询数据库，检查用户名和密码是否匹配
    $check_user_sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $conn->query($check_user_sql);

    if ($result->num_rows == 1) {
        // 登录成功后重定向到 index.html 页面
        echo "<script>window.location.href='index.html';</script>";
        exit(); // 确保重定向后，后续代码不会被执行
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 查询数据库，检查用户名和密码是否匹配
    $check_user_sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $conn->query($check_user_sql);

    if ($result->num_rows == 1) {
        // 登录成功，检查是否选择了记住密码
        if (isset($_POST['remember']) && $_POST['remember'] == 'on') {
            // 设置持久的 Cookie
            setcookie('remember_username', $username, time() + 3600 * 24 * 30); // 保存 30 天
            setcookie('remember_password', $password, time() + 3600 * 24 * 30); // 保存 30 天
        } else {
            // 删除记住密码的 Cookie
            setcookie('remember_username', '', time() - 3600);
            setcookie('remember_password', '', time() - 3600);
        }

        echo "<script>alert('登录成功！');</script>";
        // 登录成功后重定向到 index.html 页面
        echo "<script>window.location.href='jiaz.html';</script>";
        exit(); // 确保重定向后，后续代码不会被执行
    } else {
        echo "<script>alert('用户名或密码错误，请重新输入。');</script>";
        // 登录失败后重定向到登录页面 login.html
        echo "<script>window.location.href='login.html';</script>";
        exit(); // 确保重定向后，后续代码不会被执行
    }
}

$conn->close();
?>
