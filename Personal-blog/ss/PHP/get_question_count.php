<?php
include 'db_config.php';

// 获取题目数量和最后一个题的序号
$sql = "SELECT COUNT(*) AS total_questions, MAX(序号) AS last_question_serial FROM 题库";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $totalQuestions = $row["total_questions"];
    $lastQuestionSerial = $row["last_question_serial"];
    echo "题目总数: " . $totalQuestions . "<br>最后一个题的序号: " . $lastQuestionSerial;
} else {
    echo "获取题目数量失败";
}

// 关闭连接
$conn->close();
?>
