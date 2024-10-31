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
    <title>博客文章 - H's blog circle</title>
    <link rel="stylesheet" href="cs/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        #showcase {
            min-height: 100vh;
            background: url('https://picsum.photos/1920/1080') no-repeat center center/cover;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #ffffff;
            position: relative;
            padding: 80px 0;
        }
        #showcase::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }
        .blog-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            margin: 80px auto;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            max-width: 800px;
            width: 90%;
            position: relative;
            z-index: 2;
        }
        .blog-post {
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 1px solid #e0e0e0;
        }
        .blog-post:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .blog-post h3 {
            color: #35424a;
            font-size: 28px;
            margin-bottom: 15px;
            font-weight: 700;
        }
        .blog-post-content {
            color: #555;
            line-height: 1.8;
            font-size: 16px;
            margin-bottom: 20px;
            overflow: hidden;
            max-height: 9.6em; /* 6行的高度 */
            transition: max-height 0.3s ease-out;
        }
        .blog-post-content.expanded {
            max-height: none;
        }
        .read-more {
            color: #35424a;
            cursor: pointer;
            font-weight: bold;
            display: inline-block;
            margin-top: 10px;
        }
        .read-more:hover {
            text-decoration: underline;
        }
        .read-more i {
            margin-left: 5px;
            transition: transform 0.3s ease;
        }
        .read-more.expanded i {
            transform: rotate(180deg);
        }
        .blog-post-meta {
            font-size: 14px;
            color: #888;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .blog-post-meta i {
            margin-right: 5px;
        }
        .no-posts {
            text-align: center;
            color: #888;
            font-style: italic;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div id="navbar">
                <h1 class="fade-in"><span class="highlight">H's blog</span> circle</h1>
                <nav>
                    <ul>
                        <li class="fade-in"><a href="index.html">首页</a></li>
                        <li class="current fade-in"><a href="#">博客文章</a></li>
                        <li class="fade-in"><a href="250/index.html" title="给她的留言">留言板</a></li>
                        <li class="fade-in"><a href="Questionnaires/index.html" title="调查问卷">调查问卷</a></li>
                        <li class="fade-in"><a href="ss/jiaz.html">题库查询</a></li>
                        <li class="fade-in"><a href="contact.html">联系我</a></li>
                        <li class="fade-in"><a href="admin/login.php">管理员后台</a></li>
                        <li class="fade-in"><a href="about.html">关于</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <section id="showcase">
        <div class="blog-container fade-in">
            <h2 style="text-align: center; margin-bottom: 30px; color: #35424a;">博客文章</h2>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<article class='blog-post'>";
                    echo "<h3>" . htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8') . "</h3>";
                    echo "<div class='blog-post-content'>" . nl2br(htmlspecialchars($row['content'], ENT_QUOTES, 'UTF-8')) . "</div>";
                    echo "<div class='read-more'>阅读更多 <i class='fas fa-chevron-down'></i></div>";
                    echo "<div class='blog-post-meta'>";
                    echo "<span><i class='fas fa-user'></i> " . htmlspecialchars($row['author'], ENT_QUOTES, 'UTF-8') . "</span>";
                    echo "<span><i class='fas fa-calendar-alt'></i> " . htmlspecialchars(date('Y年m月d日 H:i', strtotime($row['date'])), ENT_QUOTES, 'UTF-8') . "</span>";
                    echo "</div>";
                    echo "</article>";
                }
            } else {
                echo "<p class='no-posts'>暂时没有文章</p>";
            }
            ?>
        </div>
    </section>

    <footer>
        <p>&copy; 2024 huang yu jie. 保留所有权利。</p>
        <p>备案号: <a href="https://beian.miit.gov.cn/" target="_blank">鄂ICP备2024077565号</a></p>
        <p><a href="http://www.beian.gov.cn/" target="_blank"> <img src="备案编号图标.png" alt="公安备案" style="vertical-align:middle;"> 公安备案号：鄂网安备 2024077565号</a></p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fadeElements = document.querySelectorAll('.fade-in');
            fadeElements.forEach(element => {
                element.style.opacity = '1';
            });

            const readMoreButtons = document.querySelectorAll('.read-more');
            readMoreButtons.forEach(button => {
                const content = button.previousElementSibling;
                if (content.scrollHeight > content.clientHeight) {
                    button.style.display = 'inline-block';
                } else {
                    button.style.display = 'none';
                }

                button.addEventListener('click', function() {
                    content.classList.toggle('expanded');
                    this.classList.toggle('expanded');
                    if (content.classList.contains('expanded')) {
                        this.innerHTML = '收起 <i class="fas fa-chevron-up"></i>';
                    } else {
                        this.innerHTML = '阅读更多 <i class="fas fa-chevron-down"></i>';
                    }
                });
            });
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>
