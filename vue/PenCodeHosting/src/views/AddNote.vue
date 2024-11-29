<template>
  <div class="github-style-file-explorer">
    <div class="content-layout">
      <!-- 上传区域 -->
      <div class="upload-section">
        <h2 class="section-title">添加文件</h2>
        <div class="upload-controls">
          <!-- 新建文件按钮 -->
          <div class="upload-box">
            <div class="upload-button" @click="showNewFileDialog">
              <FileEditIcon class="icon" />
              <span>新建文件</span>
            </div>
          </div>
          
          <!-- 新建文件夹按钮 -->
          <div class="upload-box">
            <div class="upload-button" @click="showNewFolderDialog">
              <FolderPlusIcon class="icon" />
              <span>新建文件夹</span>
            </div>
          </div>
          
          <!-- 文件上传 -->
          <div class="upload-box">
            <input 
              type="file" 
              multiple 
              @change="handleFileUpload"
              class="file-input"
            >
            <div class="upload-button">
              <PlusIcon class="icon" />
              <span>选择文件</span>
            </div>
          </div>

          <!-- 文件夹上传 -->
          <div class="upload-box">
            <input 
              type="file" 
              webkitdirectory 
              directory
              @change="handleFolderUpload"
              class="file-input"
            >
            <div class="upload-button">
              <FolderIcon class="icon" />
              <span>选择文件夹</span>
            </div>
          </div>
        </div>

        <!-- 文件列表显示 -->
        <transition name="fade">
          <div class="file-list" v-if="fileList.length || folderContent.length">
            <div class="list-header">
              <h3 class="list-title">待上传文件</h3>
              <button class="clear-button" @click="clearFiles">
                <TrashIcon class="icon" />
                <span>清空列表</span>
              </button>
            </div>
            
            <div class="list-container">
              <FileItem 
                v-for="item in [...fileList, ...folderContent]" 
                :key="item.id" 
                :item="item"
                @delete="deleteFile"
                @download="downloadAsZip"
                @move="handleMove"
                @colorChange="handleColorChange"
              />
            </div>

            <!-- 上传按钮 -->
            <div class="submit-section">
              <button 
                class="submit-button" 
                @click="handleSubmit"
                :disabled="isUploading"
              >
                <UploadIcon class="icon" />
                <span>{{ isUploading ? '上传中...' : '开始上传' }}</span>
              </button>
            </div>
          </div>
        </transition>
      </div>

      <!-- 已上传文件卡片 -->
      <div class="uploaded-card">
        <div class="uploaded-header">
          <h2 class="section-title">已上传文件</h2>
          <button 
            v-if="uploadedFiles.length" 
            class="batch-delete-button"
            @click="deleteSelected"
          >
            <TrashIcon class="icon" />
            <span>批量删除</span>
          </button>
        </div>
        <div class="uploaded-files">
          <FileItem 
            v-for="item in uploadedFiles" 
            :key="item.id" 
            :item="item"
            :isSelected="selectedFiles.has(item.id)"
            @select="handleSelect"
            @delete="deleteFile"
            @download="downloadAsZip"
            @move="handleMove"
            @colorChange="handleColorChange"
          />
          <div v-if="!uploadedFiles.length" class="no-files">
            <InboxIcon class="icon" />
            <p>暂无已上传文件</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- 添加新建文件对话框 -->
  <a-modal
    v-model:visible="newFileDialogVisible"
    title="新建文件"
    @ok="createNewFile"
    @cancel="cancelNewFile"
    :mask-closable="false"
  >
    <a-form :model="newFileForm" layout="vertical">
      <a-form-item
        field="name"
        label="文件名称"
        :rules="[
          { required: true, message: '请输入文件名称' },
          { 
            validator: (value) => !value.includes('/') && !value.includes('\\'),
            message: '文件名不能包含斜杠'
          }
        ]"
        :validate-trigger="['change', 'blur']"
      >
        <a-input
          v-model="newFileForm.name"
          placeholder="请输入文件名称（包含后缀，如：example.js）"
          allow-clear
        >
          <template #suffix>
            <a-tooltip content="支持的文件类型：.js, .jsx, .ts, .tsx, .vue, .py, .java, .cpp, .c, .h, .cs, .go, .rb, .php, .swift, .kt, .rs, .sql, .html, .css, .json, .xml, .yaml, .md">
              <icon-info-circle />
            </a-tooltip>
          </template>
        </a-input>
      </a-form-item>
      <a-form-item field="content" label="文件内容">
        <a-textarea
          v-model="newFileForm.content"
          placeholder="请输入文件内容"
          :auto-size="{ minRows: 5, maxRows: 15 }"
        />
      </a-form-item>
    </a-form>
  </a-modal>

  <!-- 添加新建文件夹对话框 -->
  <a-modal
    v-model:visible="newFolderDialogVisible"
    title="新建文件夹"
    @ok="createNewFolder"
    @cancel="cancelNewFolder"
    :mask-closable="false"
  >
    <a-form :model="newFolderForm" layout="vertical">
      <a-form-item
        field="name"
        label="文件夹名称"
        :rules="[{ required: true, message: '请输入文件夹名称' }]"
        :validate-trigger="['change', 'blur']"
      >
        <a-input
          v-model="newFolderForm.name"
          placeholder="请输入文件夹名称"
          allow-clear
        />
      </a-form-item>
      <a-form-item field="color" label="文件夹颜色">
        <a-input-group compact>
          <a-input
            v-model="newFolderForm.color"
            style="width: calc(100% - 40px)"
            placeholder="#e1e4e8"
          />
          <a-button
            style="width: 40px; padding: 0"
          >
            <input
              type="color"
              v-model="newFolderForm.color"
              style="width: 100%; height: 32px; padding: 0; border: none"
            />
          </a-button>
        </a-input-group>
      </a-form-item>
    </a-form>
  </a-modal>

  <!-- 在上传区域添加发布按钮 -->
  <div class="submit-section" v-if="authStore.user === 'admin'">
    <button 
      class="publish-button" 
      @click="publishFiles"
      :disabled="isUploading || !uploadedFiles.length"
    >
      <ShareIcon class="icon" />
      <span>发布到首页</span>
    </button>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { storeToRefs } from 'pinia'
