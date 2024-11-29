import { defineStore } from 'pinia'

export const useSharedStore = defineStore('shared', {
  state: () => ({
    sharedFiles: [],
    lastUpdate: null,
    isInitialized: false
  }),

  actions: {
    async loadSharedFiles() {
      try {
        await new Promise(resolve => setTimeout(resolve, 100))
        
        const savedFiles = localStorage.getItem('sharedFiles')
        if (savedFiles) {
          this.sharedFiles = JSON.parse(savedFiles)
        }
        const lastUpdate = localStorage.getItem('lastSharedUpdate')
        if (lastUpdate) {
          this.lastUpdate = new Date(lastUpdate)
        }
        this.isInitialized = true
      } catch (error) {
        console.error('Error loading shared files:', error)
      }
    },

    // 管理员发布文件
    publishFiles(files) {
      this.sharedFiles = files
      this.lastUpdate = new Date()
      
      // 保存到 localStorage
      localStorage.setItem('sharedFiles', JSON.stringify(files))
      localStorage.setItem('lastSharedUpdate', this.lastUpdate.toISOString())
    },

    // 清空共享文件
    clearSharedFiles() {
      this.sharedFiles = []
      this.lastUpdate = null
      localStorage.removeItem('sharedFiles')
      localStorage.removeItem('lastSharedUpdate')
    }
  }
}) 