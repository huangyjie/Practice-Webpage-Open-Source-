<?php
// 文件路径
$file = 'path/to/database.db';

// 检查文件是否存在
if (file_exists($file)) {
    // 设置响应头，指示浏览器下载文件
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($file) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;
} else {
    // 如果文件不存在，显示错误消息
    echo '文件不存在。';
}
?>
