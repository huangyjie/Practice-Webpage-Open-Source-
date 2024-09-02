<?php
$servername = "localhost";
$username = "root";
$password = "123456";
$dbname = "StudyPunchIn";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

$mac_address = $_POST['mac_address'];
$user_id = 'user_123';  // 假设用户ID是预先定义的或从某处获得

$sql = "INSERT INTO users (user_id, mac_address) VALUES ('$user_id', '$mac_address')
        ON DUPLICATE KEY UPDATE mac_address='$mac_address'";

if ($conn->query($sql) === TRUE) {
    echo "authenticated";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