import JSZip from 'jszip'
import { PlusIcon, FolderIcon, TrashIcon, UploadIcon, InboxIcon, FolderPlusIcon, FileEditIcon, ShareIcon } from 'lucide-vue-next'
import FileItem from '../components/FileItem.vue'
import fileDB from '../utils/db'
import { useFileStore } from '../stores/files'
import { Message } from '@arco-design/web-vue'
import { IconInfoCircle } from '@arco-design/web-vue/es/icon'
import { useSharedStore } from '../stores/shared'
import { useAuthStore } from '../stores/auth'

const fileStore = useFileStore()
const { uploadedFiles } = storeToRefs(fileStore)

const fileList = ref([])
const folderContent = ref([])
const isUploading = ref(false)
const selectedFiles = ref(new Map())

onMounted(async () => {
  try {
    await fileDB.init()
    await fileStore.loadFiles()
  } catch (error) {
    console.error('Initialization failed:', error)
  }
})

// 允许的文件类型
const allowedExtensions = [
  '.js', '.jsx', '.ts', '.tsx',  // JavaScript/TypeScript
  '.vue',                        // Vue
  '.py',                         // Python
  '.java',                       // Java
  '.cpp', '.c', '.h',           // C/C++
  '.cs',                         // C#
  '.go',                        // Go
  '.rb',                        // Ruby
  '.php',                       // PHP
  '.swift',                     // Swift
  '.kt',                        // Kotlin
  '.rs',                        // Rust
  '.sql',                       // SQL
  '.html', '.css',              // Web
  '.json', '.xml', '.yaml',     // Data formats
  '.md',                        // Markdown
]

// 过滤文件
const filterFiles = (files) => {
  const validFiles = []
  const invalidFiles = []
  
  files.forEach(file => {
    const ext = '.' + file.name.split('.').pop().toLowerCase()
    if (allowedExtensions.includes(ext)) {
      validFiles.push(file)
    } else {
      invalidFiles.push(file)
    }
  })
  
  if (invalidFiles.length > 0) {
    alert(`已过滤 ${invalidFiles.length} 个非源代码文件:\n${invalidFiles.map(f => f.name).join('\n')}`)
  }
  
  return validFiles
}

