<script setup>
  /**
   * name：
   * user：sa0ChunLuyu
   * date：2022-04-19 15:47:14
   */
  const $props = defineProps({
    name: {
      type: String,
      default: ''
    }
  });
</script>
<template>
  <uni-section title="路由参数" type="line">
  	<view class="uni-ma-5 uni-pb-5 example_item_wrapper">
  	  {"name":"{{ $props.name }}"}
  	</view>  
  </uni-section>
</template>
