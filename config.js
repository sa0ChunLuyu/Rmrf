const app_type = 'gzh'
const config = {
	api_map_url: 'http://rmrf.pi.sa0.online/api/get?client=' + app_type,
	base_assets_url: 'http://rmrf.pi.sa0.online',
}
uni.$config = JSON.parse(JSON.stringify(config))
export default {
	title: '入魔入佛',
	success_code: 200,
	loading: false,
	loading_text: '网络请求中，请稍等',
	error_message: '网络请求发生错误',
	appid: 'wx2dd4550847b258c5',
	app_type: app_type,
	config: config
}