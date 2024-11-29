<template>
  <div 
    class="file-item" 
    :class="{ 
      'is-folder': item.type === 'folder',
      'is-dragging': isDragging,
      'is-drag-over': isDragOver,
      'is-selected': isSelected
    }"
    draggable="true"
    @dragstart="handleDragStart"
    @dragend="handleDragEnd"
    @dragover.prevent="handleDragOver"
    @dragleave.prevent="handleDragLeave"
    @drop.prevent="handleDrop"
  >
    <div class="file-header" @click.stop="handleClick" :style="folderStyle">
      <input 
        type="checkbox"
        class="file-checkbox"
        :checked="isSelected"
        @click.stop="$emit('select', item)"
        @change.stop
      >
      <div class="file-info">
        <component 
          :is="item.type === 'folder' ? (isExpanded ? ChevronDownIcon : ChevronRightIcon) : FileIcon"
          class="icon"
        />
        <span class="file-name">{{ item.name }}</span>
        <input
          v-if="item.type === 'folder' && level === 0"
          type="color"
          class="color-picker"
          :value="folderColor"
          @click.stop
          @input="updateFolderColor"
        >
      </div>
      <span class="file-meta">
        {{ item.type === 'folder' ? `${item.children?.length || 0} 个项目` : item.size }}
      </span>
      <span class="upload-time">{{ item.uploadTime }}</span>
      <div class="action-buttons">
        <template v-if="item.type === 'file'">
          <button 
            v-if="isPreviewable"
            class="action-button preview-button"
            @click.stop="togglePreview"
          >
            <EyeIcon class="icon" />
            <span>预览</span>
          </button>
          <button 
            v-if="isEditable"
            class="action-button edit-button"
            @click.stop="toggleEdit"
          >
            <EditIcon class="icon" />
            <span>编辑</span>
          </button>
        </template>
        <button 
          class="action-button download-button"
          @click.stop="downloadRecursively(item)"
        >
          <DownloadIcon class="icon" />
          <span>下载</span>
        </button>
        <button 
          class="action-button delete-button"
          @click.stop="deleteRecursively(item.id)"
        >
          <TrashIcon class="icon" />
          <span>删除</span>
        </button>
      </div>
    </div>

    <div v-if="isEditing" class="file-editor">
      <div class="editor-header">
        <span>编辑文件</span>
        <div class="editor-actions">
          <button class="save-button" @click="saveChanges">
            <SaveIcon class="icon" />
            保存
          </button>
          <button class="cancel-button" @click="cancelEdit">
            <XIcon class="icon" />
            取消
          </button>
        </div>
      </div>
      <textarea
        v-model="editContent"
        class="code-editor"
        :class="previewLanguage"
        @keydown.tab.prevent="handleTab"
      ></textarea>
    </div>
    
    <div v-if="showPreview && !isEditing" class="file-preview">
      <div class="preview-header">
        <span>文件预览</span>
        <button class="close-button" @click="showPreview = false">
          <XIcon class="icon" />
        </button>
      </div>
      <pre><code :class="previewLanguage" v-html="highlightedContent"></code></pre>
    </div>

    <div v-if="item.type === 'folder'" class="folder-content" :class="{ 'expanded': isExpanded }">
      <FileItem 
        v-for="child in item.children"
        :key="child.id"
        :item="child"
        :level="level + 1"
        @select="handleSelect"
        @delete="handleDelete"
        @download="handleDownload"
        @move="handleMove"
        @colorChange="handleColorChange"
      />
    </div>

    <div v-if="isDragOver" class="drag-indicator">
      <div class="drag-line"></div>
      <span class="drag-text">放置到此处</span>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { FileIcon, FolderIcon, TrashIcon, DownloadIcon, ChevronRightIcon, ChevronDownIcon, EyeIcon, EditIcon, SaveIcon, XIcon } from 'lucide-vue-next'
import hljs from 'highlight.js'
import 'highlight.js/styles/github-dark.css'

