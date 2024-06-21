<script setup>
	/**
	 * name：
	 * user：sa0ChunLuyu
	 * date：2022-04-19 19:07:23
	 */
	import SonComponent from './son.vue'
	import {
		ref
	} from "vue"
	const fa_value = ref('fa_value')
	const son_ref = ref(false)
	const SonRef = (e) => {
		son_ref.value = e
	}
	const sonFunc = () => {
		son_ref.value.sonFunc()
	}

	const faFunc = () => {
		uni.$lu.toast('faFunc');
	}
</script>
<template>
  <uni-section title="子父组件" type="line">
  	<view class="uni-ma-5 uni-pb-5 example_item_wrapper">
      <uni-easyinput :clearable="false" v-model="fa_value"/>
      <uni-easyinput class="uni-mt-2" :clearable="false" v-model="son_ref.son_value"/>
      <button size="mini" @click="sonFunc()">子函数</button>
      
      <SonComponent @faFunc="faFunc" :fa_value="fa_value" :ref="SonRef"></SonComponent>
  	</view>  
  </uni-section>
</template>
<style scoped>
	.son_input_wrapper{
		margin-top: 5rpx;
	}
</style>
