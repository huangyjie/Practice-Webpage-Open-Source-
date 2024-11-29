<template>
  <a-layout-header class="header">
    <div class="header-wrapper">
      <div class="brand">
        <div class="avatar-container">
          <a-avatar
            :size="36"
            :image-url="`https://q1.qlogo.cn/g?b=qq&nk=1401466869&s=100`"
            :style="{
              backgroundColor: 'var(--color-primary)',
              color: '#fff'
            }"
            :trigger-type="['mask']"
            fallback="/default-avatar.png"
          />
        </div>
        <router-link to="/" class="brand-link">小黄</router-link>
      </div>
      
      <a-button 
        class="mobile-menu-btn"
        @click="showMobileMenu = true"
      >
        <icon-menu />
      </a-button>

      <a-menu
        mode="horizontal"
        :selected-keys="[currentRoute]"
        class="nav-menu"
      >
        <a-menu-item key="/">
          <template #icon><icon-home /></template>
          <router-link to="/" class="menu-link">首页</router-link>
        </a-menu-item>
        <a-menu-item key="/about">
          <template #icon><icon-info-circle /></template>
          <router-link to="/about" class="menu-link">关于</router-link>
        </a-menu-item>
        <a-menu-item key="/add" v-if="authStore.user">
          <template #icon><icon-plus /></template>
          <router-link to="/add" class="menu-link">添加笔记</router-link>
        </a-menu-item>
      </a-menu>
      
      <div class="user-actions">
        <a-button
          class="theme-button"
          type="outline" 
          shape="circle"
          @click="toggleTheme"
        >
          <template #icon>
            <icon-moon-fill v-if="isDark" />
            <icon-sun-fill v-else />
          </template>
        </a-button>

        <template v-if="authStore.user">
          <a-dropdown trigger="hover">
            <a-button class="username-btn">
              <a-avatar 
                :size="28"
                :image-url="`https://q1.qlogo.cn/g?b=qq&nk=1401466869&s=100`"
              />
              <span class="username">{{ authStore.user }}</span>
              <icon-down />
            </a-button>
            <template #content>
              <a-doption @click="handleLogout">
                <icon-export />
                退出登录
              </a-doption>
            </template>
          </a-dropdown>
        </template>
        <template v-else>
          <router-link to="/login" class="login-link">
            <a-button type="primary">
              登录
            </a-button>
          </router-link>
        </template>
      </div>
    </div>

    <a-drawer
      :visible="showMobileMenu"
      @cancel="showMobileMenu = false"
      placement="left"
      :width="280"
    >
      <a-menu
        :selected-keys="[currentRoute]"
        @menuItemClick="showMobileMenu = false"
      >
        <a-menu-item key="/">
          <template #icon><icon-home /></template>
          <router-link to="/" class="menu-link">首页</router-link>
        </a-menu-item>
        <a-menu-item key="/about">
          <template #icon><icon-info-circle /></template>
          <router-link to="/about" class="menu-link">关于</router-link>
        </a-menu-item>
        <a-menu-item key="/add" v-if="authStore.user">
          <template #icon><icon-plus /></template>
          <router-link to="/add" class="menu-link">添加笔记</router-link>
        </a-menu-item>
      </a-menu>
    </a-drawer>
  </a-layout-header>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useRouter, useRoute } from 'vue-router'
import { 
  IconDown, 
  IconExport,
  IconPlus,
  IconHome,
  IconInfoCircle,
  IconMenu,
  IconMoonFill,
  IconSunFill
} from '@arco-design/web-vue/es/icon'

const authStore = useAuthStore()
const router = useRouter()
const route = useRoute()
const showMobileMenu = ref(false)
const isDark = ref(false)

const currentRoute = computed(() => route.path)

const handleLogout = () => {
  authStore.logout()
  router.push('/')
}

const toggleTheme = () => {
  isDark.value = !isDark.value
  
  if (isDark.value) {
    document.documentElement.classList.add('dark')
  } else {
    document.documentElement.classList.remove('dark')
  }
  
  localStorage.setItem('theme', isDark.value ? 'dark' : 'light')
}

onMounted(() => {
  const savedTheme = localStorage.getItem('theme')
  if (savedTheme) {
    isDark.value = savedTheme === 'dark'
  } else {
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
    isDark.value = prefersDark
  }

  if (isDark.value) {
    document.documentElement.classList.add('dark')
  } else {
    document.documentElement.classList.remove('dark')
  }

  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
    if (!localStorage.getItem('theme')) {
      isDark.value = e.matches
      if (isDark.value) {
        document.documentElement.classList.add('dark')
      } else {
        document.documentElement.classList.remove('dark')
      }
    }
  })
})
</script>

<style scoped>
.header {
  background-color: var(--color-bg-2);
  border-bottom: 1px solid var(--color-border);
  padding: 0;
  position: sticky;
  top: 0;
  z-index: 100;
  transition: all 0.3s;
}

.header-wrapper {
  max-width: 1200px;
  margin: 0 auto;
  height: 60px;
  display: flex;
  align-items: center;
  padding: 0 20px;
  gap: 20px;
}

.brand {
  display: flex;
  align-items: center;
  gap: 12px;
}

.brand-link {
  color: var(--color-text-1);
  text-decoration: none;
  font-size: 18px;
  font-weight: 600;
  transition: color 0.3s;
}

.brand-link:hover {
  color: var(--color-primary);
}

.nav-menu {
  flex: 1;
  background: transparent;
  border-bottom: none;
}

.menu-link {
  text-decoration: none;
  color: inherit;
}

.user-actions {
  display: flex;
  align-items: center;
  gap: 12px;
}

.username-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 4px 12px;
}

.username {
  margin: 0 4px;
}

.mobile-menu-btn {
  display: none;
}

@media screen and (max-width: 768px) {
  .nav-menu {
    display: none;
  }
  
  .mobile-menu-btn {
    display: block;
    margin-left: auto;
  }
  
  .user-actions {
    margin-left: 0;
  }
  
  .username {
    display: none;
  }
  
  .brand-link {
    font-size: 16px;
  }
}

.header-wrapper {
  animation: slideDown 0.3s ease;
}

@keyframes slideDown {
  from {
    transform: translateY(-100%);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

.theme-button {
  width: 32px;
  height: 32px;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.theme-button:hover {
  background-color: var(--color-fill-2);
  transform: rotate(30deg);
}

.theme-button :deep(.arco-icon) {
  font-size: 16px;
  color: var(--color-text-2);
}

html.dark .theme-button :deep(.arco-icon) {
  color: var(--color-text-1);
}
</style>