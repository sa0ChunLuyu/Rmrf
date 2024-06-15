<script setup>
/**
 * name：
 * user：sa0ChunLuyu
 * date：2023年7月27日 10:46:18
 */
import {
  $api,
  $image,
  $response,
} from '~/api'
import {useSaveTokenType, useToken, useSessionToken, useStore, useConfig, useProxyShow} from "~/store";
import {onBeforeRouteUpdate} from "vue-router";
import $router from '~/router'
import {getInfo} from "~/tool/info";

const $proxy_show = useProxyShow()
const $config = useConfig()
const $store = useStore()
const $save_token_type = useSaveTokenType()
const $session_token = useSessionToken()
const $token = useToken()
const login_input_data = ref({
  account: '',
  password: '',
  code: '',
  hash: '',
  time: '',
  uuid: '',
})

const default_page_options = {
  f: '/'
}
const page_options = ref(default_page_options)
onBeforeRouteUpdate((to) => {
  routerChange(to.query)
})

const routerChange = (query) => {
  page_options.value = {
    f: query.f || default_page_options.f
  }
  if ($proxy_show.value) proxyShow()
}
const getCaptchaCreate = (reload = true) => {
  if (reload) {
    if (Number($store.config['后台密码登录验证']) === 1) {
      ImageCaptchaCreate()
    }
  }
}
onMounted(() => {
  routerChange($router.currentRoute.value.query)
  getCaptchaCreate()
  AdminQuit()
})
const captcha_loading = ref(false)
const ImageCaptchaCreate = async () => {
  if (captcha_loading.value) return
  captcha_loading.value = true
  const response = await $api('AdminImageCaptchaCreate')
  captcha_loading.value = false
  $response(response, () => {
    login_input_data.value = {
      ...login_input_data.value,
      ...response.data
    }
  })
}

const loading_active = ref(false)
const AdminLogin = async () => {
  const account_ = login_input_data.value.account.replace(/^\s+|\s+$/g, "")
  if (account_ === '') return window.$message().error('请输入账号')
  if (login_input_data.value.password === '') return window.$message().error('请输入密码')
  if (loading_active.value) return
  loading_active.value = true
  const response = await $api('AdminAdminLogin', {
    account: account_,
    password: login_input_data.value.password,
    code: login_input_data.value.code,
    hash: login_input_data.value.hash,
    time: login_input_data.value.time,
    uuid: login_input_data.value.uuid,
  })
  loading_active.value = false
  $response(response, () => {
    const token = response.data.token
    saveToken(token)
  })
}
const local_token = ref(false)
const saveToken = (token) => {
  if (local_token.value) {
    $save_token_type.value = 'local'
    $session_token.value = ''
    $token.value = token
  } else {
    $save_token_type.value = 'session'
    $session_token.value = token
    $token.value = ''
  }
  getInfo()
  $router.push(decodeURIComponent(page_options.value.f))
}

const AdminQuit = async () => {
  if (!$token.value && !$session_token.value) return
  const response = await $api('AdminAdminQuit')
  $response(response, () => {
    if ($save_token_type.value === 'local') {
      $token.value = ''
    } else {
      $session_token.value = ''
    }
  })
}
const api_list = ref([])
const proxyShow = () => {
  api_list.value = JSON.parse(JSON.stringify($config.value.url))
  $proxy_show.value = true
}

const add_api_data = ref({
  name: '',
  api_map_url: '',
  base_assets_url: '',
  active: false
})

const addApiClick = () => {
  if (!add_api_data.value.name) return window.$message().error('请输入名称')
  if (!add_api_data.value.api_map_url) return window.$message().error('请输入接口地址')
  if (add_api_data.value.api_map_url.indexOf('http') !== 0) return window.$message().error('请输入正确的接口地址')
  if (!add_api_data.value.base_assets_url) return window.$message().error('请输入资源地址')
  if (add_api_data.value.base_assets_url.indexOf('http') !== 0) return window.$message().error('请输入正确的资源地址')
  if (add_api_data.value.active) {
    api_list.value.forEach((item) => {
      item.active = false
    })
  }
  api_list.value.unshift(JSON.parse(JSON.stringify(add_api_data.value)))
  add_api_data.value = {
    name: '',
    api_map_url: '',
    base_assets_url: '',
    active: false
  }
}

