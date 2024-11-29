<template>
  <div class="layui-layout layui-layout-admin">
    <div class="about-container">
      <h1 class="page-title">关于我们</h1>
      
      <!-- 个人信息和技术栈部分 -->
      <div class="info-grid">
        <!-- 个人信息部分 -->
        <div class="info-card">
          <h3><i class="layui-icon layui-icon-user"></i> 个人简介</h3>
          <div class="profile-content">
            <img 
              :src="`https://q1.qlogo.cn/g?b=qq&nk=1401466869&s=100`" 
              alt="头像"
              class="profile-avatar"
            >
            <div class="profile-text">
              <p>昵称：小黄</p>
              <p>职业：学生</p>
              <p>爱好：编程、音乐、运动</p>
            </div>
          </div>
        </div>

        <!-- 技术分布图表 -->
        <div class="chart-card">
          <h3><i class="layui-icon layui-icon-chart"></i> 技术分布</h3>
          <div class="chart-container" ref="chartContainer"></div>
        </div>
      </div>

      <!-- 技能详情部分 -->
      <div class="skills-card">
        <h3><i class="layui-icon layui-icon-code-circle"></i> 技能详情</h3>
        <div class="skills-list">
          <div v-for="(skill, index) in skills" :key="index" class="skill-item">
            <div class="skill-info">
              <span class="skill-name">{{ skill.name }}</span>
              <span class="skill-percentage">{{ skill.percentage }}%</span>
            </div>
            <div class="progress-bar">
              <div class="progress" :style="{ width: skill.percentage + '%', backgroundColor: skill.color }"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- 网站技术栈部分 -->
      <div class="tech-stack-section">
        <h3><i class="layui-icon layui-icon-template-1"></i> 网站技术栈</h3>
        <div class="tech-stack-grid">
          <div v-for="(tech, index) in techStack" :key="index" class="tech-stack-item">
            <div class="tech-stack-icon" :style="{ backgroundColor: tech.color }">
              <component :is="tech.icon" />
            </div>
            <div class="tech-stack-content">
              <h4>{{ tech.name }}</h4>
              <p>{{ tech.description }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- 项目介绍部分 -->
      <div class="project-section">
        <h3><i class="layui-icon layui-icon-app"></i> 项目介绍</h3>
        <div class="tech-grid">
          <div v-for="(tech, index) in techDetails" :key="index" class="tech-item">
            <div class="tech-icon" :style="{ backgroundColor: tech.color }">
              <component :is="tech.icon" size="24" />
            </div>
            <div class="tech-info">
              <strong>{{ tech.name }}:</strong> {{ tech.description }}
            </div>
          </div>
        </div>
      </div>

      <!-- 联系方式部分 -->
      <div class="contact-section">
        <h3><i class="layui-icon layui-icon-dialogue"></i> 联系方式</h3>
        <div class="contact-buttons">
          <a-button type="outline" class="contact-btn" @click="copyEmail">
            <template #icon>
              <icon-email />
            </template>
            huangyuejei@gmail.com
          </a-button>
          
          <a-button type="outline" class="contact-btn" @click="openQQ">
            <template #icon>
              <icon-qq />
            </template>
            1401466869
          </a-button>
          
          <a-button type="outline" class="contact-btn" @click="openGithub">
            <template #icon>
              <icon-github />
            </template>
            github.com/huangyueji
          </a-button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import * as echarts from 'echarts'
import { Layers, Code2, Palette, FileJson, BarChart3, GitFork } from 'lucide-vue-next'
import { Message } from '@arco-design/web-vue'
import { IconEmail, IconQq, IconGithub, IconHome, IconStorage, IconPalette, IconApps, IconCommon, IconBranch } from '@arco-design/web-vue/es/icon'

const chartContainer = ref(null)
const skills = ref([
  { name: 'Vue 3', percentage: 90, color: '#42b883' },
  { name: 'JavaScript', percentage: 85, color: '#f7df1e' },
  { name: 'Node.js', percentage: 80, color: '#68a063' },
  { name: 'PHP/Python', percentage: 75, color: '#4F5D95' }
])

const techDetails = [
  { name: '前端框架', description: 'Vue3, React, TypeScript, Layui', icon: Layers, color: '#42b883' },
  { name: '后端技术', description: 'Node.js, PHP, Python, Java', icon: Code2, color: '#f8c555' },
  { name: '数据库', description: 'MySQL, MongoDB, Redis', icon: FileJson, color: '#4dabf7' },
  { name: 'UI组件', description: 'Layui + 自定义组件', icon: Palette, color: '#ff6b6b' },
  { name: '数据可视化', description: 'ECharts', icon: BarChart3, color: '#748ffc' },
  { name: '版本控制', description: 'Git', icon: GitFork, color: '#69db7c' },
]

// 修改技术栈数据，使用 Arco Design 的图标
const techStack = [
  {
    name: 'Vue 3',
    description: '使用 Vue 3 + Composition API 构建用户界面',
    icon: IconHome,
    color: '#42b883'
  },
  {
    name: 'Vite',
    description: '基于 Vite 构建工具，提供极速的开发体验',
    icon: IconStorage,
    color: '#646cff'
  },
  {
    name: 'Arco Design',
    description: '使用字节跳动的 Arco Design Vue 组件库',
    icon: IconPalette,
    color: '#165DFF'
  },
  {
    name: 'Pinia',
    description: '使用 Pinia 进行状态管理',
    icon: IconApps,
    color: '#ffd859'
  },
  {
    name: 'Vue Router',
    description: '使用 Vue Router 实现路由管理',
    icon: IconCommon,
    color: '#00dc82'
  },
  {
    name: 'ECharts',
    description: '使用 ECharts 实现数据可视化',
    icon: IconBranch,
    color: '#c23531'
  }
]

// 复制邮箱
const copyEmail = () => {
  navigator.clipboard.writeText('huangyuejei@gmail.com')
    .then(() => {
      Message.success('邮箱已复制到剪贴板')
    })
    .catch(() => {
      Message.error('复制失败，请手动复制')
    })
}

// 打开QQ
const openQQ = () => {
  window.open('tencent://message/?uin=1401466869')
}

// 打开GitHub
const openGithub = () => {
  window.open('https://github.com/huangyueji', '_blank')
}

onMounted(() => {
  const chart = echarts.init(chartContainer.value)
  
  const option = {
    tooltip: {
      trigger: 'item',
      formatter: '{b}: {c}%'
    },
    series: [
      {
        type: 'pie',
        radius: ['50%', '70%'],
        avoidLabelOverlap: false,
        itemStyle: {
          borderRadius: 10,
          borderColor: '#fff',
          borderWidth: 2
        },
        label: {
          show: false,
          position: 'center'
        },
        emphasis: {
          label: {
            show: true,
            fontSize: '18',
            fontWeight: 'bold'
          }
        },
        labelLine: {
          show: false
        },
        data: skills.value.map(skill => ({
          value: skill.percentage,
          name: skill.name,
          itemStyle: {
            color: skill.color
          }
        }))
      }
    ]
  }
  
  chart.setOption(option)
  
  window.addEventListener('resize', () => {
    chart.resize()
  })
})
</script>

<style scoped>
.about-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

.page-title {
  text-align: center;
  color: var(--color-text-1);
  margin-bottom: 2rem;
  font-size: 2rem;
  font-weight: 700;
}

.info-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
  margin-bottom: 2rem;
}

