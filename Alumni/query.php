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

// 获取查询提交码
$query_code = $_POST['query_code'];

// 查询数据库
$sql = "SELECT name, birthdate, gender, age, zodiac, blood_type, qq, phone, message FROM classmates WHERE submit_code='$query_code'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode([
        'success' => true,
        'name' => $row['name'], //姓名
        'birthdate' => $row['birthdate'], //生日
        'gender' => $row['gender'], //性别
        'age' => $row['age'], //年龄
        'zodiac' => $row['zodiac'],  //星座
        'blood_type' => $row['blood_type'], //血型
        'qq' => $row['qq'], //qq
        'phone' => $row['phone'], //电话
        'message' => $row['message'], //留言信息
        'submit_code' => $row['submit_code'] //提交码
    ]);
} else {
    echo json_encode(['success' => false, 'message' => '没有找到对应的留言']);
}

$conn->close();
?>
