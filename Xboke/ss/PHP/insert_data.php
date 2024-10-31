<?php
include 'db_config.php';

// 处理用户输入
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $question = $_POST["question"];
    $optionA = $_POST["optionA"];
    $optionB = $_POST["optionB"];
    $optionC = $_POST["optionC"];
    $optionD = $_POST["optionD"];
    $correctAnswer = $_POST["correctAnswer"];

    // 构建插入语句
    $sql = "INSERT INTO 题库 (题干, 选项a, 选项b, 选项c, 选项d, 标准答案) 
            VALUES ('$question', '$optionA', '$optionB', '$optionC', '$optionD', '$correctAnswer')";

    // 执行插入
    if ($conn->query($sql) === TRUE) {
        echo "数据插入成功";
    } else {
        echo "数据插入失败: " . $conn->error;
    }
}

// 关闭连接
$conn->close();
?>
