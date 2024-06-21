<script setup>
	/**
	 * name：
	 * user：sa0ChunLuyu
	 * date：2022-04-20 08:38:17
	 */
	import {
		ref
	} from 'vue'
	const user_avatar = ref('')
	const onChooseAvatar = (e) => {
		let avatar = e.detail.avatarUrl
		if (avatar.indexOf('thirdwx.qlogo.cn') === -1) {
			wx.getFileSystemManager().readFile({
				filePath: avatar,
				encoding: 'base64',
				success: res => {
					let base64 = 'data:image/png;base64,' + res.data;
					user_avatar.value = base64
				}
			});
		} else {
			uni.request({
				url: avatar,
				method: 'GET',
				responseType: 'arraybuffer',
				success: res => {
					let base64 = 'data:image/png;base64,' + uni.arrayBufferToBase64(res.data);
					user_avatar.value = base64
				}
			});
		}
	}
</script>
<template>
  <uni-section title="用户头像" type="line">
  	<view class="uni-ma-5 uni-pb-5 example_item_wrapper">
  	  <image v-if="user_avatar" :src="user_avatar"></image>
      <button size="mini" open-type="chooseAvatar"
      	@chooseavatar="onChooseAvatar">获取头像</button>
  	</view>  
  </uni-section>
</template>
<style scoped>
	.avatar_wrapper {
		width: 140rpx;
		height: 140rpx;
		position: relative;
	}
</style>
