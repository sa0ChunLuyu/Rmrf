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
      }
    ],
    socialLinks: [
      {icon: 'github', link: 'https://github.com/sa0ChunLuyu/Rmrf'},
    ]
  }
})
