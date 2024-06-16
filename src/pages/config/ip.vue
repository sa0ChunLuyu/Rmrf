<script setup>
/**
 * name：
 * user：sa0ChunLuyu
 * date：2023年8月10日 10:20:24
 */
import {$api, $response} from '~/api'
import $router from '~/router'
import {onBeforeRouteUpdate} from "vue-router";

const default_page_options = {
  search: '',
  page: 1,
}
const page_options = ref(JSON.parse(JSON.stringify(default_page_options)))
onBeforeRouteUpdate((to) => {
  routerChange(to.query)
})
const table_list = ref([])
const last_page = ref(0)
const IpPoolList = async () => {
  edit_data.value = JSON.parse(JSON.stringify(default_data))
  table_ref.value.setCurrentRow(null)
  const response = await $api('AdminIpPoolList', page_options.value)
  $response(response, () => {
    table_list.value = response.data.list.data
    last_page.value = response.data.list.last_page
  })
}

const routerChange = (query) => {
  page_options.value = {
    search: query.search || default_page_options.search,
    page: Number(query.page) || default_page_options.page,
  }
  IpPoolList()
}

onMounted(() => {
  routerChange($router.currentRoute.value.query)
})
const searchClick = (page = 1) => {
  page_options.value.page = page
  $router.push({
    query: JSON.parse(JSON.stringify(page_options.value))
  })
}
const searchClearClick = () => {
  $router.push({
    query: JSON.parse(JSON.stringify(default_page_options))
  })
}
const table_ref = ref(null)
const tableRef = (e) => {
  table_ref.value = e
}
const tableRowClick = (e) => {
  if (e.id === edit_data.value.id) {
    edit_data.value = JSON.parse(JSON.stringify(default_data))
    table_ref.value.setCurrentRow(null)
  } else {
    edit_data.value = JSON.parse(JSON.stringify(e))
    table_ref.value.setCurrentRow(e)
  }
}

const edit_show = ref(false)
const default_data = {
  id: 0,
  ip: '',
  region: ''
}
const edit_data = ref(JSON.parse(JSON.stringify(default_data)))
const editClick = async (type) => {
  if (type === 0) {
    table_ref.value.setCurrentRow(null)
    edit_data.value = JSON.parse(JSON.stringify(default_data))
  }
  edit_show.value = true
}
const copy_show = ref(false)
const copy_data = ref(JSON.parse(JSON.stringify(default_data)))
const editDoneClick = async () => {
  let response
  let data = JSON.parse(JSON.stringify(edit_data.value))
  if (data.id === 0) {
    response = await $api('AdminIpPoolCreate', data)
  } else {
    data.password = 'placeholder'
    response = await $api('AdminIpPoolUpdate', data)
  }
  $response(response, () => {
    edit_show.value = false
    table_ref.value.setCurrentRow(null)
    IpPoolList()
    window.$message().success(data.id === 0 ? '创建成功' : '修改成功')
    edit_data.value = JSON.parse(JSON.stringify(default_data))
  })
}
const IpPoolDelete = async () => {
  const response = await $api('AdminIpPoolDelete', {
    id: edit_data.value.id
  })
  $response(response, () => {
    window.$message().success('删除成功')
    edit_data.value = JSON.parse(JSON.stringify(default_data))
    table_ref.value.setCurrentRow(null)
    const index = table_list.value.findIndex(item => item.id === response.data.id)
    table_list.value.splice(index, 1)
  })
}
const deleteClick = () => {
  window.$box.confirm(
      '是否确认删除该IP？',
      '提示',
      {
        confirmButtonText: '确认',
        cancelButtonText: '取消',
        type: 'warning',
      }
  ).then(() => {
    IpPoolDelete()
  }).catch(() => {
  })
}
</script>
<template>
  <div>
    <el-dialog v-model="edit_show" :title="!!edit_data.id ? '编辑' : '新建'" width="500px"
               :close-on-click-modal="false"
               :close-on-press-escape="false"
               :show-close="false">
      <div>
        <div class="input_line_wrapper">
          <div class="input_line_tag_wrapper">
            <el-tag disable-transitions w-full type="info">IP地址</el-tag>
          </div>
          <div ml-2>
            <el-input class="input_line_input_wrapper" v-model="edit_data.ip"
                      placeholder="请输入IP地址"></el-input>
          </div>
        </div>
        <div mt-2 class="input_line_wrapper">
          <div class="input_line_tag_wrapper">
            <el-tag disable-transitions w-full type="info">IP信息</el-tag>
          </div>
          <div ml-2>
            <el-input class="input_line_input_wrapper" v-model="edit_data. region"
                      placeholder="请输入IP信息"></el-input>
          </div>
        </div>
      </div>
      <template #footer>
        <div class="dialog-footer">
          <el-button @click="edit_show = false">关闭</el-button>
          <el-button @click="editDoneClick()" type="primary">保存</el-button>
        </div>
      </template>
    </el-dialog>

    <el-card>
      <template #header>IP解析库</template>
      <div>
        <div class="input_line_wrapper">
          <div my-1 class="input_line_tag_wrapper">
            <el-tag disable-transitions w-full type="info">搜索</el-tag>
          </div>
          <div ml-2 my-1>
            <el-input @keydown.enter="searchClick()" class="input_line_input_wrapper" v-model="page_options.search"
                      placeholder="IP地址/信息"></el-input>
          </div>
          <el-button @click="searchClick()" ml-3 type="primary">搜索</el-button>
          <el-button @click="searchClearClick()" type="warning">清空</el-button>
        </div>
        <div mt-1>
          <el-button @click="editClick(0)" type="primary">新建</el-button>
          <el-button :disabled="edit_data.id === 0" @click="editClick(1)" type="success">编辑</el-button>
          <el-button :disabled="edit_data.id === 0" @click="deleteClick()" type="danger">删除</el-button>
        </div>
        <el-table row-class-name="cursor-pointer" mt-2 border :data="table_list" highlight-current-row
                  style="width: 100%"
                  @row-click="tableRowClick" :ref="tableRef">
          <el-table-column label="IP地址" prop="ip"/>
          <el-table-column label="IP信息" prop="region"/>
        </el-table>
        <el-pagination v-if="last_page > 1" :current-page="page_options.page" mt-2 background layout="prev, pager, next"
                       :page-count="last_page" @update:current-page="searchClick"/>
      </div>
    </el-card>
  </div>
</template>
<style scoped>

</style>
<route>
{"meta":{"title":"IP解析库"}}
</route>
