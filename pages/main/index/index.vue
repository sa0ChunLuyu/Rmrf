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
	const back = () => {
		uni.$lu.toast('返回');
	}

	const nav_list = ref([
		// #ifdef MP-WEIXIN
		{
			url: '/pages/example/mp_avatar/mp_avatar',
			title: '微信小程序头像'
		},
		{
			url: '/pages/example/mp_nickname/mp_nickname',
			title: '微信小程序昵称'
		},
		{
			url: '/pages/example/mp_pay/mp_pay',
			title: '微信小程序支付'
		},
		{
			url: '/pages/example/mp_phone/mp_phone',
			title: '微信小程序手机号'
		},
		{
			url: '/pages/example/mp_login/mp_login',
			title: '微信小程序登录'
		},
		// #endif
		// #ifdef H5
		{
			url: '/pages/example/gzh_login/gzh_login',
			title: '微信公众号登录'
		},
		// #endif
		{
			url: '/pages/example/pinia/pinia',
			title: 'pinia'
		},
		{
			url: '/pages/example/router_query/router_query?name=Yo',
			title: 'router_query'
		},
		{
			url: '/pages/example/ref/ref',
			title: 'ref'
		}
	])

	const navItemClick = (url) => {
		uni.navigateTo({
			url
		})
	}
</script>
<template>
	<view>
		<uni-nav-bar :border="false" fixed statusBar left-icon="left" @clickLeft="back" title="示例" />
		<view class="nav_wrapper">
			<!-- #ifdef MP-WEIXIN -->
			<view v-for="(i,k) in nav_list" :key="i" @click="navItemClick(i.url)" class="nav_item_wrapper">
				<div class="nav_item_title_wrapper">{{ i.title }}</div>
				<div class="nav_item_icon_wrapper">
					<uni-icons type="right" size="20"></uni-icons>
				</div>
			</view>
			<!-- #endif -->
		</view>
	</view>
</template>
<style>
	page {
		background: #ffffff;
	}
</style>
<style scoped>
	.nav_item_wrapper {
		height: 60px;
		line-height: 60px;
		display: flex;
		align-items: center;
		justify-content: space-between;
		width: 700rpx;
		margin: 0 auto;
		border-bottom: 1rpx #cccccc50 solid;
	}

	.nav_item_title_wrapper {
		font-size: 30rpx;
		font-weight: bold;
	}
</style>