const props = defineProps({
  item: {
    type: Object,
    required: true
  },
  level: {
    type: Number,
    default: 0
  },
  isSelected: {
    type: Boolean,
    default: false
  }
})

const isExpanded = ref(false)
const showPreview = ref(false)
const isEditing = ref(false)
const editContent = ref('')
const folderColor = ref(props.item.color || '#e1e4e8')

const folderStyle = computed(() => {
  if (props.item.type === 'folder') {
    if (props.level === 0) {
      return {
        'background-color': `${folderColor.value}22`,
        'border-left': `3px solid ${folderColor.value}`
      }
    } else {
      return {
        'background-color': '#f6f8fa',
        'border-left': '3px solid #e1e4e8'
      }
    }
  }
  return {}
})

const updateFolderColor = (event) => {
  if (props.level === 0) {
    const newColor = event.target.value
    folderColor.value = newColor
    emit('colorChange', { 
      folderId: props.item.id, 
      color: newColor 
    })
  }
}

const isEditable = computed(() => {
  if (props.item.type !== 'file') return false
  const editableExtensions = [
    '.txt', '.js', '.jsx', '.ts', '.tsx', '.vue', 
    '.css', '.html', '.json', '.md', '.py', 
    '.java', '.cpp', '.c', '.h', '.sql',
    '.php',  // 添加 PHP 扩展名
    '.xml', '.yaml', '.yml',
    '.go', '.rb', '.rs',
    '.swift', '.kt', '.cs'
  ]
  return editableExtensions.some(ext => 
    props.item.name.toLowerCase().endsWith(ext)
  )
})

const isPreviewable = computed(() => {
  if (props.item.type !== 'file') return false
  const previewableExtensions = [
    '.txt', '.js', '.jsx', '.ts', '.tsx', '.vue', 
    '.css', '.html', '.json', '.md', '.py', 
    '.java', '.cpp', '.c', '.h', '.sql',
    '.php',  // 添加 PHP 扩展名
    '.xml', '.yaml', '.yml',
    '.go', '.rb', '.rs',
    '.swift', '.kt', '.cs'
  ]
  return previewableExtensions.some(ext => 
    props.item.name.toLowerCase().endsWith(ext)
  )
})

const previewLanguage = computed(() => {
  const ext = props.item.name.split('.').pop()?.toLowerCase()
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
    sql: 'sql'
  }
  return `language-${languageMap[ext] || 'plaintext'}`
})

const highlightedContent = computed(() => {
  if (!props.item.content) return ''
  return hljs.highlight(props.item.content, {
    language: previewLanguage.value.replace('language-', '')
  }).value
})

const handleClick = () => {
  if (props.item.type === 'folder') {
    isExpanded.value = !isExpanded.value
  }
}

const togglePreview = async () => {
  try {
    const content = props.item.content
    
    if (!content) {
      throw new Error('文件内容为空')
    }

    const ext = props.item.name.split('.').pop()?.toLowerCase()
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
      sql: 'sql'
    }

    localStorage.setItem('previewFile', JSON.stringify({
      name: props.item.name,
      size: props.item.size,
      uploadTime: props.item.uploadTime,
      content: content,
      language: languageMap[ext] || 'plaintext'
    }))

    router.push('/preview')
  } catch (error) {
    console.error('Preview error:', error)
    alert('无法预览文件：' + error.message)
  }
}

const toggleEdit = async () => {
  try {
    const content = props.item.content
    
    if (!content) {
      throw new Error('文件内容为空')
    }

    localStorage.setItem('editFile', JSON.stringify({
      id: props.item.id,
      name: props.item.name,
      size: props.item.size,
      uploadTime: props.item.uploadTime,
      content: content,
      language: previewLanguage.value.replace('language-', ''),
      type: props.item.type
    }))
    
    router.push('/editor')
  } catch (error) {
    console.error('Edit error:', error)
    alert('无法编辑文件：' + error.message)
  }
}

const saveChanges = () => {
  props.item.content = editContent.value
  isEditing.value = false
}

