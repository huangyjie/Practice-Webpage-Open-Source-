<?php
$servername = "localhost";
$username = "root";
$password = "123456";
$dbname = "wy";

// 创建连接
$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

// 设置连接字符集为utf8mb4
$conn->set_charset("utf8mb4");

$name = $_POST['name']; // 获取姓名
$birthdate = $_POST['birthdate']; // 获取生日
$gender = $_POST['gender']; // 获取性别
$age = $_POST['age']; // 获取年龄
$zodiac = $_POST['zodiac']; // 获取星座
$blood_type = $_POST['blood_type']; // 获取血型
$qq = $_POST['qq']; // 获取QQ号码
$phone = $_POST['phone']; // 获取电话号码
$message = $_POST['message']; // 获取留言信息
$submit_code = $_POST['submit_code']; // 获取提交码


// 插入数据到数据库
$sql = "INSERT INTO classmates (name, birthdate, gender, age, zodiac, blood_type, qq, phone, message, submit_code) 
        VALUES ('$name', '$birthdate', '$gender', '$age', '$zodiac', '$blood_type', '$qq', '$phone', '$message', '$submit_code')";
if ($conn->query($sql) === TRUE) {
    echo "新记录插入成功";
} else {
    echo "错误: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
