<script setup>
import zhCn from 'element-plus/dist/locale/zh-cn.mjs'

const button_config = {
  autoInsertSpace: true,
}
</script>
<template>
  <Mounting/>
  <el-config-provider :button="button_config" :locale="zhCn">
    <router-view/>
  </el-config-provider>
</template>
<style>
body {
  background: #ffffff;
}

svg {
  outline: none;
}

.input_line_wrapper {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
}

.input_line_tag_wrapper {
  width: 100px;
}

.input_line_input_wrapper {
  width: 200px !important;
}
</style>