const cancelEdit = () => {
  editContent.value = props.item.content
  isEditing.value = false
}

const handleTab = (e) => {
  const start = e.target.selectionStart
  const end = e.target.selectionEnd
  editContent.value = editContent.value.substring(0, start) + '  ' + editContent.value.substring(end)
  e.target.selectionStart = e.target.selectionEnd = start + 2
}

const readFileContent = (file) => {
  return new Promise((resolve, reject) => {
    if (!(file instanceof File)) {
      reject(new Error('无效的文件对象'));
      return;
    }
    
    const reader = new FileReader();
    reader.onload = (e) => resolve(e.target.result);
    reader.onerror = (e) => reject(new Error('读取文件失败'));
    reader.readAsText(file);
  });
};

const emit = defineEmits([
  'select', 
  'delete', 
  'download', 
  'move', 
  'colorChange'
])

const isDragging = ref(false)
const isDragOver = ref(false)

const handleDragStart = (e) => {
  isDragging.value = true
  e.dataTransfer.setData('text/plain', JSON.stringify({
    id: props.item.id,
    type: props.item.type,
    name: props.item.name
  }))
}

const handleDragEnd = () => {
  isDragging.value = false
}

const handleDragOver = (e) => {
  if (props.item.type === 'folder') {
    isDragOver.value = true
  }
}

const handleDragLeave = () => {
  isDragOver.value = false
}

const handleDrop = (e) => {
  isDragOver.value = false
  const draggedItem = JSON.parse(e.dataTransfer.getData('text/plain'))
  
  if (draggedItem.id === props.item.id || isChildFolder(draggedItem.id)) {
    return
  }

  emit('move', {
    itemId: draggedItem.id,
    targetFolderId: props.item.id
  })
}

const isChildFolder = (draggedId) => {
  if (props.item.type !== 'folder') return false
  
  const checkChildren = (children) => {
    for (const child of children) {
      if (child.id === draggedId) return true
      if (child.type === 'folder' && child.children) {
        if (checkChildren(child.children)) return true
      }
    }
    return false
  }
  
  return checkChildren(props.item.children || [])
}

const handleMove = (moveData) => {
  emit('move', moveData)
}

const handleSelect = (item) => {
  emit('select', item)
}

const handleDelete = (itemId) => {
  emit('delete', itemId)
}

const handleDownload = (item) => {
  downloadRecursively(item)
}

const handleColorChange = (data) => {
  emit('colorChange', data)
}

const deleteRecursively = (itemId) => {
  if (!confirm(`确定要删除${props.item.type === 'folder' ? '文件夹及其所有内容' : '文件'}吗？`)) {
    return;
  }

  try {
    if (props.item.type === 'folder') {
      const deleteChildren = (children) => {
        if (!children) return;
        for (const child of children) {
          if (child.type === 'folder' && child.children) {
            deleteChildren(child.children);
          }
          emit('delete', child.id);
        }
      };
      
      deleteChildren(props.item.children);
    }
    
    emit('delete', itemId);
  } catch (error) {
    console.error('Delete error:', error);
    alert('删除失败：' + error.message);
  }
};

const downloadRecursively = async (item) => {
  try {
    if (item.type === 'folder') {
      const JSZip = (await import('jszip')).default;
      const zip = new JSZip();
      
      const addToZip = (items, folderPath = '') => {
        for (const child of items) {
          if (child.type === 'folder') {
            addToZip(child.children || [], `${folderPath}${child.name}/`);
          } else {
            zip.file(`${folderPath}${child.name}`, child.content || '');
          }
        }
      };
      
      addToZip(item.children || [], `${item.name}/`);
      
      const content = await zip.generateAsync({ type: 'blob' });
      const url = URL.createObjectURL(content);
      const link = document.createElement('a');
      link.href = url;
      link.download = `${item.name}.zip`;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      URL.revokeObjectURL(url);
    } else {
      const blob = new Blob([item.content || ''], { type: 'text/plain' });
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.download = item.name;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      URL.revokeObjectURL(url);
    }
  } catch (error) {
    console.error('Download error:', error);
    alert('下载失败：' + error.message);
  }
};

