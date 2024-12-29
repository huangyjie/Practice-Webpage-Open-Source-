class HeaderLoader {
    static async loadHeader(activePage) {
        try {
            const response = await fetch('/includes/header-html.php?active=' + activePage);
            const headerHtml = await response.text();
            document.getElementById('header-container').innerHTML = headerHtml;
        } catch (error) {
            console.error('加载头部失败:', error);
        }
    }
} 