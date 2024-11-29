import '@arco-design/web-vue/dist/arco.css'
import './assets/main.css'
import 'highlight.js/styles/github-dark.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'
import ArcoVue from '@arco-design/web-vue'
import App from './App.vue'
import router from './router'
import hljs from 'highlight.js'
import php from 'highlight.js/lib/languages/php'
import { MotionPlugin } from '@vueuse/motion'

hljs.registerLanguage('php', php)

const pinia = createPinia()
pinia.use(piniaPluginPersistedstate)

const app = createApp(App)

app.use(pinia)
app.use(ArcoVue)
app.use(router)
app.use(MotionPlugin)

app.mount('#app')
