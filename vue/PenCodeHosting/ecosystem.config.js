module.exports = {
  apps: [{
    name: 'xiaohuang',
    script: 'npm',
    args: 'run dev',
    cwd: '/www/wwwroot/vue/xiaohuang',
    env: {
      NODE_ENV: 'development',
      PORT: 5174
    },
    watch: true,
    ignore_watch: ['node_modules', 'logs']
  }]
} 