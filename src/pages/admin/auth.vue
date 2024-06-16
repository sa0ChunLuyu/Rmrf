<script setup>
/**
 * name：
 * user：sa0ChunLuyu
 * date：2023年7月30日 17:58:02
 */
import {
  $api,
  $response
} from '~/api'

const admin_auth_group = ref([])
const AdminAuthGroupList = async () => {
  const response = await $api('AdminAdminAuthGroupList')
  $response(response, () => {
    admin_auth_group.value = response.data.list.map((item) => {
      item.admin_auths = JSON.parse(item.admin_auths).map(Number)
      return {
        ...item,
        count: item.admin_auths.length
      }
    })
  })
}
const edit_show = ref(false)
const default_data = {
  id: 0,
  name: '',
  remark: '',
  admin_auths: [],
  status: 1
}
const edit_data = ref(JSON.parse(JSON.stringify(default_data)))
const editClick = async (type) => {
  if (admin_auth.value.length === 0) await AdminAuthChoose()
  if (type === 0) {
    table_ref.value.setCurrentRow(null)
    edit_data.value = JSON.parse(JSON.stringify(default_data))
    admin_auth.value.forEach((item) => {
      item.info.indeterminate = false
      item.info.check = false
    })
  } else {
    const adminAuthSet = new Set(edit_data.value.admin_auths)
    admin_auth.value.forEach((auth, index) => {
      let ids = auth.list.map(item => item.id)
      const is_subset = ids.every(item => adminAuthSet.has(item))
      const is_not_included = ids.every(item => !adminAuthSet.has(item))
      admin_auth.value[index].info.indeterminate = is_not_included ? false : !is_subset
      admin_auth.value[index].info.check = is_subset
    })
  }
  edit_show.value = true
}
const admin_auth = ref([])
const AdminAuthChoose = async () => {
  const response = await $api('AdminAdminAuthChoose')
  $response(response, () => {
    admin_auth.value = response.data.list.map((item) => {
      item.info.indeterminate = false
      item.info.check = false
      return item
    })
  })
}
const editDoneClick = async () => {
  let response
  let data = JSON.parse(JSON.stringify(edit_data.value))
  data.admin_auths = JSON.stringify(data.admin_auths.map(String))
  if (data.id === 0) {
    response = await $api('AdminAdminAuthGroupCreate', data)
  } else {
    response = await $api('AdminAdminAuthGroupUpdate', data)
  }
  $response(response, () => {
    edit_show.value = false
    table_ref.value.setCurrentRow(null)
    edit_data.value = JSON.parse(JSON.stringify(default_data))
    AdminAuthGroupList()
    window.$message().success(data.id === 0 ? '添加成功' : '修改成功')
  })
}
const table_ref = ref(null)
const tableRef = (e) => {
  table_ref.value = e
}
const handleCheckedAuthChange = (e, scope) => {
  let adminAuths = edit_data.value.admin_auths
  let ids = scope.row.list.map(item => item.id)
  let is_subset = ids.every(item => adminAuths.includes(item))
  let is_not_included = ids.every(item => !adminAuths.includes(item))
  let adminAuth = admin_auth.value[scope.$index]
  adminAuth.info.indeterminate = is_not_included ? false : !is_subset
  adminAuth.info.check = is_subset
}
const handleCheckAllChange = (e, scope) => {
  let ids = scope.row.list.map(item => item.id)
  const result = edit_data.value.admin_auths.filter((element) => !ids.includes(element))
  admin_auth.value[scope.$index].info.indeterminate = false
  if (e) {
    edit_data.value.admin_auths = [...result, ...ids]
  } else {
    edit_data.value.admin_auths = result
  }
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
const deleteClick = () => {
  window.$box.confirm(
      '是否确认删除该权限组？',
      '提示',
      {
        confirmButtonText: '确认',
        cancelButtonText: '取消',
        type: 'warning',
      }
  ).then(() => {
    AdminAuthGroupDelete()
  }).catch(() => {
  })
}
const AdminAuthGroupDelete = async () => {
  const response = await $api('AdminAdminAuthGroupDelete', {
    id: edit_data.value.id
  })
  $response(response, () => {
    window.$message().success('删除成功')
    edit_data.value = JSON.parse(JSON.stringify(default_data))
    table_ref.value.setCurrentRow(null)
    const index = admin_auth_group.value.findIndex(item => item.id === response.data.id)
    admin_auth_group.value.splice(index, 1)
  })
}
onMounted(() => {
  AdminAuthGroupList()
})
</script>
<template>
  <div>
    <el-dialog v-model="edit_show" :title="!!edit_data.id ? '编辑' : '新建'" width="800px"
               :close-on-click-modal="false"
               :close-on-press-escape="false"
               :show-close="false">
      <div>
        <div class="input_line_wrapper">
          <div class="input_line_tag_wrapper">
            <el-tag disable-transitions w-full type="info">名称</el-tag>
          </div>
          <div ml-2>
            <el-input class="input_line_input_wrapper" v-model="edit_data.name"
                      placeholder="请输入名称"></el-input>
          </div>
        </div>
        <div mt-2 class="input_line_wrapper">
          <div class="input_line_tag_wrapper">
            <el-tag disable-transitions w-full type="info">备注</el-tag>
          </div>
          <div ml-2>
            <el-input class="input_line_input_wrapper" v-model="edit_data.remark"
                      placeholder="请输入备注"></el-input>
          </div>
        </div>
        <div mt-2 class="input_line_wrapper">
          <div class="input_line_tag_wrapper">
            <el-tag disable-transitions w-full type="info">状态</el-tag>
          </div>
          <div ml-2>
            <el-select class="input_line_input_wrapper" v-model="edit_data.status" placeholder="请选择状态">
              <el-option label="可用" :value="1"/>
              <el-option label="停用" :value="2"/>
            </el-select>
          </div>
        </div>
        <div mt-2 class="input_line_wrapper">
          <div class="input_line_tag_wrapper">
            <el-tag disable-transitions w-full type="info">权限</el-tag>
          </div>
          <div ml-2>
            <el-button size="small" @click="AdminAuthChoose()" type="primary">刷新</el-button>
          </div>
        </div>
        <div mt-2 class="input_line_wrapper">
          <el-table border height="300" :data="admin_auth" style="width: 100%">
            <el-table-column property="name" label="类型" width="140">
              <template #default="scope">
                <el-checkbox
                    v-model="scope.row.info.check"
                    :indeterminate="scope.row.info.indeterminate"
                    @change="(e)=>{handleCheckAllChange(e,scope)}">
                  {{ scope.row.info.title }}
                </el-checkbox>
              </template>
            </el-table-column>
            <el-table-column label="权限">
              <template #default="scope">
                <el-checkbox-group @change="(e)=>{handleCheckedAuthChange(e,scope)}" v-model="edit_data.admin_auths">
                  <el-checkbox v-for="auth in scope.row.list" :key="auth.id" :label="auth.id">{{
                      auth.title
                    }}
                  </el-checkbox>
                </el-checkbox-group>
              </template>
            </el-table-column>
          </el-table>
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
      <template #header>权限管理</template>
      <div>
        <div>
          <el-button @click="editClick(0)" type="primary">新建</el-button>
          <el-button :disabled="edit_data.id === 0" @click="editClick(1)" type="success">编辑</el-button>
          <el-button :disabled="edit_data.id === 0" @click="deleteClick()" type="danger">删除</el-button>
        </div>
        <el-table row-class-name="cursor-pointer" mt-2 border :data="admin_auth_group" highlight-current-row
                  style="width: 100%"
                  @row-click="tableRowClick" :ref="tableRef">
          <el-table-column property="name" label="名称" width="300"/>
          <el-table-column property="count" label="权限数量" width="120"/>
          <el-table-column property="remark" label="备注"/>
          <el-table-column label="状态" width="120">
            <template #default="scope">
              <el-tag disable-transitions :type="Number(scope.row.status) === 1 ? 'success' : 'warning'">
                {{ Number(scope.row.status) === 1 ? '可用' : '停用' }}
              </el-tag>
            </template>
          </el-table-column>
        </el-table>
      </div>
    </el-card>
  </div>
</template>
<style scoped>

</style>
<route>
{"meta":{"title":"权限管理"}}
</route>

