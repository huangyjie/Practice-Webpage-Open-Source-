<?php
include('admin/db_config.php');

$sql = "SELECT * FROM blog_posts ORDER BY date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>博客文章 - 我的博客</title>
    <link rel="stylesheet" href="cs/styles.css">
    <link rel="stylesheet" href="cs/nav.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('https://picsum.photos/1920/1080?grayscale') no-repeat center center/cover;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .blog-container {
            max-width: 1200px;
            margin: 100px auto 40px;
            padding: 20px;
        }

        .blog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .blog-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .blog-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .blog-card h2 {
            color: #35424a;
            margin-top: 0;
            font-size: 1.5rem;
        }

        .blog-card p {
            color: #666;
            line-height: 1.6;
        }

        .blog-card .date {
            color: #e8491d;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .blog-card .tags {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }

        .blog-card .tag {
            background: #35424a;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
        }

        .read-more {
            color: #e8491d;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
            margin-top: 10px;
            transition: color 0.3s ease;
        }

        .read-more:hover {
            color: #35424a;
        }
    </style>
</head>
<body>
    <div id="header-container"></div>

    <div class="blog-container">
        <div class="blog-grid">
            <!-- 博客文章卡片 -->
            <article class="blog-card">
                <div class="date">2024-03-20</div>
                <h2>技能高考相关话题</h2>
                <p>探讨技能高考的最新动态和备考策略...</p>
                <div class="tags">
                    <span class="tag">教育</span>
                    <span class="tag">考试</span>
                </div>
                <a href="article/jngk.html" class="read-more">阅读更多 →</a>
            </article>

            <article class="blog-card">
                <div class="date">2024-03-19</div>
                <h2>node-windows常用指令</h2>
                <p>详解node-windows的安装和使用方法...</p>
                <div class="tags">
                    <span class="tag">技术</span>
                    <span class="tag">Node.js</span>
                </div>
                <a href="article/node.html" class="read-more">阅读更多 →</a>
            </article>

            <article class="blog-card">
                <div class="date">2024-03-18</div>
                <h2>Jetbrains安装教程</h2>
                <p>全面的Jetbrains IDE安装配置指南...</p>
                <div class="tags">
                    <span class="tag">工具</span>
                    <span class="tag">开发</span>
                </div>
                <a href="article/Jetbrains.html" class="read-more">阅读更多 →</a>
            </article>

            <!-- 可以继续添加更多文章卡片 -->
        </div>
    </div>

    <script src="js/nav-component.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const nav = new NavBar();
            nav.loadNav('blog');
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>
