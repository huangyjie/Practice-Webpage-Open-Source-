<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once 'config/database.php';

// 获取POST数据
$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

try {
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ? AND password = ?");
    $stmt->execute([$username, $password]);
    
    if ($stmt->fetch()) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => '用户名或密码错误']);
    }
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => '数据库错误']);
}
?> 