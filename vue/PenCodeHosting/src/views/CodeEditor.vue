<template>
  <div class="code-editor-page">
    <!-- 返回按钮 -->
    <button class="back-button" @click="goBack">
      <ArrowLeftIcon class="icon" />
      返回
    </button>

    <!-- 文件信息和操作栏 -->
    <div class="editor-header">
      <div class="file-info">
        <FileIcon class="icon" />
        <span class="file-name">{{ fileName }}</span>
        <span class="file-meta">{{ fileSize }} · {{ uploadTime }}</span>
      </div>
      <div class="actions">
        <button class="save-button" @click="saveChanges">
          <SaveIcon class="icon" />
          保存
        </button>
      </div>
    </div>

    <!-- 代码编辑器 -->
    <div class="editor-container">
      <textarea
        ref="editorRef"
        v-model="code"
        :class="codeLanguage"
        @keydown.tab.prevent="handleTab"
        spellcheck="false"
      ></textarea>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { ArrowLeftIcon, FileIcon, SaveIcon } from 'lucide-vue-next'
import hljs from 'highlight.js'
import 'highlight.js/styles/github-dark.css'

const router = useRouter()
const editorRef = ref(null)
const code = ref('')
const fileName = ref('')
const fileSize = ref('')
const uploadTime = ref('')
const codeLanguage = ref('')
const originalFileData = ref(null)
const fileId = ref(null)

onMounted(() => {
  try {
    const fileData = JSON.parse(localStorage.getItem('editFile'))
    if (!fileData || !fileData.content) {
      alert('无法加载文件内容')
      goBack()
      return
    }

    originalFileData.value = fileData
    fileId.value = fileData.id
    fileName.value = fileData.name
    fileSize.value = fileData.size
    uploadTime.value = fileData.uploadTime
    codeLanguage.value = `language-${fileData.language}`
    code.value = fileData.content
  } catch (error) {
    console.error('Error loading editor:', error)
    alert('无法加载编辑内容')
    goBack()
  }
})

const handleTab = (e) => {
  const start = e.target.selectionStart
  const end = e.target.selectionEnd
  code.value = code.value.substring(0, start) + '  ' + code.value.substring(end)
  e.target.selectionStart = e.target.selectionEnd = start + 2
}

const saveChanges = () => {
  try {
    const uploadedFiles = JSON.parse(localStorage.getItem('uploadedFiles') || '[]')
    
    const updateFileContent = (items) => {
      for (let item of items) {
        if (item.id === fileId.value) {
          item.content = code.value
          if (typeof item.file === 'string') {
            item.file = code.value
          }
          return true
        }
        if (item.children && item.children.length > 0) {
          if (updateFileContent(item.children)) {
            return true
          }
        }
      }
      return false
    }

    if (updateFileContent(uploadedFiles)) {
      localStorage.setItem('uploadedFiles', JSON.stringify(uploadedFiles))
      alert('文件保存成功')
      router.back()
    } else {
      throw new Error('找不到要更新的文件')
    }
  } catch (error) {
    console.error('Save error:', error)
    alert('保存失败：' + error.message)
  }
}

const goBack = () => {
  if (code.value !== originalFileData.value?.content) {
    if (confirm('有未保存的更改，确定要离开吗？')) {
      router.back()
    }
  } else {
    router.back()
  }
}
</script>

<style scoped>
.code-editor-page {
  min-height: 100vh;
  background-color: #0d1117;
  color: #c9d1d9;
  padding: 2rem;
}

.back-button {
  position: fixed;
  top: 1rem;
  left: 1rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  background-color: #21262d;
  color: #c9d1d9;
  border: 1px solid #30363d;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s ease;
  z-index: 100;
}

.back-button:hover {
  background-color: #30363d;
}

.editor-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  background-color: #161b22;
  border: 1px solid #30363d;
  border-radius: 6px 6px 0 0;
  margin-bottom: 1px;
}

.file-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.file-name {
  font-weight: 600;
  font-size: 1.1rem;
}

.file-meta {
  color: #8b949e;
  font-size: 0.875rem;
}

.actions {
  display: flex;
  gap: 0.5rem;
}

.save-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  background-color: #238636;
  color: #ffffff;
  border: 1px solid rgba(240, 246, 252, 0.1);
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.save-button:hover {
  background-color: #2ea043;
}

.editor-container {
  border: 1px solid #30363d;
  border-radius: 0 0 6px 6px;
  overflow: hidden;
}

.editor-container textarea {
  width: 100%;
  min-height: calc(100vh - 200px);
  padding: 1rem;
  background-color: #0d1117;
  color: #c9d1d9;
  border: none;
  font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
  font-size: 0.875rem;
  line-height: 1.5;
  resize: vertical;
  outline: none;
  white-space: pre;
  tab-size: 2;
}

.editor-container textarea:focus {
  border-color: #58a6ff;
}

.icon {
  width: 1.2rem;
  height: 1.2rem;
}
</style> 