// 修改 handleFileUpload 函数
const handleFileUpload = async (event) => {
  const files = Array.from(event.target.files)
  const validFiles = filterFiles(files)
  
  try {
    fileList.value = await Promise.all(validFiles.map(async file => {
      // 读取文件内容
      const content = await readFileContent(file)
      
      return {
        id: Date.now() + Math.random(),
        name: file.name,
        size: formatFileSize(file.size),
        type: 'file',
        uploadTime: new Date().toLocaleString(),
        content: content,  // 存储文件内容
        file: content,    // 同时保存在 file 属性中
        rawFile: file     // 保存原始文件对象
      }
    }))
  } catch (error) {
    console.error('文件上传错误:', error)
    alert('文件上传失败：' + error.message)
  }
}

// 添加文件内容读取函数
const readFileContent = (file) => {
  return new Promise((resolve, reject) => {
    const reader = new FileReader()
    reader.onload = (e) => resolve(e.target.result)
    reader.onerror = (e) => reject(new Error('读取文件失败'))
    reader.readAsText(file)
  })
}

// 修改文件夹上传处理
const handleFolderUpload = async (event) => {
  const files = Array.from(event.target.files)
  const validFiles = filterFiles(files)
  
  if (validFiles.length === 0) {
    alert('文件夹中没有有效的源代码文件')
    return
  }
  
  try {
    const rootFolderName = validFiles[0].webkitRelativePath.split('/')[0]
    
    const folder = {
      id: Date.now() + Math.random(),
      name: rootFolderName,
      type: 'folder',
      uploadTime: new Date().toLocaleString(),
      children: [],
      color: '#e1e4e8'
    }

    // 读取所有文件内容
    for (const file of validFiles) {
      const pathParts = file.webkitRelativePath.split('/')
      let currentLevel = folder.children
      
      // 创建文件夹结构
      for (let i = 1; i < pathParts.length; i++) {
        const part = pathParts[i]
        
        if (i === pathParts.length - 1) {
          // 这是文
          const content = await readFileContent(file)
          currentLevel.push({
            id: Date.now() + Math.random(),
            name: part,
            size: formatFileSize(file.size),
            type: 'file',
            uploadTime: new Date().toLocaleString(),
            content: content,  // 存储文件内容
            file: content,    // 同时保存在 file 属性中
            rawFile: file     // 保存原始文件对象
          })
        } else {
          // 这是文件夹
          let subFolder = currentLevel.find(item => item.name === part && item.type === 'folder')
          if (!subFolder) {
            subFolder = {
              id: Date.now() + Math.random(),
              name: part,
              type: 'folder',
              children: [],
              uploadTime: new Date().toLocaleString(),
              color: '#e1e4e8'
            }
            currentLevel.push(subFolder)
          }
          currentLevel = subFolder.children
        }
      }
    }

    folderContent.value = [folder]
  } catch (error) {
    console.error('文件夹上传错误:', error)
    alert('文件夹上传失败：' + error.message)
  }
}

// Format file size
const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