.info-card, .chart-card, .skills-card, .project-section, .contact-section {
  background-color: var(--color-bg-2);
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  padding: 1.5rem;
  transition: all 0.3s ease;
}

.info-card:hover, .chart-card:hover, .skills-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

h3 {
  color: var(--color-text-1);
  font-size: 18px;
  margin-bottom: 1.5rem;
}

h3 i {
  margin-right: 8px;
  color: var(--color-primary);
}

.profile-content {
  display: flex;
  align-items: flex-start;
  gap: 20px;
}

.profile-avatar {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  border: 3px solid var(--color-primary);
}

.profile-text p {
  margin: 10px 0;
  color: var(--color-text-2);
}

.chart-container {
  height: 300px;
  width: 100%;
}

.skills-list {
  display: grid;
  gap: 1.5rem;
}

.skill-item {
  background-color: var(--color-fill-2);
  padding: 1rem;
  border-radius: 8px;
}

.skill-info {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.5rem;
  color: var(--color-text-2);
}

.skill-name {
  color: var(--color-text-1);
  font-weight: 500;
}

.skill-percentage {
  color: var(--color-text-3);
}

.progress-bar {
  height: 8px;
  background-color: var(--color-fill-1);
  border-radius: 4px;
  overflow: hidden;
}

.progress {
  height: 100%;
  border-radius: 4px;
  transition: width 0.3s ease;
}

.tech-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
}

.tech-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  background-color: var(--color-fill-2);
  border-radius: 8px;
  transition: all 0.3s ease;
}

.tech-item:hover {
  transform: translateX(5px);
  background-color: var(--color-fill-1);
}

.tech-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 48px;
  height: 48px;
  border-radius: 50%;
  color: white;
}

.tech-info {
  color: var(--color-text-2);
}

.tech-info strong {
  color: var(--color-text-1);
  font-weight: 600;
}

.contact-buttons {
  display: flex;
  flex-wrap: wrap;
  gap: 16px;
}

.contact-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  font-size: 14px;
  border-color: var(--color-border);
  color: var(--color-text-2);
  background-color: var(--color-bg-2);
  transition: all 0.3s ease;
}

.contact-btn:hover {
  color: var(--color-primary);
  border-color: var(--color-primary);
  background-color: var(--color-fill-2);
  transform: translateY(-2px);
}

.contact-btn :deep(.arco-icon) {
  font-size: 18px;
}

.tech-stack-section {
  background-color: var(--color-bg-2);
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  padding: 1.5rem;
  margin-bottom: 2rem;
  transition: all 0.3s ease;
}

.tech-stack-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
}

.tech-stack-item {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  padding: 1rem;
  background-color: var(--color-fill-2);
  border-radius: 8px;
  transition: all 0.3s ease;
}

.tech-stack-item:hover {
  transform: translateY(-3px);
  background-color: var(--color-fill-1);
}

.tech-stack-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  border-radius: 8px;
  color: white;
  flex-shrink: 0;
}

.tech-stack-icon :deep(.arco-icon) {
  font-size: 24px;
}

.tech-stack-content {
  flex: 1;
}

.tech-stack-content h4 {
  color: var(--color-text-1);
  font-size: 16px;
  margin-bottom: 4px;
  font-weight: 500;
}

.tech-stack-content p {
  color: var(--color-text-3);
  font-size: 14px;
  margin: 0;
  line-height: 1.5;
}

/* 移动端适配 */
@media screen and (max-width: 768px) {
  .info-grid {
    grid-template-columns: 1fr;
  }

  .profile-content {
    flex-direction: column;
    align-items: center;
    text-align: center;
  }

  .chart-container {
    height: 250px;
  }

  .contact-buttons {
    flex-direction: column;
  }
  
  .contact-btn {
    width: 100%;
    justify-content: center;
  }

  .tech-grid {
    grid-template-columns: 1fr;
  }

  .tech-stack-grid {
    grid-template-columns: 1fr;
  }
  
  .tech-stack-item {
    padding: 0.8rem;
  }
}
</style>