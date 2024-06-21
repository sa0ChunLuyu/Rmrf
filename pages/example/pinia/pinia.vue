<script setup>
	/**
	 * name：
	 * user：sa0ChunLuyu
	 * date：2022-04-19 18:57:41
	 */
  import {
    useStore
  } from '@/store'
	const $store = useStore()
</script>
<template>
	<uni-section title="Pinia" type="line">
		<view class="uni-ma-5 uni-pb-5 example_item_wrapper">
			<view class="button_wrapper">
				<button size="mini" @click="$store.count++">增加</button>
				<view class="break_wrapper"> {{ $store.count }} </view>
				<button size="mini" @click="$store.count--">减少</button>
			</view>
		</view>
	</uni-section>
</template>
<style scoped>
	.break_wrapper {
		margin: 0 10rpx;
	}

	.button_wrapper {
		align-items: center;
		display: flex;
		justify-content: center;
	}
</style>