// 修改 handleSubmit 函数
const handleSubmit = async () => {
  if (fileList.value.length === 0 && folderContent.value.length === 0) {
    alert('请先选择文件或文件夹')
    return
  }

  // 检查文件总大小
  const checkFileSize = (items) => {
    let totalSize = 0
    items.forEach(item => {
      if (item.type === 'file') {
        totalSize += item.content?.length || 0
      } else if (item.type === 'folder' && item.children) {
        totalSize += checkFileSize(item.children)
      }
    })
    return totalSize
  }

  const totalSize = checkFileSize([...fileList.value, ...folderContent.value])
  if (totalSize > 5 * 1024 * 1024) { // 5MB 限制
    alert('文件总大小超过限制，请删除一些文件后重试')
    return
  }

  isUploading.value = true
  try {
    let newFiles = [...fileList.value, ...folderContent.value]
    
    // 记录提交历史
    const commitHistory = JSON.parse(localStorage.getItem('commitHistory') || '[]')
    const today = new Date().toISOString().split('T')[0]
    const existingCommit = commitHistory.find(c => c[0] === today)
    
    if (existingCommit) {
      existingCommit[1]++
    } else {
      commitHistory.push([today, 1])
    }
    
    localStorage.setItem('commitHistory', JSON.stringify(commitHistory))
    
    // 更新文件列表
    await fileStore.saveFiles([...uploadedFiles.value, ...newFiles])
    
    // 清空文件列表和重置状态
    clearFiles()
    alert('文件上传成功！')
  } catch (error) {
    console.error('Upload failed:', error)
    alert('上传失败：' + error.message)
  } finally {
    isUploading.value = false  // 确保在任何情况下都重置上传状态
  }
}

// 修改 clearFiles 函数
const clearFiles = () => {
  fileList.value = []
  folderContent.value = []
  isUploading.value = false  // 确保清空时也重置上传状态
}

// 添加递归保存文件夹内容的函数
const saveFolder = async (folder) => {
  if (folder.children) {
    for (const child of folder.children) {
      if (child.type === 'file') {
        await fileDB.saveFile({
          id: child.id,
          content: child.content
        })
        // 移除文件内容，只保留元数据
        delete child.content
        delete child.file
      } else if (child.type === 'folder') {
        await saveFolder(child)
      }
    }
  }
}

// 修改文件预览和编辑相关的函数
const togglePreview = async (file) => {
  try {
    // 从 IndexedDB 获取文件内容
    const fileData = await fileDB.getFile(file.id)
    if (!fileData) {
      throw new Error('文件内容为空')
    }

    const previewData = {
      name: file.name,
      size: file.size,
      uploadTime: file.uploadTime,
      content: fileData.content,
      language: getFileLanguage(file.name)
    }

    localStorage.setItem('previewFile', JSON.stringify(previewData))
    router.push('/preview')
  } catch (error) {
    console.error('Preview error:', error)
    alert('无法预览文件：' + error.message)
  }
}

const toggleEdit = async (file) => {
  try {
    // 从 IndexedDB 获取文件内容
    const fileData = await fileDB.getFile(file.id)
    if (!fileData) {
      throw new Error('文件内容为空')
    }

    const editData = {
      id: file.id,
      name: file.name,
      size: file.size,
      uploadTime: file.uploadTime,
      content: fileData.content,
      language: getFileLanguage(file.name),
      type: file.type
    }

    localStorage.setItem('editFile', JSON.stringify(editData))
    router.push('/editor')
  } catch (error) {
    console.error('Edit error:', error)
    alert('无法编辑文件：' + error.message)
  }
}

// 修改 getFileLanguage 函数
const getFileLanguage = (filename) => {
  const ext = filename.split('.').pop()?.toLowerCase()
  const languageMap = {
    js: 'javascript',
    jsx: 'javascript',
    ts: 'typescript',
    tsx: 'typescript',
    vue: 'html',
    html: 'html',
    css: 'css',
    json: 'json',
    md: 'markdown',
    py: 'python',
    java: 'java',
    cpp: 'cpp',
    c: 'c',
    h: 'cpp',
    sql: 'sql',
    php: 'php',  // 添加 PHP 语言支持
    xml: 'xml',
    yaml: 'yaml',
    yml: 'yaml',
    go: 'go',
    rb: 'ruby',
    rs: 'rust',
    swift: 'swift',
    kt: 'kotlin',
    cs: 'csharp'
  }
  return languageMap[ext] || 'plaintext'
}

// 修改 deleteFile 函数
const deleteFile = async (fileId) => {
  try {
    await fileStore.deleteFile(fileId)
  } catch (error) {
    console.error('Delete error:', error)
    alert('删除文件失败：' + error.message)
  }
}

