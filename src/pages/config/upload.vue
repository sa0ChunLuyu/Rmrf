<script setup>
/**
 * name：
 * user：sa0ChunLuyu
 * date：2023年8月10日 10:20:24
 */
import {
  $api, $response, $image, $base64
} from '~/api'
import $router from '~/router'
import {onBeforeRouteUpdate} from "vue-router";
import {$copy} from "~/tool/copy";

const default_page_options = {
  search: '',
  time: [null, null],
  ext: '',
  from: '',
  page: 1,
}
const page_options = ref(JSON.parse(JSON.stringify(default_page_options)))
onBeforeRouteUpdate((to) => {
  routerChange(to.query)
})
const table_list = ref([])
const last_page = ref(0)
const UploadList = async () => {
  edit_data.value = JSON.parse(JSON.stringify(default_data))
  table_ref.value.setCurrentRow(null)
  const response = await $api('AdminUploadList', page_options.value)
  $response(response, () => {
    table_list.value = response.data.list.data
    last_page.value = response.data.list.last_page
  })
}

const routerChange = (query) => {
  page_options.value = {
    search: query.search || default_page_options.search,
    time: (!!query.time && query.time !== 'null') ? JSON.parse(query.time) : default_page_options.time,
    ext: query.ext || default_page_options.ext,
    from: query.from || default_page_options.from,
    page: Number(query.page) || default_page_options.page,
  }
  UploadList()
}

