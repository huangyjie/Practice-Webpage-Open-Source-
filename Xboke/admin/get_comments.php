<?php
include('db_config.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $article_id = $_GET['article_id'];

    $sql = "SELECT * FROM comments WHERE article_id = ? ORDER BY comment_date DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $article_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $comments = [];
    while ($row = $result->fetch_assoc()) {
        $comments[] = $row;
    }

    echo json_encode($comments);
}

$conn->close();
