import {createRouter, createWebHashHistory} from 'vue-router'
import {setupLayouts} from 'virtual:generated-layouts'
import generatedRoutes from 'virtual:generated-pages'
import {$favicon} from "~/tool/favicon"
import {
  useStore, useSaveTokenType, useSessionToken, useToken, useRouterActive, useProxyShow
} from '~/store'
import {$image, $api, $response} from "~/api";

const allow_unlogged_in_page = ['login', '404'];
const $proxy_show = useProxyShow()
const $router_active = useRouterActive()
const router = createRouter({
  history: createWebHashHistory(import.meta.env.BASE_URL), routes: setupLayouts(generatedRoutes)
})
const updateRouterActive = (matched) => {
  matched.shift()
  const last = matched[matched.length - 1]
  if (allow_unlogged_in_page.indexOf(last.name) !== -1) return
  setTimeout(() => {
    $router_active.value = matched.map((item) => {
      return {
        title: 'title' in item.meta ? item.meta.title : item.name,
        key: 'active' in item.meta ? item.meta.active : item.name,
      }
    })
  })
}
let token_check = ''
let error_status = false

router.beforeEach(async (to, from, next) => {
  const $store = useStore()
  if (!$store.config) {
    const response = await $api('AdminConfigGet', {
      config_arr: [
        "Logo",
        "Favicon",
        "Login欢迎图片",
        "Login背景图",
        "Login背景色",
        "网站名称",
        "后台密码登录验证",
      ]
    })
    $response(response, () => {
      $store.config = response.data
      $favicon($image(response.data['Favicon']))
    })
    if (!response) error_status = true
  }
  if (!!error_status) {
    $store.config = {
      "Logo": "./assets/images/logo.png",
      "Favicon": "./assets/images/favicon.png",
      "Login欢迎图片": "./assets/images/login_cover.png",
      "Login背景图": "./assets/images/login_bg.png",
      "Login背景色": "#409eff",
      "网站名称": "网络错误",
      "网站介绍": "请点击右下角代理设置进行调整",
      "后台密码登录验证": "0",
    }
    $favicon($image($store.config['Favicon']))
    if (to.name !== 'login') {
      $proxy_show.value = true
      next('/login')
    }
  }
  document.title = ('title' in to.meta && to.meta.title !== '首页') ? `${to.meta.title} ${$store.config['网站名称']}` : $store.config['网站名称']
  if (allow_unlogged_in_page.indexOf(to.name) === -1) {
    const $save_token_type = useSaveTokenType()
    let $token;
    if ($save_token_type.value === 'local') {
      $token = useToken()
    } else {
      $token = useSessionToken()
    }
    if ($token.value === '') {
      next('/login?f=' + encodeURIComponent(to.fullPath))
    } else {
      if (token_check === $token.value) {
        updateRouterActive(to.matched.map(item => item))
        next()
      } else {
        const response = await $api('AdminAdminStatus')
        $response(response, () => {
          token_check = $token.value
          updateRouterActive(to.matched.map(item => item))
          next()
        })
      }
    }
  } else {
    next()
  }
})
export default router