// 修改批量删除函数
const deleteSelected = async () => {
  if (selectedFiles.value.size === 0) {
    alert('请先选择要删除的文件')
    return
  }

  if (!confirm(`确定要删除选中的 ${selectedFiles.value.size} 个文件/文件夹吗？`)) {
    return
  }

  try {
    // 删除选中的文件和文件夹
    const deleteItems = async (list) => {
      const newList = [];
      for (const item of list) {
        if (selectedFiles.value.has(item.id)) {
          // 如果是文件，从 IndexedDB 中删除
          if (item.type === 'file') {
            await fileDB.deleteFile(item.id);
          }
          continue;
        }
        if (item.type === 'folder' && item.children) {
          item.children = await deleteItems(item.children);
        }
        newList.push(item);
      }
      return newList;
    }

    // 从所有列表中删除选中的文件
    fileList.value = await deleteItems(fileList.value);
    folderContent.value = await deleteItems(folderContent.value);
    uploadedFiles.value = await deleteItems(uploadedFiles.value);

    // 清空选择
    selectedFiles.value.clear();
    
    // 保存更新后的文件列表
    await fileStore.saveFiles(uploadedFiles.value);
    
    // 清除相关的预览和编辑数据
    localStorage.removeItem('previewFile');
    localStorage.removeItem('editFile');
    
    alert('删除成功');
  } catch (error) {
    console.error('Batch delete error:', error);
    alert('批量删除失败：' + error.message);
  }
}

// 处理文件选择
const handleSelect = (item) => {
  if (selectedFiles.value.has(item.id)) {
    selectedFiles.value.delete(item.id)
  } else {
    selectedFiles.value.set(item.id, item)
    // 如果是文件夹，可以选择是否同时选中其下所有文件
    if (item.type === 'folder' && confirm('是否同时选择文件夹内的所有文件？')) {
      selectAllInFolder(item)
    }
  }
}

// 选择文件夹中的所有文件
const selectAllInFolder = (folder) => {
  if (folder.children) {
    folder.children.forEach(child => {
      selectedFiles.value.set(child.id, child)
      if (child.type === 'folder') {
        selectAllInFolder(child)
      }
    })
  }
}

// 添加移动文件/文件夹的处理函数
const handleMove = ({ itemId, targetFolderId }) => {
  // 在上传列表中查找和移动
  const moveInList = (list, sourceId, targetId) => {
    const item = findAndRemoveItem(list, sourceId)
    if (item) {
      const targetFolder = findFolder(list, targetId)
      if (targetFolder) {
        targetFolder.children = targetFolder.children || []
        targetFolder.children.push(item)
        return true
      }
    }
    return false
  }
  
  // 在已上传文件中查找和移动
  if (!moveInList(fileList.value, itemId, targetFolderId) &&
      !moveInList(folderContent.value, itemId, targetFolderId)) {
    moveInList(uploadedFiles.value, itemId, targetFolderId)
  }
  
  // 保存更新后的文件列表
  fileStore.saveFiles(uploadedFiles.value)
}

// 查找并移除项目
const findAndRemoveItem = (list, id) => {
  for (let i = 0; i < list.length; i++) {
    if (list[i].id === id) {
      return list.splice(i, 1)[0]
    }
    if (list[i].children) {
      const found = findAndRemoveItem(list[i].children, id)
      if (found) return found
    }
  }
  return null
}

// 查找文件夹
const findFolder = (list, id) => {
  for (const item of list) {
    if (item.id === id && item.type === 'folder') {
      return item
    }
    if (item.children) {
      const found = findFolder(item.children, id)
      if (found) return found
    }
  }
  return null
}

// 处理文件��颜色变更
const handleColorChange = ({ folderId, color }) => {
  const updateFolderColor = (items) => {
    for (const item of items) {
      if (item.id === folderId) {
        item.color = color
        return true
      }
      if (item.children && item.children.length > 0) {
        if (updateFolderColor(item.children)) {
          return true
        }
      }
    }
    return false
  }

  // 更新待上传文件列表中的颜色
  folderContent.value.forEach(folder => {
    updateFolderColor([folder])
  })

  // 更新已上传文件列表中的颜色
  uploadedFiles.value.forEach(item => {
    if (item.type === 'folder') {
      updateFolderColor([item])
    }
  })

  // 保存到 localStorage
  fileStore.saveFiles(uploadedFiles.value)
}

