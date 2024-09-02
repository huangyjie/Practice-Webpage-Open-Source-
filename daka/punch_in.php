<?php
$servername = "localhost";
$username = "root";
$password = "123456";
$dbname = "StudyPunchIn";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

$date = $_POST['date'];
$mac_address = $_POST['mac_address'];
$user_id = 'user_123';  // 假设用户ID是预先定义的或从某处获得

// 检查是否已经打过卡
$checkSql = "SELECT * FROM punch_records WHERE user_id='$user_id' AND punch_date='$date'";
$result = $conn->query($checkSql);

if ($result->num_rows > 0) {
    echo "今天已经打过卡了！";
} else {
    // 插入打卡记录
    $insertSql = "INSERT INTO punch_records (user_id, punch_date) VALUES ('$user_id', '$date')";
    if ($conn->query($insertSql) === TRUE) {
        echo "打卡成功！";
    } else {
        echo "Error: " . $insertSql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
