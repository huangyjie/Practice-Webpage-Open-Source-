<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: admin_login.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>管理面板</title>
    <style>
        body {
            font-family: 'Microsoft YaHei', sans-serif;
            margin: 0;
            padding: 20px;
            background: #f0f2f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input, textarea, select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            padding: 8px 16px;
            background: #1890ff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #40a9ff;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .logout {
            background: #ff4d4f;
        }
        .logout:hover {
            background: #ff7875;
        }

        /* 移动端适配样式 */
        @media screen and (max-width: 768px) {
            body {
                padding: 10px;
            }

            .container {
                padding-top: 60px;
            }

            .header {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                background: white;
                padding: 10px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                z-index: 1000;
            }

            .card {
                padding: 15px;
                margin-bottom: 15px;
            }

            .form-group {
                margin-bottom: 12px;
            }

            input, textarea, select {
                padding: 6px;
            }

            .table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            .table th, .table td {
                padding: 8px;
                font-size: 14px;
            }

            button {
                padding: 6px 12px;
                font-size: 14px;
            }

            h1 {
                font-size: 20px;
            }

            h2 {
                font-size: 18px;
            }
        }

        /* 小屏幕手机适配 */
        @media screen and (max-width: 480px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }

            .logout {
                width: 100%;
                margin-top: 5px;
            }

            .table th, .table td {
                padding: 6px;
                font-size: 12px;
            }

            button {
                padding: 4px 8px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>管理面板</h1>
            <div>
                <span>欢迎, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                <button class="logout" onclick="logout()">退出登录</button>
            </div>
        </div>

        <!-- 添加分类 -->
        <div class="card">
            <h2>添加分类</h2>
            <form id="categoryForm">
                <div class="form-group">
                    <label>分类名称</label>
                    <input type="text" name="category_name" required>
                </div>
                <div class="form-group">
                    <label>分类描述</label>
                    <textarea name="category_description"></textarea>
                </div>
                <button type="submit">添加分类</button>
            </form>
        </div>

        <!-- 添加应用 -->
        <div class="card">
            <h2>添加应用</h2>
            <form id="appForm">
                <div class="form-group">
                    <label>选择分类</label>
                    <select name="category_id" required id="categorySelect">
                    </select>
                </div>
                <div class="form-group">
                    <label>应用名称</label>
                    <input type="text" name="app_name" required>
                </div>
                <div class="form-group">
                    <label>应用描述</label>
                    <textarea name="app_description"></textarea>
                </div>
                <div class="form-group">
                    <label>下载链接</label>
                    <input type="text" name="download_url" required>
                </div>
                <button type="submit">添加应用</button>
            </form>
        </div>

        <!-- 管理现有内容 -->
        <div class="card">
            <h2>管理分类</h2>
            <div id="categoriesList"></div>
        </div>

        <div class="card">
            <h2>管理应用</h2>
            <div id="appsList"></div>
        </div>
    </div>

    <script>
        // 加载分类列表
        async function loadCategories() {
            const response = await fetch('admin_api.php?action=get_categories');
            const data = await response.json();
            const select = document.getElementById('categorySelect');
            select.innerHTML = data.categories.map(cat => 
                `<option value="${cat.category_id}">${cat.category_name}</option>`
            ).join('');
            
            const categoriesList = document.getElementById('categoriesList');
            categoriesList.innerHTML = `
                <table class="table">
                    <tr>
                        <th>ID</th>
                        <th>名称</th>
                        <th>描述</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                    ${data.categories.map(cat => `
                        <tr>
                            <td>${cat.category_id}</td>
                            <td>${cat.category_name}</td>
                            <td>${cat.category_description}</td>
                            <td>${cat.is_hidden == 1 ? '隐藏' : '显示'}</td>
                            <td>
                                <button onclick="toggleCategory(${cat.category_id}, ${cat.is_hidden})">${cat.is_hidden == 1 ? '显示' : '隐藏'}</button>
                                <button onclick="deleteCategory(${cat.category_id})" style="background-color: #ff4d4f;">删除</button>
                            </td>
                        </tr>
                    `).join('')}
                </table>
            `;
        }

        // 加载应用列表
        async function loadApps() {
            const response = await fetch('admin_api.php?action=get_apps');
            const data = await response.json();
            const appsList = document.getElementById('appsList');
            appsList.innerHTML = `
                <table class="table">
                    <tr>
                        <th>ID</th>
                        <th>名称</th>
                        <th>描述</th>
                        <th>下载次数</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                    ${data.apps.map(app => `
                        <tr>
                            <td>${app.app_id}</td>
                            <td>${app.app_name}</td>
                            <td>${app.app_description}</td>
                            <td>${app.download_count}</td>
                            <td>${app.is_hidden == 1 ? '隐藏' : '显示'}</td>
                            <td>
                                <button onclick="toggleApp(${app.app_id}, ${app.is_hidden})">${app.is_hidden == 1 ? '显示' : '隐藏'}</button>
                                <button onclick="deleteApp(${app.app_id})" style="background-color: #ff4d4f;">删除</button>
                            </td>
                        </tr>
                    `).join('')}
                </table>
            `;
        }

        // 添加分类
        document.getElementById('categoryForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const response = await fetch('admin_api.php?action=add_category', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            if (data.success) {
                alert('分类添加成功');
                loadCategories();
                e.target.reset();
            } else {
                alert('添加失败：' + data.error);
            }
        });

        // 添加应用
        document.getElementById('appForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const response = await fetch('admin_api.php?action=add_app', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            if (data.success) {
                alert('应用添加成功');
                loadApps();
                e.target.reset();
            } else {
                alert('添加失败：' + data.error);
            }
        });

        // 切换分类显示/隐藏
        async function toggleCategory(id, currentStatus) {
            const response = await fetch('admin_api.php?action=toggle_category', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ category_id: id, is_hidden: currentStatus ? 0 : 1 })
            });
            const data = await response.json();
            if (data.success) {
                loadCategories();
            }
        }

        // 切换应用显示/隐藏
        async function toggleApp(id, currentStatus) {
            const response = await fetch('admin_api.php?action=toggle_app', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ app_id: id, is_hidden: currentStatus ? 0 : 1 })
            });
            const data = await response.json();
            if (data.success) {
                loadApps();
            }
        }

        // 退出登录
        function logout() {
            fetch('admin_logout.php').then(() => {
                window.location.href = 'admin_login.html';
            });
        }

        // 页面加载时获取数据
        document.addEventListener('DOMContentLoaded', () => {
            loadCategories();
            loadApps();
        });

        // 删除分类
        async function deleteCategory(id) {
            if (!confirm('确定要删除这个分类吗？这将同时删除该分类下的所有应用！')) {
                return;
            }
            const response = await fetch('admin_api.php?action=delete_category', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ category_id: id })
            });
            const data = await response.json();
            if (data.success) {
                loadCategories();
                loadApps();
            }
        }

        // 删除应用
        async function deleteApp(id) {
            if (!confirm('确定要删除这个应用吗？')) {
                return;
            }
            const response = await fetch('admin_api.php?action=delete_app', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ app_id: id })
            });
            const data = await response.json();
            if (data.success) {
                loadApps();
            }
        }
    </script>
</body>
</html> 