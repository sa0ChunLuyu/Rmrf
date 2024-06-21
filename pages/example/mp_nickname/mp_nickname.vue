<script setup>
	/**
	 * name：
	 * user：sa0ChunLuyu
	 * date：2022-04-20 08:51:52
	 */
	import {
		ref
	} from 'vue'
	const user_nickname = ref('')
</script>
<template>
  <uni-section title="用户昵称" type="line">
  	<view class="uni-ma-5 uni-pb-5 example_item_wrapper">
  	  <input class="uni-input" type="nickname" v-model="user_nickname" placeholder="请输入昵称" />
  	</view>  
  </uni-section>
</template>
