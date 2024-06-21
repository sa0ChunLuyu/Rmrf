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
		$api,
		$response
	} from '@/api'
	const pay_test = ref('')
	const payTest = (e) => {
		drawer_ref.value.open()
	}
	const payTestDo = () => {
		let pay_info = JSON.parse(pay_test.value);
		wx.requestPayment({
			'timeStamp': pay_info.timestamp,
			'nonceStr': pay_info.nonce_str,
			'package': pay_info.package,
			'signType': 'RSA',
			'paySign': pay_info.pay_sign,
			'success': function(res) {
				console.log(res)
			},
			'fail': function(res) {
				console.log(res)
			},
			'complete': function(res) {}
		});
	}

	const drawer_ref = ref(null)
	const drawerRef = (e) => {
		drawer_ref.value = e
	}

	const WeChatPayPayTest = async () => {
		const response = await $api('ExampleMpPay', {
			openid: JSON.parse(pay_test.value)['openid']
		})
		$response(response, () => {
			pay_test.value = JSON.stringify(response.data.info)
		})
	}
</script>
<template>
	<uni-drawer :ref="drawerRef" mode="right">
		<view class="navbar_wrapper"></view>
		<textarea :maxlength="-1" class="textarea_wrapper" v-model="pay_test" />
		<view class="button_line_wrapper">
			<button size="mini" @click="WeChatPayPayTest()">获取</button>
			<button size="mini" @click="payTestDo()">测试</button>
		</view>
	</uni-drawer>

	<uni-section title="支付测试" type="line">
		<view class="uni-ma-5 uni-pb-5 example_item_wrapper">
			<button size="mini" @click="payTest()">测试</button>
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