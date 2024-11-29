<script setup>
import { onMounted } from 'vue'
import NavBar from './components/NavBar.vue'
import { useAuthStore } from './stores/auth'

const authStore = useAuthStore()

onMounted(() => {
  authStore.checkAuth()
})
</script>

<template>
  <div class="app-container">
    <NavBar />
    <div class="main-container">
      <div class="content-container">
        <router-view v-slot="{ Component }">
          <transition 
            name="fade" 
            mode="out-in"
            appear
          >
            <div class="router-view-container">
              <component :is="Component" />
            </div>
          </transition>
        </router-view>
      </div>
    </div>
    <footer class="app-footer">
      © 2024 小黄 - Vue3 + Arco Design
    </footer>
  </div>
</template>

<style>
/* 亮色主题变量 */
:root {
  /* Arco Design 变量覆盖 */
  --color-menu-light-bg: var(--color-bg-2);
  --color-bg-white: var(--color-bg-2);
  --color-neutral-1: var(--color-bg-1);
  --color-neutral-2: var(--color-bg-2);
  --color-neutral-3: var(--color-fill-1);
  --color-neutral-4: var(--color-fill-2);
  --color-border: var(--color-border);
  --color-text-1: var(--color-text-1);
  --color-text-2: var(--color-text-2);
  --color-text-3: var(--color-text-3);
  --color-text-4: var(--color-text-3);

  /* 自定义主题变量 */
  --color-primary: #165DFF;
  --color-success: #00B42A;
  --color-warning: #FF7D00;
  --color-danger: #F53F3F;
  
  --color-bg-1: #f5f7fa;
  --color-bg-2: #ffffff;
  --color-border: #e5e6eb;
  
  --color-text-1: #1d2129;
  --color-text-2: #4e5969; 
  --color-text-3: #86909c;
  
  --color-fill-1: #f2f3f5;
  --color-fill-2: #f7f8fa;
  
  --shadow-1: 0 4px 8px rgba(0, 0, 0, 0.08);
  --shadow-2: 0 8px 16px rgba(0, 0, 0, 0.12);
}

/* 暗色主题变量 */
html.dark {
  /* Arco Design 暗色主题变量覆盖 */
  --color-menu-dark-bg: var(--color-bg-2);
  --color-bg-white: var(--color-bg-2);
  --color-neutral-1: var(--color-bg-1);
  --color-neutral-2: var(--color-bg-2);
  --color-neutral-3: var(--color-fill-1);
  --color-neutral-4: var(--color-fill-2);
  --color-border: var(--color-border);
  --color-text-1: var(--color-text-1);
  --color-text-2: var(--color-text-2);
  --color-text-3: var(--color-text-3);
  --color-text-4: var(--color-text-3);

  /* 自定义暗色主题变量 */
  --color-primary: #3C7EFF;
  --color-success: #27C346;
  --color-warning: #FF9A2E;
  --color-danger: #F76965;
  
  --color-bg-1: #17171a;
  --color-bg-2: #232324;
  --color-border: #333335;
  
  --color-text-1: rgba(255, 255, 255, 0.82);
  --color-text-2: rgba(255, 255, 255, 0.6);
  --color-text-3: rgba(255, 255, 255, 0.4);
  
  --color-fill-1: #28282a;
  --color-fill-2: #1f1f1f;
  
  --shadow-1: 0 4px 8px rgba(0, 0, 0, 0.2);
  --shadow-2: 0 8px 16px rgba(0, 0, 0, 0.3);
}

/* 基础样式 */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  transition: background-color 0.3s, border-color 0.3s, color 0.3s, box-shadow 0.3s;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial,
    'Noto Sans', sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol',
    'Noto Color Emoji';
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  background-color: var(--color-bg-1);
  color: var(--color-text-1);
}

/* 应用容器样式 */
.app-container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.main-container {
  flex: 1;
  padding: 20px;
  background: linear-gradient(
    135deg,
    var(--color-bg-2) 0%,
    var(--color-bg-1) 100%
  );
}

.content-container {
  width: 100%;
  max-width: 1400px;
  margin: 0 auto;
  background-color: var(--color-bg-2);
  border-radius: 8px;
  box-shadow: var(--shadow-1);
  padding: 20px;
  min-height: calc(100vh - 120px - 61px);
}

.app-footer {
  text-align: center;
  padding: 20px;
  background-color: var(--color-bg-2);
  color: var(--color-text-2);
  border-top: 1px solid var(--color-border);
}

/* Arco Design 组件主题覆盖 */
.arco-btn {
  background-color: var(--color-bg-2);
  border-color: var(--color-border);
  color: var(--color-text-1);
}

.arco-btn-primary {
  background-color: var(--color-primary);
  border-color: var(--color-primary);
  color: #fff;
}

.arco-menu {
  background-color: var(--color-bg-2);
}

.arco-menu-light .arco-menu-item {
  color: var(--color-text-1);
}

.arco-menu-light .arco-menu-item:hover {
  color: var(--color-primary);
}

.arco-card {
  background-color: var(--color-bg-2);
  border-color: var(--color-border);
}

/* 过渡动画 */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(10px);
}

/* 响应式调整 */
@media screen and (max-width: 1440px) {
  .content-container {
    max-width: 100%;
  }
}

@media screen and (max-width: 768px) {
  .main-container {
    padding: 10px;
  }
  
  .content-container {
    padding: 15px;
    border-radius: 0;
  }
}
</style>