onMounted(() => {
  routerChange($router.currentRoute.value.query)
  UploadSearch()
})
const ext_arr = ref([])
const from_arr = ref([])
const UploadSearch = async () => {
  const response = await $api('AdminUploadSearch')
  $response(response, () => {
    ext_arr.value = response.data.ext
    from_arr.value = response.data.from
  })
}
const searchClick = (page = 1) => {
  page_options.value.page = page
  let query = JSON.parse(JSON.stringify(page_options.value))
  query.time = JSON.stringify(query.time)
  $router.push({query})
}
const searchClearClick = () => {
  let query = JSON.parse(JSON.stringify(default_page_options))
  query.time = JSON.stringify(query.time)
  $router.push({query})
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
const default_data = {
  id: 0,
}
const edit_data = ref(JSON.parse(JSON.stringify(default_data)))
const UploadDelete = async () => {
  const response = await $api('AdminUploadDelete', {
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
      '是否确认删除该上传文件？',
      '提示',
      {
        confirmButtonText: '确认',
        cancelButtonText: '取消',
        type: 'warning',
      }
  ).then(() => {
    UploadDelete()
  }).catch(() => {
  })
}
const image_arr = ['png', 'jpeg', 'jpg', 'gif']

const fileChange = async (e) => {
  if (e.size > 1024 * 1024 * 2) return window.$message().error('图片大小不能超过2M')
  await UploadImage(await $base64(e.raw))
}
const UploadImage = async (base64) => {
  const response = await $api('AdminUploadImage', {
    base64
  })
  $response(response, () => {
    UploadList()
  })
}

const copyLinkClick = (link) => {
  $copy(link, () => {
    window.$message().success('复制成功')
  })
}
</script>
<template>
  <div>
    <el-card>
      <template #header>上传管理</template>
      <div>
        <div class="input_line_wrapper">
          <div my-1 class="input_line_tag_wrapper">
            <el-tag disable-transitions w-full type="info">搜索</el-tag>
          </div>
          <div ml-2 my-1>
            <el-input @keydown.enter="searchClick()" class="input_line_input_wrapper" v-model="page_options.search"
                      placeholder="请输入搜索"></el-input>
          </div>
          <div ml-2 my-1 class="input_line_tag_wrapper">
            <el-tag disable-transitions w-full type="info">时间范围</el-tag>
          </div>
          <div ml-2 my-1>
            <el-date-picker v-model="page_options.time" type="daterange" range-separator="至"
                            start-placeholder="开始时间" end-placeholder="结束时间" value-format="YYYY-MM-DD"/>
          </div>
          <div ml-2 my-1 class="input_line_tag_wrapper">
            <el-tag disable-transitions w-full type="info">类型</el-tag>
          </div>
          <div ml-2 my-1>
            <el-select v-model="page_options.ext" class="input_line_input_wrapper"
                       placeholder="请选择类型">
              <el-option label="全部" value=""/>
              <el-option v-for="(i,k) in ext_arr" :key="k" :label="i.ext" :value="i.ext"/>
            </el-select>
          </div>
          <div ml-2 my-1 class="input_line_tag_wrapper">
            <el-tag disable-transitions w-full type="info">来源</el-tag>
          </div>
          <div ml-2 my-1>
            <el-select v-model="page_options.from" class="input_line_input_wrapper"
                       placeholder="请选择来源">
              <el-option label="全部" value=""/>
              <el-option v-for="(i,k) in from_arr" :key="k" :label="i" :value="i"/>
            </el-select>
          </div>
          <el-button my-1 @click="searchClick()" ml-3 type="primary">搜索</el-button>
          <el-button my-1 @click="searchClearClick()" type="warning">清空</el-button>
        </div>
        <div mt-1 class="button_group_wrapper">
          <el-dropdown>
            <el-button type="primary">
              上传
              <el-icon ml-2>
                <Icon type="down"></Icon>
              </el-icon>
            </el-button>
            <template #dropdown>
              <el-dropdown-menu>
                <el-dropdown-item>
                  <el-upload :auto-upload="false" :show-file-list="false" @change="fileChange">
                    上传图片
                  </el-upload>
                </el-dropdown-item>
              </el-dropdown-menu>
            </template>
          </el-dropdown>
          <el-button ml-2 :disabled="edit_data.id === 0" @click="deleteClick()" type="danger">删除</el-button>
        </div>
        <el-table row-class-name="cursor-pointer" mt-2 border :data="table_list" highlight-current-row
                  style="width: 100%"
                  @row-click="tableRowClick" :ref="tableRef">
          <el-table-column type="expand">
            <template #default="props">
              <div v-if="!!props.row.url">
                <div v-if="image_arr.indexOf(props.row.ext) !== -1">
                  <div class="table_image_wrapper">
                    <el-image :preview-src-list="[$image(props.row.url)]" class="image_box_wrapper" fit="contain"
                              :src="$image(props.row.url)" preview-teleported>
                      <template #error>
                        <div class="image_error_wrapper">暂无图片</div>
                      </template>
                    </el-image>
                  </div>
                </div>
                <div class="no_preview_wrapper" v-else>暂无预览</div>
                <div text-center>
                  <el-dropdown mt-2>
                    <el-button type="primary">
                      复制链接
                      <el-icon ml-2>
                        <Icon type="down"></Icon>
                      </el-icon>
                    </el-button>
                    <template #dropdown>
                      <el-dropdown-menu>
                        <el-dropdown-item @click="copyLinkClick($image(props.row.url))">完整链接</el-dropdown-item>
                        <el-dropdown-item @click="copyLinkClick(props.row.url)">不带域名</el-dropdown-item>
                      </el-dropdown-menu>
                    </template>
                  </el-dropdown>
                </div>
              </div>
              <div class="no_preview_wrapper" v-else>暂无预览</div>
            </template>
          </el-table-column>
          <el-table-column label="UUID" prop="uuid"></el-table-column>
          <el-table-column label="文件名" prop="name"></el-table-column>
          <el-table-column label="来源" prop="from" width="200"></el-table-column>
          <el-table-column label="大小" width="100">
            <template #default="scope">
              {{ scope.row.size }}MB
            </template>
          </el-table-column>
          <el-table-column label="类型" prop="ext" width="60"></el-table-column>
          <el-table-column label="MD5" prop="md5" width="290"></el-table-column>
          <el-table-column label="上传时间" prop="created_at" width="170"></el-table-column>
        </el-table>
        <el-pagination v-if="last_page > 1" :current-page="page_options.page" mt-2 background layout="prev, pager, next"
                       :page-count="last_page" @update:current-page="searchClick"/>
      </div>
    </el-card>
  </div>
</template>
<style scoped>
.no_preview_wrapper {
  line-height: 100px;
  text-align: center;
  color: #999999;
}

.table_image_wrapper {
  width: 200px;
  aspect-ratio: 1/1;
  background: #cccccc;
  border-radius: 6px;
  overflow: hidden;
  position: relative;
  margin: 0 auto;
}

.image_box_wrapper {
  width: 100%;
  aspect-ratio: 1/1;
  position: relative;
  background-image: linear-gradient(45deg, #eee 25%, transparent 25%, transparent 75%, #eee 75%), linear-gradient(45deg, #eee 25%, transparent 25%, transparent 75%, #eee 75%);
  background-size: 16px 16px;
  background-position: 0 0, 8px 8px;
}

.image_error_wrapper {
  position: absolute;
  left: 50%;
  top: 50%;
  transform: translate(-50%, -50%);
  color: #333333;
}

.button_group_wrapper {
  display: flex;
}
</style>
<route>
{"meta":{"title":"上传管理"}}
</route>
