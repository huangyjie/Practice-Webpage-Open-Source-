<template>
  <div class="login-container">
    <div class="layui-card">
      <div class="layui-card-header">
        {{ isRegisterMode ? '注册' : '登录' }}
      </div>
      <div class="layui-card-body">
        <form class="layui-form">
          <div class="layui-form-item">
            <label class="layui-form-label">用户名</label>
            <div class="layui-input-block">
              <input 
                type="text" 
                v-model="username"
                placeholder="请输入用户名" 
                class="layui-input"
              >
            </div>
          </div>
          <div class="layui-form-item">
            <label class="layui-form-label">QQ号</label>
            <div class="layui-input-block">
              <input 
                type="text" 
                v-model="qqNumber"
                placeholder="请输入QQ号（用于显示头像）" 
                class="layui-input"
              >
            </div>
          </div>
          <div class="layui-form-item">
            <label class="layui-form-label">密码</label>
            <div class="layui-input-block">
              <input 
                type="password" 
                v-model="password"
                placeholder="请输入密码" 
                class="layui-input"
              >
            </div>
          </div>
          
          <!-- 注册模式下显示确认密码 -->
          <div class="layui-form-item" v-if="isRegisterMode">
            <label class="layui-form-label">确认密码</label>
            <div class="layui-input-block">
              <input 
                type="password" 
                v-model="confirmPassword"
                placeholder="请再次输入密码" 
                class="layui-input"
              >
            </div>
          </div>

          <div class="layui-form-item">
            <div class="layui-input-block button-group">
              <button 
                type="button" 
                class="layui-btn layui-btn-normal"
                @click="handleSubmit"
                :disabled="isLoading"
              >
                {{ isLoading ? '处理中...' : (isRegisterMode ? '注册' : '登录') }}
              </button>
              
              <a-button 
                type="text" 
                @click="toggleMode"
                :disabled="isLoading"
              >
                {{ isRegisterMode ? '已有账号？去登录' : '没有账号？去注册' }}
              </a-button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { Message } from '@arco-design/web-vue'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const username = ref('')
const password = ref('')
const qqNumber = ref('')
const confirmPassword = ref('')
const isLoading = ref(false)
const isRegisterMode = ref(false)

const toggleMode = () => {
  isRegisterMode.value = !isRegisterMode.value
  username.value = ''
  password.value = ''
  confirmPassword.value = ''
  qqNumber.value = ''
}

const handleSubmit = async () => {
  if (!username.value || !password.value) {
    Message.error('请输入用户名和密码')
    return
  }

  if (isRegisterMode.value) {
    await handleRegister()
  } else {
    await handleLogin()
  }
}

const handleRegister = async () => {
  if (password.value !== confirmPassword.value) {
    Message.error('两次输入的密码不一致')
    return
  }

  isLoading.value = true
  try {
    await authStore.register({
      username: username.value,
      password: password.value,
      qqNumber: qqNumber.value,
      registerTime: new Date().toISOString()
    })
    
    Message.success('注册成功，请登录')
    isRegisterMode.value = false
    password.value = ''
  } catch (error) {
    Message.error(error.message)
  } finally {
    isLoading.value = false
  }
}

const handleLogin = async () => {
  isLoading.value = true
  try {
    if (authStore.validateLogin(username.value, password.value)) {
      // 设置QQ头像
      const avatar = qqNumber.value ? 
        `https://q1.qlogo.cn/g?b=qq&nk=${qqNumber.value}&s=100` :
        `https://q1.qlogo.cn/g?b=qq&nk=10001&s=100`
      
      localStorage.setItem('avatar', avatar)
      await authStore.login(username.value)
      
      Message.success('登录成功')
      const redirectPath = route.query.redirect || '/'
      await router.push(redirectPath)
    } else {
      Message.error('用户名或密码错误')
    }
  } catch (error) {
    console.error('Login error:', error)
    Message.error('登录失败：' + error.message)
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: calc(100vh - 120px);
  padding: 20px;
}

.layui-card {
  width: 100%;
  max-width: 400px;
  background: var(--color-bg-2);
  border-radius: 8px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.layui-card-header {
  font-size: 24px;
  font-weight: bold;
  text-align: center;
  padding: 20px;
  color: var(--color-text-1);
  border-bottom: 1px solid var(--color-border);
}

.layui-form {
  padding: 20px;
}

.button-group {
  display: flex;
  flex-direction: column;
  gap: 10px;
  align-items: center;
}

.layui-btn {
  width: 100%;
  height: 40px;
  font-size: 16px;
}

:deep(.arco-btn) {
  font-size: 14px;
}
</style> 