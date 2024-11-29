import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    isAuthenticated: false,
    users: []
  }),
  
  actions: {
    login(username) {
      this.user = username
      this.isAuthenticated = true
      localStorage.setItem('user', username)
    },
    
    logout() {
      this.user = null
      this.isAuthenticated = false
      localStorage.removeItem('user')
    },
    
    checkAuth() {
      const user = localStorage.getItem('user')
      if (user) {
        this.user = user
        this.isAuthenticated = true
      }
    },
    
    register(userData) {
      const users = JSON.parse(localStorage.getItem('users') || '[]')
      if (users.some(user => user.username === userData.username)) {
        throw new Error('用户名已存在')
      }
      
      users.push(userData)
      localStorage.setItem('users', JSON.stringify(users))
      this.users = users
    },
    
    validateLogin(username, password) {
      const users = JSON.parse(localStorage.getItem('users') || '[]')
      const user = users.find(u => u.username === username && u.password === password)
      
      if (username === 'admin' && password === 'admin123') {
        return true
      }
      
      return !!user
    },
    
    loadUsers() {
      const users = JSON.parse(localStorage.getItem('users') || '[]')
      this.users = users
    }
  },
  
  persist: true
}) 