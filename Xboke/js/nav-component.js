class NavBar {
    constructor(containerId = 'header-container') {
        this.containerId = containerId;
        this.container = document.getElementById(containerId);
        console.log('NavBar initialized, container:', this.container);
    }

    async loadNav(activePage) {
        try {
            console.log('Loading nav for page:', activePage);
            const response = await fetch(`/api/nav.php?active=${activePage}`);
            const data = await response.json();
            console.log('Nav data received:', data);
            this.renderNav(data);
        } catch (error) {
            console.error('加载导航栏失败:', error);
        }
    }

    renderNav(data) {
        const navHtml = `
            <header>
                <div class="container">
                    <div id="navbar">
                        <h1 class="fade-in"><span class="highlight">我的</span> 博客</h1>
                        <nav>
                            <ul>
                                ${data.menu_items.map(item => `
                                    <li class="fade-in ${item.active ? 'current' : ''}">
                                        <a href="${item.url}" 
                                           ${item.title ? `title="${item.title}"` : ''}>
                                            ${item.text}
                                        </a>
                                    </li>
                                `).join('')}
                            </ul>
                        </nav>
                    </div>
                </div>
            </header>
        `;
        this.container.innerHTML = navHtml;
    }

    initScrollEffect() {
        window.addEventListener('scroll', () => {
            const header = this.container.querySelector('header');
            if (window.scrollY > 50 && !this.isScrolled) {
                header.style.backgroundColor = 'rgba(53, 66, 74, 0.95)';
                header.style.boxShadow = '0 2px 5px rgba(0,0,0,0.1)';
                this.isScrolled = true;
            } else if (window.scrollY <= 50 && this.isScrolled) {
                header.style.backgroundColor = 'rgba(53, 66, 74, 0.9)';
                header.style.boxShadow = 'none';
                this.isScrolled = false;
            }
        });
    }
} 