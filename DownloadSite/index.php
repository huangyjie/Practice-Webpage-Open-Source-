<?php
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>应用下载中心</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="header">
        <span class="qq-number">QQ: 1401466869</span>
        <img class="qq-avatar" src="https://q1.qlogo.cn/g?b=qq&nk=1401466869&s=100" alt="QQ头像">
    </div>

    <div class="container" id="categoriesContainer">
        <?php
        try {
            $categories = [];
            $stmt = $pdo->query("SELECT * FROM app_categories WHERE is_hidden = 0");
            while ($category = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $category_id = $category['category_id'];
                $stmt2 = $pdo->prepare("SELECT * FROM apps WHERE category_id = ? AND is_hidden = 0");
                $stmt2->execute([$category_id]);
                $apps = $stmt2->fetchAll(PDO::FETCH_ASSOC);
                
                if (count($apps) > 0) {
                    ?>
                    <div class="category-card">
                        <h2 class="category-title"><?php echo htmlspecialchars($category['category_name']); ?></h2>
                        <p class="category-description"><?php echo htmlspecialchars($category['category_description']); ?></p>
                        <div class="apps-container">
                            <?php foreach ($apps as $app) { ?>
                                <div class="app-card">
                                    <div class="app-name"><?php echo htmlspecialchars($app['app_name']); ?></div>
                                    <div class="app-description"><?php echo htmlspecialchars($app['app_description']); ?></div>
                                    <div class="download-count">下载次数: <?php echo $app['download_count']; ?></div>
                                    <a href="download.php?id=<?php echo $app['app_id']; ?>" 
                                       class="download-btn" 
                                       data-app-id="<?php echo $app['app_id']; ?>">
                                       下载应用
                                    </a>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php
                }
            }
        } catch(PDOException $e) {
            echo "获取数据失败: " . $e->getMessage();
        }
        ?>
    </div>

    <script>
        document.addEventListener('click', async function(e) {
            if (e.target.classList.contains('download-btn')) {
                e.preventDefault();
                const appId = e.target.getAttribute('data-app-id');
                const downloadUrl = e.target.href;
                
                try {
                    const response = await fetch(`download.php?id=${appId}&action=increment`);
                    const data = await response.json();
                    if (data.success) {
                        const card = e.target.closest('.app-card');
                        const countElement = card.querySelector('.download-count');
                        const currentCount = parseInt(countElement.textContent.split(': ')[1]);
                        countElement.textContent = `下载次数: ${currentCount + 1}`;
                        window.location.href = data.download_url;
                    }
                } catch (error) {
                    console.error('下载失败:', error);
                }
            }
        });
    </script>
</body>
</html> 