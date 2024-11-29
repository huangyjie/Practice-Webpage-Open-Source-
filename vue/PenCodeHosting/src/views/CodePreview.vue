<template>
  <div class="code-preview">
    <!-- 返回按钮 -->
    <button class="back-button" @click="goBack">
      <ArrowLeftIcon class="icon" />
      返回
    </button>

    <!-- 文件信息 -->
    <div class="file-info">
      <div class="file-name">
        <FileIcon class="icon" />
        {{ fileName }}
      </div>
      <div class="file-meta">
        <span>{{ fileSize }}</span>
        <span>{{ uploadTime }}</span>
      </div>
    </div>

    <!-- 代码预览区域 -->
    <div class="code-container">
      <pre><code :class="codeLanguage" v-html="highlightedCode"></code></pre>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { ArrowLeftIcon, FileIcon } from 'lucide-vue-next'
import hljs from 'highlight.js'
import 'highlight.js/styles/github-dark.css'

const router = useRouter()
const route = useRoute()

const fileName = ref('')
const fileSize = ref('')
const uploadTime = ref('')
const highlightedCode = ref('')
const codeLanguage = ref('')

onMounted(() => {
  try {
    const fileData = JSON.parse(localStorage.getItem('previewFile'))
    if (!fileData) {
      alert('没有可预览的文件')
      goBack()
      return
    }

    fileName.value = fileData.name
    fileSize.value = fileData.size
    uploadTime.value = fileData.uploadTime
    
    // 更新语言映射，确保支持 PHP
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
      php: 'php',  // 确保这一行存在
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
    
    // 设置语言类和高亮
    const ext = fileData.name.split('.').pop()?.toLowerCase()
    const language = languageMap[ext] || 'plaintext'
    codeLanguage.value = `language-${language}`
    
    // 处理代码高亮
    if (fileData.content) {
      // 确保 highlight.js 加载了 PHP 语言支持
      highlightedCode.value = hljs.highlight(fileData.content, {
        language: language
      }).value
    } else {
      highlightedCode.value = '文件内容为空'
    }
  } catch (error) {
    console.error('Error loading preview:', error)
    alert('无法加载预览内容：' + error.message)
    goBack()
  }
})

const goBack = () => {
  router.back()
}
</script>

<style scoped>
.code-preview {
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

.back-button .icon {
  width: 1.2rem;
  height: 1.2rem;
}

.file-info {
  margin-bottom: 2rem;
  padding: 1rem;
  background-color: #161b22;
  border: 1px solid #30363d;
  border-radius: 6px;
}

.file-name {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 1.2rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.file-meta {
  display: flex;
  gap: 1rem;
  color: #8b949e;
  font-size: 0.875rem;
}

.code-container {
  background-color: #0d1117;
  border: 1px solid #30363d;
  border-radius: 6px;
  overflow: hidden;
}

.code-container pre {
  margin: 0;
  padding: 1rem;
}

.code-container code {
  font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
  font-size: 0.875rem;
  line-height: 1.5;
}

:deep(.hljs) {
  background-color: #0d1117;
  padding: 0;
}
</style> 