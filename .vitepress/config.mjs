import {defineConfig} from 'vitepress'

// https://vitepress.dev/reference/site-config
export default defineConfig({
  base: `/rm-rf-doc/`,
  title: "入魔入佛",
  description: "使用说明书",
  themeConfig: {
    // https://vitepress.dev/reference/default-theme-config
    nav: [
      {text: '开始使用', link: '/README'},
      {text: 'Github', link: 'https://github.com/sa0ChunLuyu/Rmrf'}
    ],
    sidebar: [
      {
        text: 'README',
        link: '/README'
      }, {
        text: 'Bot',
        items: [
          {text: 'README', link: '/Bot/readme'},
        ]
      }, {
        text: 'Vue3',
        items: [
          {text: 'README', link: '/Vue3/readme'},
        ]
      }, {
        text: 'Admin',
        items: [
          {text: 'README', link: '/Admin/readme'},
        ]
      }, {
        text: 'Gateway',
        items: [
          {text: 'README', link: '/Gateway/readme'},
        ]
      }, {
        text: 'Uniapp',
        items: [
          {text: 'README', link: '/Uniapp/readme'},
        ]
      }, {
        text: 'UniappPro',
        items: [
          {text: 'README', link: '/UniappPro/readme'},
        ]
      }, {
        text: 'Laravel',
        items: [
          {text: 'README', link: '/Laravel/readme'},
        ]
      }, {
        text: 'LaravelPro',
        items: [
          {text: 'README', link: '/LaravelPro/readme'},
        ]
      }, {
        text: 'Vitepress',
        items: [
          {text: 'README', link: '/Vitepress/readme'},
        ]
      }, {
        text: 'Doc',
        items: [
          {text: 'README', link: '/Doc/readme'},
        ]
      }
    ],
    socialLinks: [
      {icon: 'github', link: 'https://github.com/sa0ChunLuyu/Rmrf'},
    ]
  }
})
