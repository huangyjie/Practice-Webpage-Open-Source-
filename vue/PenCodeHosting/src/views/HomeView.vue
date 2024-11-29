<template>
    <div class="dashboard">
      <h1 class="dashboard-title">项目概览</h1>
      
      <!-- 统计卡片 -->
      <div class="stats-cards">
        <div class="stat-card">
          <div class="stat-icon">
            <CodeIcon class="icon" />
          </div>
          <div class="stat-content">
            <div class="stat-value">{{ totalLines.toLocaleString() }}</div>
            <div class="stat-label">代码总行数</div>
          </div>
        </div>
  
        <div class="stat-card">
          <div class="stat-icon">
            <FileIcon class="icon" />
          </div>
          <div class="stat-content">
            <div class="stat-value">{{ totalFiles.toLocaleString() }}</div>
            <div class="stat-label">文件总数</div>
          </div>
        </div>
  
        <div class="stat-card">
          <div class="stat-icon">
            <FolderIcon class="icon" />
          </div>
          <div class="stat-content">
            <div class="stat-value">{{ totalFolders.toLocaleString() }}</div>
            <div class="stat-label">文件夹量</div>
          </div>
        </div>
      </div>
  
      <!-- 图表区域 -->
      <div class="charts-container">
        <!-- 提交热力图 -->
        <div class="chart-card full-width">
          <h3>代码提交热力图</h3>
          <div ref="heatmapRef" class="chart heatmap-chart"></div>
        </div>
  
        <div class="charts-row">
          <!-- 语言分布饼图 -->
          <div class="chart-card">
            <h3>代码语言分布</h3>
            <div ref="pieRef" class="chart pie-chart"></div>
          </div>
          <!-- 文件数量柱状图 -->
          <div class="chart-card">
            <h3>文件类型统计</h3>
            <div ref="barRef" class="chart bar-chart"></div>
          </div>
          <!-- 新增的编程语言占比图表 -->
          <div class="chart-card">
            <h3>编程语言占比</h3>
            <div ref="languagePieRef" class="chart pie-chart"></div>
          </div>
        </div>
      </div>
  
      <!-- 添加共享文件区域 -->
      <div class="shared-files-section" v-if="sharedStore.sharedFiles.length">
        <h2 class="section-title">
          管理员分享
          <span class="update-time" v-if="sharedStore.lastUpdate">
            最后更新: {{ formatDate(sharedStore.lastUpdate) }}
          </span>
        </h2>
        
        <div class="shared-files-grid">
          <div v-for="file in sharedStore.sharedFiles" 
               :key="file.id" 
               class="shared-file-card">
            <div class="file-icon">
              <component :is="getFileIcon(file.type)" />
            </div>
            <div class="file-info">
              <h3>{{ file.name }}</h3>
              <p>{{ file.size }}</p>
            </div>
            <div class="file-actions">
              <a-button type="text" @click="previewFile(file)">
                <template #icon><icon-eye /></template>
                预览
              </a-button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted, onUnmounted } from 'vue'
  import { CodeIcon, FileIcon, FolderIcon } from 'lucide-vue-next'
  import * as echarts from 'echarts'
  import { useFileStore } from '../stores/files'
  import { useRouter } from 'vue-router'
  import { useSharedStore } from '../stores/shared'
  import { IconEye } from '@arco-design/web-vue/es/icon'
  
  const fileStore = useFileStore()
  const router = useRouter()
  const sharedStore = useSharedStore()
  
  // 引用DOM元素
  const heatmapRef = ref(null)
  const pieRef = ref(null)
  const barRef = ref(null)
  const languagePieRef = ref(null)
  
  // 图表实例
  let heatmapChart = null
  let pieChart = null
  let barChart = null
  let languagePieChart = null
  
  // 统计数据
  const totalLines = ref(0)
  const totalFiles = ref(0)
  const totalFolders = ref(0)
  
  // 定义一个鲜艳的调色板
  const brightColors = [
    '#FF6B6B', // 珊瑚红
    '#4ECDC4', // 绿松石
    '#45B7D1', // 天蓝
    '#96CEB4', // 薄荷绿
    '#FFD93D', // 明黄
    '#FF8C42', // 橙色
    '#6C5CE7', // 靛蓝
    '#A8E6CF', // 浅绿
    '#FF9F1C', // 金橙
    '#7ED6DF', // 浅蓝
    '#E056FD', // 紫色
    '#F8C291', // 杏色
    '#00D2D3', // 青绿
    '#FF9FF3', // 粉红
    '#FFF200', // 亮黄
    '#32FF7E', // 荧光绿
  ]
  
  // 初始化数据
  const initStats = () => {
    try {
      let lines = 0
      let files = 0
      let folders = 0
      const languageStats = {}
      const fileTypeStats = {}
      
      const processItem = (item) => {
        if (item.type === 'folder') {
          folders++
          item.children?.forEach(processItem)
        } else {
          files++
          if (item.content) {
            lines += item.content.split('\n').length
          }
          
          // 获取文件扩展名，处理特殊文件
          let ext = item.name.toLowerCase()
          if (ext.includes('.')) {
            ext = ext.split('.').pop()
          } else {
            // 处理无扩展名的特殊文件
            switch (ext) {
              case 'dockerfile':
                ext = 'dockerfile'
                break
              case 'dockerignore':
                ext = 'dockerignore'
                break
              case 'gitignore':
                ext = 'gitignore'
                break
              case 'editorconfig':
                ext = 'editorconfig'
                break
              default:
                ext = 'other'
            }
          }
          
          languageStats[ext] = (languageStats[ext] || 0) + 1
          fileTypeStats[ext] = (fileTypeStats[ext] || 0) + 1
        }
      }
      
      fileStore.uploadedFiles.forEach(processItem)
      
      totalLines.value = lines
      totalFiles.value = files
      totalFolders.value = folders
      
      updateCharts(languageStats, fileTypeStats)
    } catch (error) {
      console.error('Error calculating stats:', error)
    }
  }
  
  // 更新图表
  const updateCharts = (languageStats, fileTypeStats) => {
    const isDarkTheme = document.documentElement.getAttribute('arco-theme') === 'dark'
    
    // 定义主题相关的颜色
    const themeColors = {
      background: isDarkTheme ? '#232324' : '#ffffff',
      textColor: isDarkTheme ? '#ffffffd1' : '#1d2129',
      borderColor: isDarkTheme ? '#333335' : '#e5e6eb',
      tooltipBackground: isDarkTheme ? 'rgba(0, 0, 0, 0.85)' : 'rgba(255, 255, 255, 0.9)',
      tooltipTextColor: isDarkTheme ? '#fff' : '#333',
      axisLineColor: isDarkTheme ? '#333335' : '#E2E8F0',
      splitLineColor: isDarkTheme ? '#333335' : '#E2E8F0'
    }
    
    // 定义编程语言的专属颜色
    const languageColors = {
      js: '#F7DF1E',      // JavaScript 明亮的黄色
      jsx: '#61DAFB',     // React 亮蓝色
      ts: '#3178C6',      // TypeScript 鲜艳的蓝色
      tsx: '#00D8FF',     // React TypeScript 天蓝色
      vue: '#42D392',     // Vue 翠绿色
      php: '#8993BE',     // PHP 淡紫色
      py: '#FFD43B',      // Python 金黄色
      java: '#F89820',    // Java 橙色
      cpp: '#F34B7D',     // C++ 粉红色
      c: '#659AD2',       // C 天蓝色
      cs: '#68217A',      // C# 紫色
      go: '#00ADD8',      // Go 亮蓝色
      rb: '#FF0000',      // Ruby 红色
      rs: '#FFA500',      // Rust 橙色
      swift: '#FF4B4B',   // Swift 亮红色
      kt: '#A97BFF',      // Kotlin 亮紫色
      
      // 配置文件
      json: '#40C4FF',    // JSON 亮蓝色
      xml: '#FF6B6B',     // XML 珊瑚红
      yaml: '#FF9800',    // YAML 橙色
      yml: '#FF9800',     // YML 橙色
      toml: '#4CAF50',    // TOML 绿色
      ini: '#9C27B0',     // INI 紫色
      conf: '#673AB7',    // CONF 深紫色
      properties: '#2196F3', // Properties 蓝色
      
      // 标记语言
      html: '#E44D26',    // HTML 亮红色
      css: '#563D7C',     // CSS 深紫色
      md: '#0D47A1',      // Markdown 深蓝色
      svg: '#FF9800',     // SVG 橙色
      
      // 其他文件类型
      sh: '#4CAF50',      // Shell 绿色
      bash: '#4CAF50',    // Bash 绿色
      bat: '#00E676',     // Batch 亮绿色
      ps1: '#012456',     // PowerShell 深蓝色
      sql: '#FF6B6B',     // SQL 珊瑚红
      
      // 特殊文件
      gitignore: '#F05033',  // Git 红色
      dockerignore: '#2496ED', // Docker 蓝色
      dockerfile: '#2496ED',   // Dockerfile 蓝色
      editorconfig: '#FF4081', // EditorConfig 粉色
      env: '#00C853',         // 环境变量文件 绿色
    }
  
    // 更新饼图数据分组
    const groupData = {
      '编程语言': ['js', 'jsx', 'ts', 'tsx', 'vue', 'php', 'py', 'java', 'cpp', 'c', 'cs', 'go', 'rb', 'rs', 'swift', 'kt'],
      '配置文件': ['json', 'xml', 'yaml', 'yml', 'toml', 'ini', 'conf', 'properties', 'env'],
      '标记语言': ['html', 'css', 'md', 'svg'],
      '脚本文件': ['sh', 'bash', 'bat', 'ps1', 'sql'],
      '特殊文件': ['gitignore', 'dockerignore', 'dockerfile', 'editorconfig']
    }
  
    const groupedStats = {}
    Object.entries(languageStats).forEach(([ext, count]) => {
      const group = Object.entries(groupData).find(([_, exts]) => exts.includes(ext))?.[0] || '其他'
      groupedStats[group] = (groupedStats[group] || 0) + count
    })
  
    // 更新饼图配置
    if (pieChart) {
      pieChart.setOption({
        tooltip: {
          trigger: 'item',
          formatter: '{a} <br/>{b}: {c} ({d}%)',
          backgroundColor: themeColors.tooltipBackground,
          borderColor: themeColors.borderColor,
          borderWidth: 1,
          textStyle: {
            color: themeColors.tooltipTextColor
          },
          extraCssText: 'box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);'
        },
        legend: {
          orient: 'vertical',
          left: 'left',
          top: 'middle',
          textStyle: {
            color: themeColors.textColor,
            fontSize: 12
          },
          itemWidth: 10,
          itemHeight: 10,
          itemGap: 12,
          formatter: name => {
            const value = groupedStats[name]
            const percentage = ((value / Object.values(groupedStats).reduce((a, b) => a + b, 0)) * 100).toFixed(1)
            return `${name.toUpperCase()} ${percentage}%`
          }
        },
        series: [{
          name: '文件分布',
          type: 'pie',
          radius: ['45%', '75%'],
          center: ['60%', '50%'],
          avoidLabelOverlap: false,
          itemStyle: {
            borderRadius: 10,
            borderColor: isDarkTheme ? '#232324' : '#ffffff',
            borderWidth: 2,
            shadowBlur: 15,
            shadowColor: 'rgba(0, 0, 0, 0.2)'
          },
          label: {
            show: false
          },
          emphasis: {
            label: {
              show: true,
              fontSize: '18',
              fontWeight: 'bold',
              formatter: '{b}\n{d}%'
            },
            itemStyle: {
              shadowBlur: 25,
              shadowColor: 'rgba(0, 0, 0, 0.3)'
            }
          },
          data: Object.entries(groupedStats).map(([name, value], index) => ({
            name,
            value,
            itemStyle: {
              color: brightColors[index % brightColors.length]
            }
          }))
        }]
      })
    }
  
    // 更新柱状图配置
    if (barChart) {
      barChart.setOption({
        tooltip: {
          trigger: 'axis',
          axisPointer: {
            type: 'shadow'
          },
          backgroundColor: themeColors.tooltipBackground,
          borderColor: themeColors.borderColor,
          borderWidth: 1,
          textStyle: {
            color: themeColors.tooltipTextColor
          },
          extraCssText: 'box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);'
        },
        grid: {
          left: '3%',
          right: '4%',
          bottom: '15%',
          top: '10%',
          containLabel: true
        },
        xAxis: {
          type: 'category',
          data: Object.keys(fileTypeStats).map(name => name.toUpperCase()),
          axisLabel: {
            rotate: 45,
            color: themeColors.textColor,
            fontSize: 11,
            interval: 0
          },
          axisLine: {
            lineStyle: {
              color: themeColors.axisLineColor,
              width: 2
            }
          },
          axisTick: {
            show: false
          }
        },
        yAxis: {
          type: 'value',
          axisLabel: {
            color: themeColors.textColor,
            formatter: '{value}'
          },
          axisLine: {
            show: false
          },
          splitLine: {
            lineStyle: {
              color: themeColors.splitLineColor,
              type: 'dashed'
            }
          }
        },
        series: [{
          name: '文件数量',
          type: 'bar',
          barWidth: '60%',
          data: Object.entries(fileTypeStats).map(([name, value], index) => ({
            value,
            itemStyle: {
              color: {
                type: 'linear',
                x: 0,
                y: 0,
                x2: 0,
                y2: 1,
                colorStops: [{
                  offset: 0,
                  color: brightColors[index % brightColors.length]
                }, {
                  offset: 1,
                  color: echarts.color.lift(brightColors[index % brightColors.length], 0.3)
                }]
              },
              borderRadius: [6, 6, 0, 0],
              shadowColor: 'rgba(0, 0, 0, 0.2)',
              shadowBlur: 15
            }
          }))
        }]
      })
    }
  
    // 更新热力图配置
    if (heatmapChart) {
      const commitHistory = JSON.parse(localStorage.getItem('commitHistory') || '[]')
      const today = new Date()
      const data = []
      for (let i = 0; i < 365; i++) {
        const date = new Date(today)
        date.setDate(date.getDate() - i)
        const dateStr = date.toISOString().split('T')[0]
        const commit = commitHistory.find(c => c[0] === dateStr)
        data.unshift([dateStr, commit ? commit[1] : 0])
      }
      
      heatmapChart.setOption({
        tooltip: {
          position: 'top',
          formatter: function (p) {
            return `${p.data[0]}: ${p.data[1]} 次提交`
          },
          backgroundColor: themeColors.tooltipBackground,
          borderColor: themeColors.borderColor,
          borderWidth: 1,
          textStyle: {
            color: themeColors.tooltipTextColor
          },
          extraCssText: 'box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);'
        },
        visualMap: {
          min: 0,
          max: 10,
          calculable: true,
          orient: 'horizontal',
          left: 'center',
          bottom: '0%',
          itemHeight: 80,
          itemWidth: 15,
          inRange: {
            color: [
              '#ebedf0',
              '#45B7D1',  // 天蓝
              '#4ECDC4',  // 绿松石
              '#96CEB4',  // 薄荷绿
              '#32FF7E'   // 荧光绿
            ]
          },
          textStyle: {
            color: themeColors.textColor
          }
        },
        calendar: {
          top: '5%',
          left: 'center',
          range: [data[0][0], data[data.length - 1][0]],
          cellSize: ['auto', 15],
          splitLine: {
            show: true,
            lineStyle: {
              color: themeColors.axisLineColor,
              width: 2,
              type: 'solid'
            }
          },
          itemStyle: {
            color: '#F7FAFC',
            borderColor: '#E2E8F0',
            borderWidth: 1,
            borderRadius: 2
          },
          yearLabel: { show: false },
          dayLabel: {
            firstDay: 1,
            nameMap: ['周日', '周一', '周二', '周三', '周四', '周五', '周六'],
            color: themeColors.textColor
          },
          monthLabel: {
            nameMap: 'cn',
            color: themeColors.textColor,
            fontSize: 12
          }
        },
        series: {
          type: 'heatmap',
          coordinateSystem: 'calendar',
          data: data,
          animationDuration: 1000,
          animationEasing: 'cubicOut',
          emphasis: {
            itemStyle: {
              shadowBlur: 10,
              shadowColor: 'rgba(0, 0, 0, 0.2)'
            }
          }
        }
      })
    }
  
    // 更新编程语言占比图配置
    if (languagePieChart) {
      // 只处理编程语言的数据
      const programmingLanguages = {
        'JavaScript': ['js', 'jsx'],
        'TypeScript': ['ts', 'tsx'],
        'Vue': ['vue'],
        'PHP': ['php'],
        'Python': ['py'],
        'Java': ['java'],
        'C/C++': ['cpp', 'c', 'h'],
        'C#': ['cs'],
        'Go': ['go'],
        'Ruby': ['rb'],
        'Rust': ['rs'],
        'Swift': ['swift'],
        'Kotlin': ['kt']
      }

      const languageData = {}
      Object.entries(languageStats).forEach(([ext, count]) => {
        for (const [lang, extensions] of Object.entries(programmingLanguages)) {
          if (extensions.includes(ext)) {
            languageData[lang] = (languageData[lang] || 0) + count
            break
          }
        }
      })

      // 过滤掉数量为0的语言
      const filteredData = Object.entries(languageData)
        .filter(([_, value]) => value > 0)
        .sort((a, b) => b[1] - a[1])

      languagePieChart.setOption({
        tooltip: {
          trigger: 'item',
          formatter: '{a} <br/>{b}: {c} ({d}%)',
          backgroundColor: themeColors.tooltipBackground,
          borderColor: themeColors.borderColor,
          borderWidth: 1,
          textStyle: {
            color: themeColors.tooltipTextColor
          },
          extraCssText: 'box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);'
        },
        legend: {
          orient: 'vertical',
          right: '5%',
          top: 'middle',
          textStyle: {
            color: themeColors.textColor,
            fontSize: 12
          },
          itemWidth: 10,
          itemHeight: 10,
          itemGap: 12,
          formatter: name => {
            const value = languageData[name]
            const total = Object.values(languageData).reduce((a, b) => a + b, 0)
            const percentage = ((value / total) * 100).toFixed(1)
            return `${name} ${percentage}%`
          }
        },
        series: [{
          name: '编程语言',
          type: 'pie',
          radius: ['45%', '75%'],
          center: ['35%', '50%'],
          avoidLabelOverlap: false,
          itemStyle: {
            borderRadius: 10,
            borderColor: isDarkTheme ? '#232324' : '#ffffff',
            borderWidth: 2,
            shadowBlur: 15,
            shadowColor: 'rgba(0, 0, 0, 0.2)'
          },
          label: {
            show: false
          },
          emphasis: {
            label: {
              show: true,
              fontSize: '18',
              fontWeight: 'bold'
            },
            itemStyle: {
              shadowBlur: 25,
              shadowColor: 'rgba(0, 0, 0, 0.3)'
            }
          },
          data: filteredData.map(([name, value], index) => ({
            name,
            value,
            itemStyle: {
              color: brightColors[index % brightColors.length]
            }
          }))
        }]
      })
    }
  }
  
  // 初始化图表
  const initCharts = () => {
    if (heatmapRef.value) {
      heatmapChart = echarts.init(heatmapRef.value)
    }
    if (pieRef.value) {
      pieChart = echarts.init(pieRef.value)
    }
    if (barRef.value) {
      barChart = echarts.init(barRef.value)
    }
    if (languagePieRef.value) {
      languagePieChart = echarts.init(languagePieRef.value)
    }
    
    // 初始化统计数据
    initStats()
  }
  
  // 监听窗口大小变化
  const handleResize = () => {
    heatmapChart?.resize()
    pieChart?.resize()
    barChart?.resize()
    languagePieChart?.resize()
  }
  
  // 添加主题监听
  const handleThemeChange = () => {
    // 销毁现有图表
    heatmapChart?.dispose()
    pieChart?.dispose()
    barChart?.dispose()
    languagePieChart?.dispose()
    
    // 重新初始化图表
    initCharts()
  }
  
  // 格式化日期
  const formatDate = (date) => {
    return new Date(date).toLocaleString()
  }
  
  // 获取文件图标
  const getFileIcon = (type) => {
    // 返回对应的图标组件
    return type === 'folder' ? FolderIcon : FileIcon
  }
  
  // 预览文件
  const previewFile = (file) => {
    localStorage.setItem('previewFile', JSON.stringify(file))
    router.push('/preview')
  }
  
  onMounted(async () => {
    try {
      // 先加载共享文件
      await sharedStore.loadSharedFiles()
      
      // 再加载其他数据
      await fileStore.loadFiles()
      initCharts()
      
      // 添加数据加载状态检查
      console.log('Shared files:', sharedStore.sharedFiles)
      console.log('Uploaded files:', fileStore.uploadedFiles)
    } catch (error) {
      console.error('Error loading data:', error)
    }
    window.addEventListener('resize', handleResize)
    
    // 添加主题变化监听
    const observer = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        if (mutation.attributeName === 'arco-theme') {
          handleThemeChange()
        }
      })
    })
    
    observer.observe(document.documentElement, {
      attributes: true,
      attributeFilter: ['arco-theme']
    })
  })
  
  onUnmounted(() => {
    window.removeEventListener('resize', handleResize)
    heatmapChart?.dispose()
    pieChart?.dispose()
    barChart?.dispose()
    languagePieChart?.dispose()
    
    // 在组件卸载时移除观察器
    observer.disconnect()
  })
  </script>
  
  <style scoped>
  .dashboard {
    padding: 2rem;
    background-color: var(--color-bg-1);
    min-height: 100vh;
    font-family: 'Inter', sans-serif;
  }
  
  .dashboard-title {
    font-size: 2rem;
    font-weight: 700;
    color: var(--color-text-1);
    margin-bottom: 2rem;
    text-align: center;
  }
  
  .stats-cards {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
    margin-bottom: 3rem;
    padding: 0 1rem;
    max-width: 1400px;
    margin: 0 auto 3rem auto;
  }
  
  .stat-card {
    background: var(--color-bg-2);
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    display: flex;
    align-items: center;
    gap: 1.5rem;
    transition: all 0.3s ease;
    border: 1px solid var(--color-border);
    min-height: 100px;
  }
  
  .stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
  }
  
  .stat-icon {
    background: var(--color-fill-2);
    padding: 1.25rem;
    border-radius: 12px;
    color: var(--color-primary);
    transition: all 0.3s ease;
  }
  
  .stat-card:hover .stat-icon {
    transform: scale(1.1);
    background: var(--color-primary);
    color: white;
  }
  
  .stat-icon .icon {
    width: 32px;
    height: 32px;
  }
  
  .stat-content {
    flex: 1;
  }
  
  .stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: var(--color-text-1);
    line-height: 1.2;
  }
  
  .stat-label {
    font-size: 1rem;
    color: var(--color-text-3);
    margin-top: 0.5rem;
  }
  
  .charts-container {
    display: flex;
    flex-direction: column;
    gap: 2rem;
    padding: 0 1rem;
    max-width: 1400px;
    margin: 0 auto;
  }
  
  .chart-card.full-width {
    grid-column: 1 / -1;
    margin-bottom: 2rem;
  }
  
  .charts-row {
    display: flex;
    flex-direction: column;
    gap: 2rem;
  }
  
  .chart-card {
    background: var(--color-bg-2);
    border-radius: 16px;
    padding: 2.5rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid var(--color-border);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    width: 100%;
  }
  
  .chart-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
  }
  
  .chart-card h3 {
    margin: 0 0 2rem 0;
    color: var(--color-text-1);
    font-size: 1.5rem;
    font-weight: 600;
    text-align: center;
  }
  
  .chart {
    width: 100%;
  }
  
  .heatmap-chart {
    height: 300px;
    margin-top: 1.5rem;
  }
  
  .pie-chart, .bar-chart {
    height: 500px;
    margin-top: 1.5rem;
  }
  
  @media (max-width: 768px) {
    .chart-card {
      padding: 1.5rem;
    }
    
    .pie-chart, .bar-chart {
      height: 400px;
    }
    
    .heatmap-chart {
      height: 250px;
    }
    
    .chart-card h3 {
      font-size: 1.25rem;
    }
  }
  
  @media (min-width: 1024px) {
    .stats-cards {
      grid-template-columns: repeat(3, 1fr);
    }
  }
  
  /* 添加共享文件区域样式 */
  .shared-files-section {
    margin-top: 3rem;
    padding: 2rem;
    background: var(--color-bg-2);
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  }
  
  .section-title {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    color: var(--color-text-1);
  }
  
  .update-time {
    font-size: 0.875rem;
    color: var(--color-text-3);
  }
  
  .shared-files-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
  }
  
  .shared-file-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: var(--color-fill-2);
    border-radius: 8px;
    transition: all 0.3s ease;
  }
  
  .shared-file-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }
  
  .file-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--color-fill-1);
    border-radius: 8px;
    color: var(--color-text-2);
  }
  
  .file-info {
    flex: 1;
  }
  
  .file-info h3 {
    margin: 0;
    font-size: 1rem;
    color: var(--color-text-1);
  }
  
  .file-info p {
    margin: 0.25rem 0 0;
    font-size: 0.875rem;
    color: var(--color-text-3);
  }
  
  .file-actions {
    opacity: 0;
    transition: opacity 0.2s ease;
  }
  
  .shared-file-card:hover .file-actions {
    opacity: 1;
  }
  </style>