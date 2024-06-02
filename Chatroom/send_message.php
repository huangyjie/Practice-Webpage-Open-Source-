<?php
// Establish database connection
$servername = "localhost";
$username = "root";
$password = "123456";
$dbname = "wy";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

// Get message, username, and timestamp from POST request
$message = $_POST['message'];
$timestamp = $_POST['timestamp'];
$username = $_POST['username'];

// Insert message into database
$sql = "INSERT INTO messagess (username, message, timestamp) VALUES ('$username', '$message', '$timestamp')";
if ($conn->query($sql) === TRUE) {
    echo "消息已成功发送";
} else {
    echo "错误：" . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
