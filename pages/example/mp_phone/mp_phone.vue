<script setup>
	/**
	 * name：
	 * user：sa0ChunLuyu
	 * date：2022-04-20 09:02:17
	 */
	import {
		ref
	} from 'vue'
	const user_phone = ref('')
	const phoneLoginButtonClick = (e) => {
		uni.login({
			provider: 'weixin',
			success: (loginRes) => {
				let d = {
					code: loginRes.code,
					iv: e.detail.iv,
					encrypted_data: e.detail.encryptedData,
				}
				user_phone.value = JSON.stringify(d, null, 4)
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
			data: user_phone.value
		})
	}
</script>
<template>
  <uni-drawer :ref="drawerRef" mode="right">
    <view class="navbar_wrapper"></view>
    <textarea :maxlength="-1" class="textarea_wrapper" v-model="user_phone" />
    <button size="mini" @click="copyContent()">复制</button>
  </uni-drawer>
  
  <uni-section title="用户手机号" type="line">
    <view class="uni-ma-5 uni-pb-5 example_item_wrapper">
      <button size="mini" open-type="getPhoneNumber" @getphonenumber="phoneLoginButtonClick"
      	withCredentials="true">获取手机号</button>
    </view>
  </uni-section>
</template>
<style scoped>
  .textarea_wrapper {
    margin-top: 100rpx;
    height: 400rpx;
  }
</style>
