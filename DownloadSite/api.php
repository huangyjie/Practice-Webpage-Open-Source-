<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'config/database.php';

// 处理下载计数
if (isset($_GET['action']) && $_GET['action'] === 'increment') {
    $app_id = filter_input(INPUT_GET, 'app_id', FILTER_VALIDATE_INT);
    if ($app_id) {
        try {
            $stmt = $pdo->prepare("UPDATE apps SET download_count = download_count + 1 WHERE app_id = ?");
            $stmt->execute([$app_id]);
            echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
        } catch(PDOException $e) {
            echo json_encode(['error' => '更新下载次数失败'], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }
}

// 获取所有分类和应用数据
try {
    $categories = [];
    $stmt = $pdo->query("SELECT * FROM app_categories WHERE is_hidden = 0");
    while ($category = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $category_id = $category['category_id'];
        $stmt2 = $pdo->prepare("SELECT * FROM apps WHERE category_id = ? AND is_hidden = 0");
        $stmt2->execute([$category_id]);
        $category['apps'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        $categories[] = $category;
    }
    echo json_encode(['categories' => $categories], JSON_UNESCAPED_UNICODE);
} catch(PDOException $e) {
    echo json_encode(['error' => '获取数据失败'], JSON_UNESCAPED_UNICODE);
} 