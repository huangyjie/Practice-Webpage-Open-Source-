import axios from 'axios'

// 创建 axios 实例
const instance = axios.create({
  baseURL: `http://localhost:${process.env.PORT || 3000}/api`,
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json'
  }
})

// 添加响应拦截器
instance.interceptors.response.use(
  response => response.data,
  error => {
    console.error('API Error:', error)
    if (error.code === 'ECONNABORTED') {
      throw new Error('请求超时，请检查网络连接')
    }
    if (!error.response) {
      throw new Error('无法连接到服务器，请确保服务器已启动')
    }
    throw error
  }
)

export const api = {
  async saveFile(file) {
    try {
      return await instance.post('/files', file)
    } catch (error) {
      console.error('Save file error:', error)
      throw new Error(`保存文件失败: ${error.message}`)
    }
  },

  async getFiles() {
    try {
      return await instance.get('/files')
    } catch (error) {
      console.error('Get files error:', error)
      throw new Error(`获取文件列表失败: ${error.message}`)
    }
  },

  async deleteFile(id) {
    try {
      return await instance.delete(`/files/${id}`)
    } catch (error) {
      console.error('Delete file error:', error)
      throw new Error(`删除文件失败: ${error.message}`)
    }
  }
} 