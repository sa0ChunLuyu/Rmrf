const config = {
	api_map_url: 'http://rmrf.pi.sa0.online/api/get',
	base_assets_url: 'http://rmrf.pi.sa0.online',
}
uni.$config = JSON.parse(JSON.stringify(config))
export default {
	title: '入魔入佛',
	success_code: 200,
	loading: false,
	loading_text: '网络请求中，请稍等',
	error_message: '网络请求发生错误',
	config: config
}