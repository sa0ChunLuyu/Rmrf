import {
	$post
} from '@/lu/axios.js'
import $config from '@/config.js'
import {
	useStore
} from '@/store'
const app_path = 'App'
let api_map_url = $config.config.api_map_url
let base_assets_url = $config.config.base_assets_url
export const $api = async (url_key, data = {}, opt = {}) => {
	const opt_data = {
		...$config,
		...opt,
	}
	const $store = useStore()
	if (!(url_key in $store.api_map)) {
		const api_map = await $post({
			url: opt_data.config.api_map_url
		}, opt_data)
		if (api_map.code !== 200) {
			uni.$lu.toast('获取接口失败')
			return false
		}
		$store.api_map = api_map.data.list
	}
	if (!(url_key in $store.api_map)) {
		uni.$lu.toast(`接口不存在 [${url_key}]`)
		return false
	}
	return await $post({
		url: $store.api_map[url_key],
		data
	}, opt_data)
}

export const $image = (path) => {
	const path_ret = ['http://', 'https://', ';base64,']
	for (let i = 0; i < path_ret.length; i++) {
		if (path.indexOf(path_ret[i]) !== -1) {
			return path
		}
	}
	return `${$config.config.base_assets_url}${path}`
}

export const $response = (response, then, opt = {}, error = () => {}) => {
	if (response) {
		const opt_data = {
			...$config,
			...opt,
		}
		if (response.code != opt_data.success_code) {
			uni.$lu.toast(response.message);
			error()
			return
		}
		then()
	}
}