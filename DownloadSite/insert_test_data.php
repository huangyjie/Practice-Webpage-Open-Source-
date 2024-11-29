<?php
header('Content-Type: text/html; charset=utf-8');

require_once 'config/database.php';

try {
    // 创建数据库
    $pdo->exec("CREATE DATABASE IF NOT EXISTS {$db_config['name']} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE {$db_config['name']}");
    
    // 删除现有表
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("DROP TABLE IF EXISTS apps");
    $pdo->exec("DROP TABLE IF EXISTS app_categories");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    // 创建表
    $pdo->exec("CREATE TABLE IF NOT EXISTS app_categories (
        category_id INT PRIMARY KEY AUTO_INCREMENT,
        category_name VARCHAR(50) NOT NULL,
        category_description TEXT,
        is_hidden TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS apps (
        app_id INT PRIMARY KEY AUTO_INCREMENT,
        category_id INT,
        app_name VARCHAR(100) NOT NULL,
        app_description TEXT,
        download_url VARCHAR(255) NOT NULL,
        download_count INT DEFAULT 0,
        is_hidden TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES app_categories(category_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 插入分类
    $categories = [
        ['开发工具 IDE', '各类集成开发环境'],
        ['运行环境', '各种编程语言的运行环境'],
        ['服务器工具', '服务器管理和部署工具'],
        ['数据库工具', '数据库管理和操作工具'],
        ['浏览器', '网页浏览器'],
        ['办公软件', '日常办公软件'],
        ['系统工具', '系统维护和管理工具'],
        ['图像处理', '图片编辑软件'],
        ['其他工具', '其他实用工具']
    ];

    $stmt = $pdo->prepare("INSERT INTO app_categories (category_name, category_description) VALUES (?, ?)");
    foreach ($categories as $category) {
        $stmt->execute($category);
    }

    // 插入应用
    $apps = [
        // 开发工具 IDE (category_id: 1)
        [1, 'Visual Studio 2022', '微软官方IDE', 'https://pan.karpov.cn/f/E9Vcp/vs2022.exe'],
        [1, 'Visual Studio 2010', '经典IDE', 'https://pan.karpov.cn/f/pQBFA/VC++2010.rar'],
        [1, 'VS Code', '轻量级代码编辑器', 'https://pan.karpov.cn/f/866UY/VScode.exe'],
        [1, 'Dev C++', 'C/C++开发工具', 'https://pan.karpov.cn/f/7L6hZ/Dev%20C++.exe'],
        [1, 'IDEA', 'Java开发IDE', 'https://pan.karpov.cn/f/nrNfm/IDEA-2024.1.exe'],
        [1, 'PyCharm', 'Python开发IDE', 'https://pan.karpov.cn/f/qYLfX/Pycharm-professional-2024.1.exe'],
        [1, 'PhpStorm', 'PHP开发IDE', 'https://pan.karpov.cn/f/rR7H2/PhpStorm-2024.1.exe'],
        [1, 'WebStorm', 'Web开发IDE', 'https://pan.karpov.cn/f/APxiE/WebStorm-2024.1.exe'],
        [1, 'Goland', 'Go语言开发IDE', 'https://pan.karpov.cn/f/voxHA/Goland-2024.1.exe'],
        [1, 'Eclipse', 'Java开发IDE', 'https://pan.karpov.cn/f/wmxTy/Eclipse2024(64bit).zip'],
        [1, 'HBuilderX', '前端开发工具', 'https://pan.karpov.cn/f/0V8Hm/HBuilderX.zip'],
        [1, 'Datagrip', '数据库开发工具', 'https://pan.karpov.cn/f/zKEh4/Datagrip-2024.1.1.exe'],
        [1, 'Cursor', 'AI辅助编程工具', 'https://pan.karpov.cn/f/G5NHA/Cursor.exe'],

        // 运行环境 (category_id: 2)
        [2, 'JDK', 'Java开发工具包', 'https://pan.karpov.cn/f/jq8TR/Jdk-17.exe'],
        [2, 'JRE', 'Java运行环境', 'https://pan.karpov.cn/f/kZoF5/Jre8u421.exe'],
        [2, 'Python', 'Python解释器', 'https://pan.karpov.cn/f/D9oU3/Python_3.12.5.exe'],
        [2, 'Mingw', 'C/C++编译环境', 'https://pan.karpov.cn/f/gLnuG/Mingw.zip'],
        [2, 'Node.js', 'JavaScript运行环境', 'https://pan.karpov.cn/f/Q0wCo/Node.js.msi'],

        // 服务器工具 (category_id: 3)
        [3, '宝塔面板', '服务器管理面板', 'https://pan.karpov.cn/f/9Q1U2/pagoda.exe'],
        [3, '小皮面板', '本地服务器面板', 'https://pan.karpov.cn/f/BLjh8/Smallskin.exe'],

        // 数据库工具 (category_id: 4)
        [4, 'Navicat16', '数据库管理工具', 'https://pan.karpov.cn/f/KZNU4/Navicat16.exe'],
        [4, 'Navicat破解工具', 'Navicat激活工具', 'https://pan.karpov.cn/f/JqniK/NavicatCrack.exe'],

        // 浏览器 (category_id: 5)
        [5, '谷歌浏览器', '主流浏览器', 'https://pan.karpov.cn/f/OyKcv/ChromeSetup.exe'],

        // 办公软件 (category_id: 6)
        [6, 'Office2016', '微软办公套件', 'https://pan.karpov.cn/f/yrOHL/Office2016(64bit).zip'],
        [6, 'Notepad++', '文本编辑器', 'https://pan.karpov.cn/f/mQ7tW/Notepad++.rar'],

        // 系统工具 (category_id: 7)
        [7, 'VMware', '虚拟机软件', 'https://pan.karpov.cn/f/oQ7Cv/VMware17.0.exe'],
        [7, 'Geek', '软件卸载工具', 'https://pan.karpov.cn/f/L8kuQ/Geek.exe'],
        [7, 'Bandizip', '压缩解压工具', 'https://pan.karpov.cn/f/PZxs3/BANDIZIP.EXE'],
        [7, 'Mihomo', '代理工具', 'https://pan.karpov.cn/f/MQzIz/Mihomo.exe'],

        // 图像处理 (category_id: 8)
        [8, 'PS2024', '图像处理软件', 'https://pan.karpov.cn/f/Ro9U5/PS2024v25.0(64bit).zip'],

        // 其他工具 (category_id: 9)
        [9, 'JetBrains全家桶破解工具', 'JetBrains系列软件激活工具', 'https://pan.karpov.cn/f/xv5sx/Crack.zip']
    ];

    $stmt = $pdo->prepare("INSERT INTO apps (category_id, app_name, app_description, download_url) VALUES (?, ?, ?, ?)");
    foreach ($apps as $app) {
        $stmt->execute($app);
    }

    echo "数据添加成功！<br>";
    echo "已添加 " . count($categories) . " 个分类<br>";
    echo "已添加 " . count($apps) . " 个应用<br>";

} catch(PDOException $e) {
    die("错误: " . $e->getMessage() . "<br>");
}
?> 