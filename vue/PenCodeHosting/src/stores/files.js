import { defineStore } from 'pinia'

export const useFileStore = defineStore('files', {
  state: () => ({
    uploadedFiles: []
  }),
  
  actions: {
    async loadFiles() {
      try {
        const savedFiles = localStorage.getItem('uploadedFiles')
        if (savedFiles) {
          this.uploadedFiles = JSON.parse(savedFiles)
        }
      } catch (error) {
        console.error('Error loading files:', error)
      }
    },
    
    async saveFiles(files) {
      try {
        this.uploadedFiles = files
        localStorage.setItem('uploadedFiles', JSON.stringify(files))
      } catch (error) {
        console.error('Error saving files:', error)
      }
    },
    
    async deleteFile(fileId) {
      try {
        const deleteFromList = (list) => {
          for (let i = 0; i < list.length; i++) {
            if (list[i].id === fileId) {
              list.splice(i, 1)
              return true
            }
            if (list[i].children) {
              if (deleteFromList(list[i].children)) {
                return true
              }
            }
          }
          return false
        }
        
        deleteFromList(this.uploadedFiles)
        await this.saveFiles(this.uploadedFiles)
      } catch (error) {
        console.error('Error deleting file:', error)
        throw error
      }
    }
  },
  
  persist: true
}) 