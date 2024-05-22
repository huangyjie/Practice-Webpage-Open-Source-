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

// Select messages from database
$sql = "SELECT * FROM messagess ORDER BY timestamp DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        echo "<p class='message'><strong>" . $row["username"]. ":</strong> " . $row["message"]. " <span>(" . $row["timestamp"]. ")</span></p>";
    }
} else {
    echo "还没有消息";
}
$conn->close();
?>
