<script setup>
	/**
	 * name：
	 * user：sa0ChunLuyu
	 * date：2022-04-20 09:02:17
	 */
	import {
		ref
	} from 'vue'
	import {
		$url,
		$api,
		$response
	} from '@/api'
	const appid = uni.$lu.config.appid
	const getApiMap = async () => {
		const response = await $api('Yo')
		$response(response, () => {

		})
	}
	const wxGetUserInfo = (jump) => {
		let state = encodeURIComponent($url('ExampleGzhJump'))
		let redirect_uri = encodeURIComponent($url('ExampleGzhAuth'));
		const url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' + appid + '&redirect_uri=' +
			redirect_uri + '&response_type=code&scope=snsapi_base&state=' + state + '#wechat_redirect';
		if (jump) {
			window.location = url;
		} else {
			console.log({
				appid,
				state,
				redirect_uri,
				url
			})
		}
	}
</script>
<template>
	<uni-section title="用户Code" type="line">
		<view class="uni-ma-5 uni-pb-5 example_item_wrapper">
			<button size="mini" @click="getApiMap">获取接口</button>
			<button class="login_button_wrapper" size="mini" @click="wxGetUserInfo(true)">获取Code 跳转</button>
			<button class="login_button_wrapper" size="mini" @click="wxGetUserInfo(false)">获取Code 打印</button>
		</view>
	</uni-section>
</template>
<style scoped>
	.login_button_wrapper {
		margin-left: 10px;
	}

	.button_line_wrapper {
		display: flex;
		justify-content: space-around;
	}

	.textarea_wrapper {
		margin-top: 100rpx;
		height: 400rpx;
	}
</style>