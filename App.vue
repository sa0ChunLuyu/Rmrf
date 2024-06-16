<script>
	export default {
		onLaunch: function() {
			console.log(`\n %c 鹿和 %c https://sa0.online/ \n\n`, 'color: #ffffff; background: #fd6b60; padding:5px 0;',
				'color: #fd6b60;background: #ffffff; padding:5px 0;')
		}
	}
</script>

<style lang="scss">
	/*每个页面公共css */
	@import '@/uni_modules/uni-scss/index.scss';
	/* #ifndef APP-NVUE */
	@import '@/static/customicons.css';

	// 设置整个项目的背景色
	page {
		background-color: #f5f5f5;
	}

	/* #endif */
	.bottom_blank_wrapper {
		height: calc(20rpx + var(--safe-area-inset-bottom));
	}

	.top_blank_wrapper::after {
		content: ".";
	}

	.top_blank_wrapper {
		height: 0;
		width: 100%;
		opacity: 0;
	}

	.navbar_blank_wrapper {
		height: calc(100rpx + var(--safe-area-inset-top));
	}

	.input_line_wrapper {
		display: flex;
		align-items: center;
		flex-wrap: wrap;
		justify-content: space-between;
	}

	.input_line_tag_wrapper {
		width: 200rpx;
		height: 60rpx;
		line-height: 60rpx;
		text-align: center;
		background: #f9f9f9;
		font-size: 26rpx;
		font-weight: bold;
		border-radius: 6rpx;
	}

	.input_line_input_wrapper {
		width: 500rpx;
	}
</style>