const apiDelClick = (k) => {
  if (api_list.length === 1) return
  let active = api_list.value[k]
  api_list.value.splice(k, 1)
  if (!!active) {
    api_list.value[0].active = true
  }
}
const apiActiveChange = (e, k) => {
  if (!!e) {
    api_list.value.forEach((item) => {
      item.active = false
    })
    api_list.value[k].active = true
  } else {
    api_list.value[k].active = false
    api_list.value[0].active = true
  }
}

const apiSaveClick = () => {
  const active_item = $config.value.api.url.filter((item) => {
    return item.active
  })
  const change_item = api_list.value.filter((item) => {
    return item.active
  })
  $config.value.api.url = JSON.parse(JSON.stringify(api_list.value))
  $proxy_show.value = false
  if (change_item[0].url !== active_item[0].url) {
    if (page_options.value.m === 1) {
      window.location.href = window.location.origin + window.location.pathname + '#/login'
    } else {
      window.location.reload()
    }

  }
}

const apiResetClick = () => {
  window.$box.confirm(
      '确定要重置代理配置吗？',
      '提示',
      {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
    localStorage.setItem('APP_CONFIG', JSON.stringify(window.CONFIG_RESET_DATA))
    window.location.reload()
  }).catch(() => {
  })
}

const reloadClick = () => {
  window.location.reload()
}

</script>
<template>
  <div>
    <div class="login_wrapper" :style="{
      backgroundImage: 'url(' + $image($store.config['Login背景图']) + ')'
    }">
      <el-card :body-style="{ padding: '0px' }">
        <div class="login_card_wrapper">
          <div class="login_cover_wrapper" :style="{
            backgroundImage: 'url(' + $image($store.config['Login欢迎图片']) + ')',
            backgroundColor: $store.config['Login背景色']
          }">
          </div>
          <div>
            <div v-loading="loading_active" class="login_input_wrapper">
              <div class="login_input_title_wrapper">登录</div>
              <div class="login_input_box_wrapper">
                <div class="login_input_line_wrapper">
                  <el-input v-model="login_input_data.account" class="login_input_input_wrapper"
                            placeholder="请输入登录账号">
                    <template #prefix>
                      <el-icon>
                        <Icon type="people"></Icon>
                      </el-icon>
                    </template>
                  </el-input>
                </div>
                <div class="login_input_line_wrapper">
                  <el-input type="password" v-model="login_input_data.password" class="login_input_input_wrapper"
                            placeholder="请输入登录密码">
                    <template #prefix>
                      <el-icon>
                        <Icon type="lock"></Icon>
                      </el-icon>
                    </template>
                  </el-input>
                </div>
                <div class="login_input_line_wrapper" v-if="Number($store.config['后台密码登录验证']) === 0"></div>
                <div class="login_input_line_wrapper" v-if="Number($store.config['后台密码登录验证']) === 1">
                  <el-input v-model="login_input_data.code" class="login_input_code_input_wrapper"
                            placeholder="请输入验证码">
                    <template #prefix>
                      <el-icon>
                        <Icon type="protect"></Icon>
                      </el-icon>
                    </template>
                  </el-input>
                  <div v-loading="captcha_loading && !login_input_data.image"
                       @click="ImageCaptchaCreate()"
                       class="login_input_code_image_wrapper">
                    <div w-full h-full v-if="!!login_input_data.image" :style="{
                    backgroundImage: 'url(' + $image(login_input_data.image) + ')'
                  }"></div>
                  </div>
                </div>
                <div class="login_checkbox_wrapper">
                  <el-checkbox v-model="local_token"/>
                  <div ml-2>记住密码</div>
                </div>
                <div text-center mt-4>
                  <el-button @click="AdminLogin()" v-loading="loading_active" class="login_button_wrapper"
                             type="primary">登录
                  </el-button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </el-card>

      <el-dialog v-model="$proxy_show" title="代理设置"
                 :close-on-click-modal="false">
        <div>
          <table class="table-fixed">
            <thead>
            <tr>
              <th>状态</th>
              <th class="w-1/4">名称</th>
              <th class="w-1/2">接口地址</th>
              <th class="w-1/2">资源地址</th>
              <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <tr>
              <td>
                <el-switch size="small" v-model="add_api_data.active"/>
              </td>
              <td>
                <el-input size="small" v-model="add_api_data.name" class="api_name_input_wrapper"
                          placeholder="请输入名称">
                </el-input>
              </td>
              <td>
                <el-input size="small" v-model="add_api_data.api_map_url" class="api_url_input_wrapper"
                          placeholder="请输入接口地址">
                </el-input>
              </td>
              <td>
                <el-input size="small" v-model="add_api_data.base_assets_url" class="api_url_input_wrapper"
                          placeholder="请输入资源地址">
                </el-input>
              </td>
              <td>
                <el-button @click="addApiClick()" type="primary" size="small">
                  <el-icon>
                    <Icon type="plus"></Icon>
                  </el-icon>
                </el-button>
              </td>
            </tr>
            <tr v-for="(i,k) in api_list" :key="k">
              <td>
                <el-switch size="small" v-model="api_list[k].active" @change="(e)=>{apiActiveChange(e,k)}"/>
              </td>
              <td>
                <el-input size="small" class="api_name_input_wrapper" v-model="api_list[k].name" placeholder="名称">
                </el-input>
              </td>
              <td>
                <el-input size="small" class="api_url_input_wrapper" v-model="api_list[k].api_map_url"
                          placeholder="接口地址">
                </el-input>
              </td>
              <td>
                <el-input size="small" class="api_url_input_wrapper" v-model="api_list[k].base_assets_url"
                          placeholder="资源地址">
                </el-input>
              </td>
              <td>
                <el-button @click="apiDelClick(k)" :disabled="api_list.length === 1" type="danger" size="small">
                  <el-icon>
                    <Icon type="delete"></Icon>
                  </el-icon>
                </el-button>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
        <template #footer>
          <div class="dialog-footer">
            <el-button @click="apiResetClick()">重置</el-button>
            <el-button @click="$proxy_show = false">关闭</el-button>
            <el-button @click="apiSaveClick()" type="primary">
              保存
            </el-button>
          </div>
        </template>
      </el-dialog>

      <div class="api_mana_wrapper">
        <el-button @click="proxyShow()" text>
          <Icon type="link-four"></Icon>
        </el-button>
      </div>
    </div>
  </div>
