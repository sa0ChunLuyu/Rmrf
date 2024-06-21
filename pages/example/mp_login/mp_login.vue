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
		$response
	} from '@/api'
	const user_code = ref('')
	const wxGetUserInfo = (res) => {
		if (!res.detail.iv) {
			uni.$lu.toast("您取消了授权,登录失败");
			return false;
		}
		uni.login({
			provider: 'weixin',
			success: (loginRes) => {
				let d = loginRes
				user_code.value = JSON.stringify(d, null, 4)
				drawer_ref.value.open()
			},
		});
	}

	const drawer_ref = ref(null)
	const drawerRef = (e) => {
		drawer_ref.value = e
	}

	const copyContent = () => {
		uni.setClipboardData({
			data: user_code.value
		})
	}
	const WeChatLoginTest = async () => {
		const user_code_data = JSON.parse(user_code.value)
		const response = await WeChatLoginTestAction({
			code: user_code_data.code,
			app_id: uni.$lu.config.app_id
		})
		$response(response, () => {
			user_code.value = JSON.stringify(response.data.info, null, 4)
		})
	}
</script>
<template>
	<uni-drawer :ref="drawerRef" mode="right">
		<view class="navbar_wrapper"></view>
		<textarea :maxlength="-1" class="textarea_wrapper" v-model="user_code" />
		<view class="button_line_wrapper">
			<button size="mini" @click="copyContent()">复制</button>
			<button size="mini" @click="WeChatLoginTest()">登录</button>
		</view>
	</uni-drawer>

	<uni-section title="用户Code" type="line">
		<view class="uni-ma-5 uni-pb-5 example_item_wrapper">
			<button size="mini" open-type="getUserInfo" @getuserinfo="wxGetUserInfo" withCredentials="true">获取Code</button>
		</view>
	</uni-section>
</template>
<style scoped>
	.button_line_wrapper {
		display: flex;
		justify-content: space-around;
	}

	.textarea_wrapper {
		margin-top: 100rpx;
		height: 400rpx;
	}
</style>