// 新建文件相关的状态
const newFileDialogVisible = ref(false)
const newFileForm = ref({
  name: '',
  content: ''
})

// 显示新建文件对话框
const showNewFileDialog = () => {
  newFileDialogVisible.value = true
  newFileForm.value = {
    name: '',
    content: ''
  }
}

// 取消新建文件
const cancelNewFile = () => {
  newFileDialogVisible.value = false
}

// 创建新文件
const createNewFile = () => {
  // 验证文件名
  if (!newFileForm.value.name.trim()) {
    Message.error('请输入文件名称')
    return
  }

  // 验证文件后缀
  const ext = '.' + newFileForm.value.name.split('.').pop().toLowerCase()
  if (!allowedExtensions.includes(ext)) {
    Message.error('不支持的文件类型')
    return
  }

  const newFile = {
    id: Date.now() + Math.random(),
    name: newFileForm.value.name.trim(),
    type: 'file',
    uploadTime: new Date().toLocaleString(),
    content: newFileForm.value.content,
    size: formatFileSize(new Blob([newFileForm.value.content]).size)
  }

  // 添加到待上传列表
  fileList.value.push(newFile)
  
  // 关闭对话框
  newFileDialogVisible.value = false
  Message.success('文件创建成功')
}

// 新建文件夹相关的状态
const newFolderDialogVisible = ref(false)
const newFolderForm = ref({
  name: '',
  color: '#e1e4e8'
})

// 显示新建文件夹对话框
const showNewFolderDialog = () => {
  newFolderDialogVisible.value = true
  newFolderForm.value = {
    name: '',
    color: '#e1e4e8'
  }
}

// 取消新建文件夹
const cancelNewFolder = () => {
  newFolderDialogVisible.value = false
}

// 创建新文件夹
const createNewFolder = () => {
  if (!newFolderForm.value.name.trim()) {
    return
  }

  const newFolder = {
    id: Date.now() + Math.random(),
    name: newFolderForm.value.name.trim(),
    type: 'folder',
    uploadTime: new Date().toLocaleString(),
    children: [],
    color: newFolderForm.value.color
  }

  // 添加到待上传列表
  folderContent.value.push(newFolder)
  
  // 关闭对话框
  newFolderDialogVisible.value = false
}

const sharedStore = useSharedStore()
const authStore = useAuthStore()

// 添加发布功能
const publishFiles = async () => {
  if (!authStore.user || authStore.user !== 'admin') {
    Message.error('只有管理员可以发布文件')
    return
  }

  try {
    // 添加日志
    console.log('Publishing files:', uploadedFiles.value)
    
    await sharedStore.publishFiles(uploadedFiles.value)
    Message.success('文件发布成功')
    
    // 验证发布结果
    console.log('Published files:', sharedStore.sharedFiles)
  } catch (error) {
    console.error('Publish error:', error)
    Message.error('发布失败：' + error.message)
  }
}
</script>

<style scoped>
.github-style-file-explorer {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
  color: #24292e;
  background-color: #f6f8fa;
  min-height: 100vh;
  padding: 2rem;
}

