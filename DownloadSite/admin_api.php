<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    die(json_encode(['error' => '未登录']));
}

require_once 'config/database.php';

$action = $_GET['action'] ?? '';

switch($action) {
    case 'get_categories':
        $stmt = $pdo->query("SELECT * FROM app_categories ORDER BY category_id DESC");
        echo json_encode(['categories' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        break;

    case 'get_apps':
        $stmt = $pdo->query("SELECT * FROM apps ORDER BY app_id DESC");
        echo json_encode(['apps' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        break;

    case 'add_category':
        $name = $_POST['category_name'] ?? '';
        $description = $_POST['category_description'] ?? '';
        
        if (!$name) {
            die(json_encode(['error' => '请填写分类名称']));
        }

        $description = $description ?: '无';

        $stmt = $pdo->prepare("INSERT INTO app_categories (category_name, category_description) VALUES (?, ?)");
        $stmt->execute([$name, $description]);
        echo json_encode(['success' => true]);
        break;

    case 'add_app':
        $category_id = $_POST['category_id'] ?? '';
        $name = $_POST['app_name'] ?? '';
        $description = $_POST['app_description'] ?? '';
        $download_url = $_POST['download_url'] ?? '';
        
        if (!$category_id || !$name || !$download_url) {
            die(json_encode(['error' => '请填写必要字段']));
        }

        $description = $description ?: '无';

        $stmt = $pdo->prepare("INSERT INTO apps (category_id, app_name, app_description, download_url) VALUES (?, ?, ?, ?)");
        $stmt->execute([$category_id, $name, $description, $download_url]);
        echo json_encode(['success' => true]);
        break;

    case 'toggle_category':
        $data = json_decode(file_get_contents('php://input'), true);
        $category_id = $data['category_id'] ?? 0;
        $is_hidden = $data['is_hidden'] ?? 0;
        
        $stmt = $pdo->prepare("UPDATE app_categories SET is_hidden = ? WHERE category_id = ?");
        $stmt->execute([$is_hidden, $category_id]);
        echo json_encode(['success' => true]);
        break;

    case 'toggle_app':
        $data = json_decode(file_get_contents('php://input'), true);
        $app_id = $data['app_id'] ?? 0;
        $is_hidden = $data['is_hidden'] ?? 0;
        
        $stmt = $pdo->prepare("UPDATE apps SET is_hidden = ? WHERE app_id = ?");
        $stmt->execute([$is_hidden, $app_id]);
        echo json_encode(['success' => true]);
        break;

    case 'delete_download':
        $data = json_decode(file_get_contents('php://input'), true);
        $app_id = $data['app_id'] ?? 0;
        
        $stmt = $pdo->prepare("UPDATE apps SET download_url = '' WHERE app_id = ?");
        $stmt->execute([$app_id]);
        echo json_encode(['success' => true]);
        break;

    case 'delete_category':
        $data = json_decode(file_get_contents('php://input'), true);
        $category_id = $data['category_id'] ?? 0;
        
        try {
            // 首先删除该分类下的所有应用
            $stmt = $pdo->prepare("DELETE FROM apps WHERE category_id = ?");
            $stmt->execute([$category_id]);
            
            // 然后删除分类
            $stmt = $pdo->prepare("DELETE FROM app_categories WHERE category_id = ?");
            $stmt->execute([$category_id]);
            
            echo json_encode(['success' => true]);
        } catch(PDOException $e) {
            echo json_encode(['error' => '删除失败']);
        }
        break;

    case 'delete_app':
        $data = json_decode(file_get_contents('php://input'), true);
        $app_id = $data['app_id'] ?? 0;
        
        try {
            $stmt = $pdo->prepare("DELETE FROM apps WHERE app_id = ?");
            $stmt->execute([$app_id]);
            echo json_encode(['success' => true]);
        } catch(PDOException $e) {
            echo json_encode(['error' => '删除失败']);
        }
        break;

    default:
        echo json_encode(['error' => '未知操作']);
}
?> 