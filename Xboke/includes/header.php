<?php
function renderHeader($active = '') {
    $menu_items = [
        'home' => ['url' => '/index.php', 'text' => '首页'],
        'about' => ['url' => '/about.html', 'text' => '关于'],
        'blog' => ['url' => '/blog.php', 'text' => '博客文章'],
        'message' => ['url' => '/250/index.html', 'text' => '留言板', 'title' => '给她的留言'],
        'survey' => ['url' => '/Questionnaires/index.html', 'text' => '调查问卷'],
        'questions' => ['url' => '/ss/index.html', 'text' => '题库查询'],
        'contact' => ['url' => '/contact.html', 'text' => '联系我'],
        'admin' => ['url' => '/admin/login.php', 'text' => '管理员后台']
    ];

    ob_start();
    ?>
    <header>
        <div class="container">
            <div id="navbar">
                <h1 class="fade-in"><span class="highlight">我的</span> 博客</h1>
                <nav>
                    <ul>
                        <?php foreach ($menu_items as $key => $item): ?>
                            <li class="fade-in <?php echo $active === $key ? 'current' : ''; ?>">
                                <a href="<?php echo $item['url']; ?>" 
                                   <?php echo isset($item['title']) ? 'title="'.$item['title'].'"' : ''; ?>>
                                    <?php echo $item['text']; ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </header>
    <?php
    return ob_get_clean();
}
?> 