.content-layout {
  display: flex;
  flex-direction: column;
  gap: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.upload-section {
  background: white;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
  padding: 2rem;
}

.uploaded-card {
  background: white;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
  padding: 2rem;
}

.uploaded-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.section-title {
  font-size: 1.5rem;
  font-weight: 600;
  margin-bottom: 1.5rem;
  color: #2d3748;
}

.upload-controls {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
  margin-bottom: 2rem;
}

.upload-box {
  position: relative;
  border: 2px dashed #e2e8f0;
  border-radius: 8px;
  padding: 2rem 1rem;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s ease;
}

.upload-box:hover {
  border-color: #4299e1;
  background-color: #ebf8ff;
}

.file-input {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  opacity: 0;
  cursor: pointer;
}

.upload-button {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  color: #4a5568;
  font-weight: 500;
}

.upload-button .icon {
  width: 2rem;
  height: 2rem;
  color: #4299e1;
}

.file-list {
  margin-top: 2rem;
}

.list-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.list-title {
  font-size: 1.2rem;
  font-weight: 600;
  color: #2d3748;
}

.clear-button, .batch-delete-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  background-color: #fed7d7;
  color: #c53030;
  border: none;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.clear-button:hover, .batch-delete-button:hover {
  background-color: #feb2b2;
}

.list-container {
  background: #f7fafc;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.06);
}

.file-entry {
  padding-left: calc(var(--level, 0) * 1.5rem);
  border-bottom: 1px solid #e2e8f0;
}

.file-header {
  display: grid;
  grid-template-columns: auto 1fr auto auto auto;
  align-items: center;
  padding: 0.75rem 1rem;
  gap: 1rem;
  transition: background-color 0.2s ease;
}

.file-header:hover {
  background-color: #edf2f7;
}

.file-checkbox {
  width: 1.2rem;
  height: 1.2rem;
  border-radius: 3px;
  border: 2px solid #cbd5e0;
  appearance: none;
  cursor: pointer;
  transition: all 0.2s ease;
}

.file-checkbox:checked {
  background-color: #4299e1;
  border-color: #4299e1;
}

.file-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.file-name {
  font-weight: 500;
  color: #2d3748;
}

.file-meta, .upload-time {
  font-size: 0.875rem;
  color: #718096;
}

.action-buttons {
  display: flex;
  gap: 0.5rem;
  opacity: 0;
  transition: opacity 0.2s ease;
}

.file-header:hover .action-buttons {
  opacity: 1;
}

.action-button {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  padding: 0.25rem 0.5rem;
  border: none;
  border-radius: 4px;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.download-button {
  background-color: #ebf8ff;
  color: #3182ce;
}

.download-button:hover {
  background-color: #bee3f8;
}

.delete-button {
  background-color: #fed7d7;
  color: #c53030;
}

.delete-button:hover {
  background-color: #feb2b2;
}

.submit-section {
  margin-top: 2rem;
  text-align: center;
}

.submit-button {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 2rem;
  background-color: #4299e1;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 6px rgba(66, 153, 225, 0.5);
}

.submit-button:hover:not(:disabled) {
  background-color: #3182ce;
  transform: translateY(-2px);
  box-shadow: 0 6px 8px rgba(66, 153, 225, 0.6);
}

.submit-button:disabled {
  background-color: #a0aec0;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

.uploaded-files {
  border: 1px solid #e1e4e8;
  border-radius: 6px;
  overflow: hidden;
}

.no-files {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem;
  background: #f7fafc;
  border-radius: 8px;
  color: #a0aec0;
  font-size: 1.1rem;
  text-align: center;
}

.no-files .icon {
  width: 4rem;
  height: 4rem;
  margin-bottom: 1rem;
}

.folder-content {
  padding-left: 1.5rem;
  border-left: 2px solid #e2e8f0;
}

/* Transitions */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.list-enter-active,
.list-leave-active {
  transition: all 0.3s ease;
}

.list-enter-from,
.list-leave-to {
  opacity: 0;
  transform: translateX(30px);
}

.list-move {
  transition: transform 0.3s ease;
}

/* 新建文件按钮特殊样式 */
.upload-box:first-child,
.upload-box:nth-child(2) {
  border-style: solid;
  background-color: #f7fafc;
}

.upload-box:first-child:hover,
.upload-box:nth-child(2):hover {
  background-color: #edf2f7;
  border-color: #4299e1;
}

/* 文件名输入框后缀图标样式 */
:deep(.arco-input-suffix) {
  color: var(--color-text-3);
  cursor: help;
}

:deep(.arco-input-suffix .arco-icon) {
  font-size: 16px;
}

/* 文本域样式优化 */
:deep(.arco-textarea) {
  resize: vertical;
  font-family: monospace;
}

.publish-button {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 2rem;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-left: 1rem;
}

.publish-button:hover:not(:disabled) {
  background-color: #45a049;
  transform: translateY(-2px);
}

.publish-button:disabled {
  background-color: #a0aec0;
  cursor: not-allowed;
  transform: none;
}
</style>