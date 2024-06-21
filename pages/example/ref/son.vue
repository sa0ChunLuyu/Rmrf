<script setup>
	/**
	 * name：
	 * user：sa0ChunLuyu
	 * date：2022-04-19 19:07:23
	 */
	import {
		ref
	} from "vue";
	const $emit = defineEmits(['faFunc'])
	const props = defineProps({
		fa_value: {
			type: String,
			default: ''
		}
	}); 
	const son_value = ref('son_value')
	const faFuncClick = () => {
		$emit('faFunc')
	}
	const sonFunc = () => {
		uni.$lu.toast('sonFunc');
	}
	defineExpose({
		son_value,
		sonFunc
	})
</script>
<template>
	<uni-easyinput :clearable="false" disabled v-model="props.fa_value"/>
	<uni-easyinput :clearable="false" v-model="son_value"/>
	<button size="mini" @click="faFuncClick()">父函数</button>
</template>
<style scoped>
	.son_input_wrapper {
		margin-top: 5rpx;
	}
</style>