</template>
<style scoped>
.api_name_input_wrapper {
  width: 170px;
}

.api_url_input_wrapper {
  width: 300px;
}

.api_mana_wrapper {
  position: fixed;
  right: 50px;
  bottom: 50px;
}

.login_button_wrapper {
  width: 320px;
  height: 40px;
  margin: 0 auto;
}

.login_checkbox_wrapper {
  display: flex;
  align-items: center;
  font-size: 14px;
  margin-left: 50px;
  margin-top: 15px;
}

.login_input_box_wrapper {
  margin-top: 60px;
}

.login_input_line_wrapper {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 320px;
  margin: 20px auto 0;
  height: 40px;
}

.login_input_code_input_wrapper {
  width: 180px;
  height: 40px;
}

.login_input_input_wrapper {
  width: 320px;
  height: 40px;
}

.login_input_code_image_wrapper {
  width: 130px;
  height: 40px;
  background-repeat: no-repeat;
  background-size: cover;
  border-radius: 4px;
  border: 1px solid #dcdfe6;
  box-sizing: border-box;
  overflow: hidden;
}

.login_input_title_wrapper {
  font-size: 24px;
  color: #1d1d1f;
  margin-top: 35px;
  margin-left: 50px;
}

.login_input_wrapper {
  width: 420px;
  height: 450px;
  overflow: hidden;
}

.login_cover_wrapper {
  width: 500px;
  height: 450px;
  text-align: center;
  background-repeat: no-repeat;
  background-position: center;
  background-size: cover;
  overflow: hidden;
}

.login_card_wrapper {
  width: 920px;
  display: flex;
}

.login_wrapper {
  min-height: 100vh;
  background-repeat: no-repeat;
  background-position: center;
  background-size: cover;
  display: flex;
  justify-content: center;
  align-items: center;
}
</style>
<route>
{"meta":{"layout":"none","title":"登录"}}
</route>

