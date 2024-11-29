<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'config/database.php';

$app_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$action = $_GET['action'] ?? '';

if (!$app_id) {
    die(json_encode(['error' => '无效的应用ID']));
}

try {
    if ($action === 'increment') {
        // 增加下载次数
        $stmt = $pdo->prepare("UPDATE apps SET download_count = download_count + 1 WHERE app_id = ?");
        $stmt->execute([$app_id]);
        
        // 获取下载链接
        $stmt = $pdo->prepare("SELECT download_url FROM apps WHERE app_id = ?");
        $stmt->execute([$app_id]);
        $app = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($app) {
            echo json_encode([
                'success' => true,
                'download_url' => $app['download_url']
            ]);
        } else {
            echo json_encode(['error' => '应用不存在']);
        }
    } else {
        // 直接获取下载链接并跳转
        $stmt = $pdo->prepare("SELECT download_url FROM apps WHERE app_id = ?");
        $stmt->execute([$app_id]);
        $app = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($app) {
            header('Location: ' . $app['download_url']);
            exit;
        } else {
            die('应用不存在');
        }
    }
} catch(PDOException $e) {
    echo json_encode(['error' => '数据库错误']);
}
?> 