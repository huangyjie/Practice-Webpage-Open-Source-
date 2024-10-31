<?php
include 'db_config.php';

// 处理用户输入
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_input = $_POST["user_input"];
    
    // 构建查询语句
    $sql = "SELECT * FROM 题库 WHERE 题干 LIKE '%$user_input%'";
    
    // 执行查询
    $result = $conn->query($sql);

    if ($result) {
        if ($result->num_rows > 0) {
            // 输出数据
            while($row = $result->fetch_assoc()) {
                echo "序号: " . $row["序号"]. "<br>";
                echo "题干: " . $row["题干"]. "<br>";
                echo "选项A: " . $row["选项a"]. "<br>";
                echo "选项B: " . $row["选项b"]. "<br>";
                echo "选项C: " . $row["选项c"]. "<br>";
                echo "选项D: " . $row["选项d"]. "<br>";
                echo "标准答案: " . $row["标准答案"]. "<br>";
                echo "<hr>";
            }
        } else {
            echo "未找到你想要的结果请你检查题目前后是否有空格";
        }
    } else {
        echo "查询失败: " . $conn->error;
    }
}

// 关闭连接
$conn->close();
?>
