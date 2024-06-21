const app_type = 'mp'
const config = {
	api_map_url: 'http://rmrf.pi.sa0.online/api/get?client=' + app_type,
	base_assets_url: 'http://rmrf.pi.sa0.online',
}
uni.$config = JSON.parse(JSON.stringify(config))
const config_str_key = "CONFIG_STR"
let config_str = uni.getStorageSync(config_str_key)
if (!config_str) {
	config_str = JSON.stringify(config)
	uni.setStorageSync(config_str_key, config_str)
}
const config_data = JSON.parse(config_str)
export default {
	title: '入魔入佛',
	success_code: 200,
	loading: false,
	loading_text: '网络请求中，请稍等',
	error_message: '网络请求发生错误',
	appid: 'wx0d92d2990ec16a55',
	app_type: app_type,
	config: config_data
}