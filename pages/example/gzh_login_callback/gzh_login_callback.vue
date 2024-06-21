<script setup>
	/**
	 * name：
	 * user：sa0ChunLuyu
	 * date：2023年8月14日 06:41:42
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

	const $props = defineProps({
		code: {
			type: String,
			default: ''
		}
	});

	const user_code = ref('')
	const WeChatLoginTest = async () => {
		if (!$props.code) return
		const response = await $api('ExampleGzhLogin', {
			code: $props.code
		})
		$response(response, () => {
			user_code.value = JSON.stringify(response.data.info, null, 4)
		})
	}

	const copyContent = () => {
		uni.setClipboardData({
			data: user_code.value
		})
	}

	const toHome = () => {
		uni.switchTab({
			url: '/pages/main/index/index'
		})
	}

	onShow(() => {
		WeChatLoginTest()
	})
</script>
<template>
	<view>
		<textarea :maxlength="-1" class="textarea_wrapper" v-model="user_code" />
		<view class="button_line_wrapper">
			<button size="mini" @click="copyContent()">复制</button>
			<button size="mini" @click="toHome()">首页</button>
		</view>
		<view class="bottom_blank_wrapper"></view>
	</view>
</template>
<style scoped>
	.button_line_wrapper {
		display: flex;
		justify-content: space-around;
	}

	.textarea_wrapper {
		margin-top: 100rpx;
		height: 400rpx;
		margin: 0 auto;
	}
</style>