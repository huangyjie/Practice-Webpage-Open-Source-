import { ref } from 'vue'

const fileList = ref([])
const folderContent = ref([])
const uploadedFiles = ref([])

export function useFileStore() {
  return {
    fileList,
    folderContent,
    uploadedFiles
  }
} 