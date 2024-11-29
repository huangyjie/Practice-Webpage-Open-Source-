import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: () => import('../views/HomeView.vue')
    },
    {
      path: '/about',
      name: 'about',
      component: () => import('../views/AboutView.vue')
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('../views/LoginView.vue')
    },
    {
      path: '/add',
      name: 'add',
      component: () => import('../views/AddNote.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/preview',
      name: 'preview',
      component: () => import('../views/CodePreview.vue')
    },
    {
      path: '/editor',
      name: 'editor',
      component: () => import('../views/CodeEditor.vue'),
      meta: { requiresAuth: true }
    }
  ]
})

// 导航守卫
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({
      path: '/login',
      query: { redirect: to.fullPath }
    })
  } else {
    next()
  }
})

export default router 