const router = useRouter()
</script>

<style scoped>
.file-item {
  border-bottom: 1px solid #e1e4e8;
}

.file-header {
  display: flex;
  align-items: center;
  padding: 0.5rem 1rem;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.file-header:hover {
  background-color: #f6f8fa;
}

.file-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex: 1;
}

.file-checkbox {
  width: 1rem;
  height: 1rem;
}

.file-icon {
  width: 1rem;
  height: 1rem;
  color: #586069;
}

.file-name {
  font-size: 0.875rem;
  color: #24292e;
}

.file-size, .upload-time {
  font-size: 0.75rem;
  color: #586069;
  margin-right: 1rem;
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
  background-color: #fafbfc;
  border: 1px solid rgba(27, 31, 35, 0.15);
  border-radius: 4px;
  font-size: 0.75rem;
  color: #24292e;
  cursor: pointer;
  transition: all 0.2s ease;
}

.action-button:hover {
  background-color: #f3f4f6;
}

.button-icon {
  width: 0.875rem;
  height: 0.875rem;
}

.folder-content {
  display: none;
  border-left: 1px solid #e1e4e8;
  margin-left: 1rem;
}

.folder-content.expanded {
  display: block;
}

.is-folder > .file-header {
  font-weight: 600;
}

.preview-button {
  background-color: #e6fffa;
  color: #319795;
}

.preview-button:hover {
  background-color: #b2f5ea;
}

.file-preview {
  padding: 1rem;
  background-color: #f8f9fa;
  border-top: 1px solid #e1e4e8;
  overflow-x: auto;
}

.file-preview pre {
  margin: 0;
  padding: 1rem;
  background-color: #fff;
  border-radius: 4px;
  font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
  font-size: 0.875rem;
  line-height: 1.5;
}

.color-picker {
  width: 24px;
  height: 24px;
  padding: 0;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  background: none;
}

.color-picker::-webkit-color-swatch-wrapper {
  padding: 0;
}

.color-picker::-webkit-color-swatch {
  border: 1px solid #e1e4e8;
  border-radius: 4px;
}

.file-editor, .file-preview {
  padding: 1rem;
  background-color: #0d1117;
  border-top: 1px solid #30363d;
}

.editor-header, .preview-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
  color: #c9d1d9;
}

.editor-actions {
  display: flex;
  gap: 0.5rem;
}

.save-button, .cancel-button, .close-button {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  padding: 0.25rem 0.5rem;
  border: 1px solid #30363d;
  border-radius: 4px;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.save-button {
  background-color: #238636;
  color: #ffffff;
}

.save-button:hover {
  background-color: #2ea043;
}

.cancel-button, .close-button {
  background-color: #21262d;
  color: #c9d1d9;
}

.cancel-button:hover, .close-button:hover {
  background-color: #30363d;
}

.code-editor {
  width: 100%;
  min-height: 300px;
  padding: 1rem;
  background-color: #0d1117;
  color: #c9d1d9;
  font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
  font-size: 0.875rem;
  line-height: 1.5;
  border: 1px solid #30363d;
  border-radius: 4px;
  resize: vertical;
}

.edit-button {
  background-color: #238636;
  color: white;
}

.edit-button:hover {
  background-color: #2ea043;
}

.preview-button {
  background-color: #1f6feb;
  color: white;
}

.preview-button:hover {
  background-color: #388bfd;
}

.is-dragging {
  opacity: 0.5;
}

.is-drag-over {
  background-color: #f0f9ff;
}

.drag-indicator {
  position: relative;
  padding: 0.5rem 0;
}

.drag-line {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 2px;
  background-color: #3182ce;
}

.drag-text {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: #3182ce;
  font-size: 0.875rem;
  background-color: #fff;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.is-selected {
  background-color: #f0f7ff;
}

.is-selected .file-header {
  background-color: #e6f0ff;
}
</style>