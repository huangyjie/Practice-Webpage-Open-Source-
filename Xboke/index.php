<?php
require_once 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>首页 - 我的博客</title>
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

        #showcase {
            color: #ffffff;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
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

        .showcase-content {
            z-index: 2;
            padding: 2rem;
            padding-bottom: 4rem;
            border-radius: 10px;
            background: rgba(0, 0, 0, 0.7);
            position: relative;
        }

        .showcase-content h1 {
            font-size: 3rem;
            margin-bottom: 20px;
        }

        .showcase-content p {
            font-size: 1.2rem;
            margin-bottom: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .section {
            margin-top: 40px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            color: #333;
        }

        .section h2 {
            font-size: 2rem;
            margin-bottom: 20px;
            color: #35424a;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .grid a {
            background: rgba(255, 255, 255, 0.8);
            color: #333;
            padding: 15px;
            border-radius: 5px;
            text-decoration: none;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .grid a:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        footer {
            background-color: rgba(53, 66, 74, 0.9);
            color: #ffffff;
            text-align: center;
            padding: 20px 0;
            font-size: 0.9rem;
        }

        footer a {
            color: #e8491d;
            text-decoration: none;
            margin: 0 10px;
            transition: color 0.3s ease;
        }

        footer a:hover {
            color: #ffffff;
        }

        .triangle-down {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 20px solid transparent;
            border-right: 20px solid transparent;
            border-top: 20px solid #ffffff;
            cursor: pointer;
            transition: transform 0.3s ease;
            z-index: 2;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateX(-50%) translateY(0);
            }
            40% {
                transform: translateX(-50%) translateY(-10px);
            }
            60% {
                transform: translateX(-50%) translateY(-5px);
            }
        }

        .triangle-down:hover {
            transform: translateX(-50%) translateY(5px);
        }
    </style>
</head>
<body>
    <div id="header-container"></div>
    
    <section id="showcase">
        <div class="showcase-content">
            <h1>欢迎来到我的博客</h1>
            <p>这里是我分享想法和经验的地方</p>
            <p id="current-time"></p>
            <div class="triangle-down" onclick="scrollToBottom()"></div>
        </div>
    </section>

    <div class="container">

        <section class="section latest-posts">
            <h2>最新文章</h2>
            <div class="grid">
                <a href="article/jngk.html" title="技能高考相关话题">技能高考相关话题</a>
                <a href="article/node.html" title="node">node-windows常用指令</a>
                <a href="article/Jetbrains.html" title="Jetbrains">Jetbrains安装教程</a>
                <a href="article/Brush questions.html" title="刷题平台分享">刷题平台分享</a>
                <a href="article/Eclipse.html" title="暂时还没有博客！">Eclipse下载教程 </a>
                <a href="article/Download.html" title="Download">软件下载 </a>
                <a href="http:\\xiaohuanghuang.icu:5000" title="安全微伴小助手">安全微伴小助手</a>
                <a href="http:\\xiaohuanghuang.icu:5001" title="学习通">学习通小助手</a>
                <a href="#" title="暂时还没有博客！">文章标题 </a>
                <a href="#" title="暂时还没有博客！">文章标题 </a>
                <a href="#" title="暂时还没有博客！">文章标题 </a>
            </div>
        </section>

        <section class="section popular-topics">
            <h2>热门话题</h2>
            <div class="grid">
                <a href="article/wz/W1.html">《2023年度十大前沿科技趋势报告》</a>
                <a href="article/wz/W2.html">《2024年十大新兴技术将如何影响世界》</a>
                <a href="#">话题</a>
                <a href="#">话题</a>
                <a href="#">话题</a>
                <a href="#">话题</a>
                <a href="#">话题</a>
                <a href="#">话题</a>
            </div>
        </section>

        <section class="section friend-links">
            <h2>友情链接</h2>
            <div class="grid">
                <a href="https://jyt.hubei.gov.cn/" target="_top">湖北省教育厅</a>
                <a href="https://www.cjit.edu.cn/" target="_top">长江工程职业技术学院</a>
                <a href="https://jsjxy.cjit.edu.cn/" target="_top">长江工程职业技术学院计算机学院</a>
                <a href="https://github.com/huangyjie/Practice-Webpage-Open-Source-.git" target="_top" title="会分享一些好玩的网页源码和实用的工具">作者的GitHub</a>
                <a href="article/Source code/Personal-blog.7z" target="_top">源码下载(旧版本UI)</a>
                <a href="article/Source code/Xboke.7z" target="_top">源码下载(新UI)</a>
            </div>
        </section>
    </div>

    <footer>
        <p>&copy; 2024 huang yu jie. 保留所有权利。</p>
        <p>备案号: <a href="https://beian.miit.gov.cn/" target="_blank">鄂ICP备2024077565号</a></p>
        <p><a href="http://www.beian.gov.cn/" target="_blank"> <img src="备案编号图标.png" alt="公安备案" style="vertical-align:middle;"> 公安备案号：鄂网安备 2024077565号</a></p>
    </footer>

    <script>
        function updateTime() {
            const now = new Date();
            const formattedTime = now.toLocaleString('zh-CN', { hour12: false });
            document.getElementById('current-time').textContent = `当前时间：${formattedTime}`;
        }

        function scrollToBottom() {
            window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateTime();
            setInterval(updateTime, 1000);

            const fadeElements = document.querySelectorAll('.fade-in');
            fadeElements.forEach(element => {
                element.style.opacity = '1';
            });
        });
    </script>
    <script src="js/nav-component.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const nav = new NavBar();
            nav.loadNav('home');
        });
    </script>
</body>
</html> 