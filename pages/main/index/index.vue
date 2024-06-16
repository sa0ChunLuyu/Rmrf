<script setup>
	/**
	 * name：
	 * user：sa0ChunLuyu
	 * date：2023年8月13日 18:33:58
	 */
	import {
		ref
	} from 'vue'
	import {
		$api,
		$response
	} from '@/api'
	import {
		onShow
	} from '@dcloudio/uni-app'
	const title = uni.$lu.config.title
</script>
<template>
	<view>
		<view class="page_wrapper">
			<view>Yo {{ title }}</view>
		</view>
	</view>
</template>
<style scoped>
	.page_wrapper {
		position: absolute;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
	}
</style>