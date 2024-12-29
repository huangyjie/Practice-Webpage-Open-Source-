<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// 添加调试日志
error_log('Nav API called with active: ' . ($_GET['active'] ?? 'none'));

// 导航菜单配置
$nav_config = [
    'logo' => '我的博客',
    'menu_items' => [
        [
            'id' => 'home',
            'text' => '首页',
            'url' => '/index.php',
            'active' => false
        ],
        [
            'id' => 'about',
            'text' => '关于',
            'url' => '/about.html',
            'active' => false
        ],
        [
            'id' => 'blog',
            'text' => '博客文章',
            'url' => '/blog.php',
            'active' => false
        ],
        [
            'id' => 'message',
            'text' => '留言板',
            'url' => '/250/index.html',
            'title' => '给她的留言',
            'active' => false
        ],
        [
            'id' => 'survey',
            'text' => '调查问卷',
            'url' => '/Questionnaires/index.html',
            'active' => false
        ],
        [
            'id' => 'questions',
            'text' => '题库查询',
            'url' => '/ss/index.html',
            'active' => false
        ],
        [
            'id' => 'contact',
            'text' => '联系我',
            'url' => '/contact.html',
            'active' => false
        ],
        [
            'id' => 'admin',
            'text' => '管理员后台',
            'url' => '/admin/login.php',
            'active' => false
        ]
    ],
    'settings' => [
        'fixed' => true,
        'transparent' => true,
        'animation' => true
    ]
];

// 获取当前活动页面
$active_page = isset($_GET['active']) ? $_GET['active'] : '';

// 设置活动状态
foreach ($nav_config['menu_items'] as &$item) {
    $item['active'] = ($item['id'] === $active_page);
}

echo json_encode($nav_config);
?> 