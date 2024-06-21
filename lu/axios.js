import {
	getToken
} from './token.js'
import {
	useStore
} from '@/store'
export const $post = async ({
	url,
	data = {}
}, opt) => {
	const $store = useStore()
	let header = {}
	if ('delete_token' in opt && !!opt.delete_token) {
		if (header['Authorization']) {
			delete header['Authorization']
		}
	} else {
		const token = getToken() ? getToken() : '';
		header['Authorization'] = 'Bearer ' + token
	}
	if ('delete_appid' in opt && !!opt.delete_appid) {
		if (data['UNIAPP_APPID']) {
			delete data['UNIAPP_APPID']
		}
	} else {
		data['UNIAPP_APPID'] = opt.appid
	}
	if ('delete_apptype' in opt && !!opt.delete_apptype) {
		if (data['UNIAPP_APPTYPE']) {
			delete data['UNIAPP_APPTYPE']
		}
	} else {
		data['UNIAPP_APPTYPE'] = opt.app_type
	}
	if (!!opt.loading) {
		$store.loadingStart()
		if ($store.loading === 1) uni.showLoading({
			title: opt.loading_text
		})
	}
	const res = await uni.request({
		url,
		method: 'POST',
		data,
		header
	});
	if (!!opt.loading) {
		$store.loadingDone()
		if ($store.loading === 0) uni.hideLoading()
	}
	if (!!res && res.data != '') {
		return res.data
	} else {
		uni.$lu.toast("请求发生错误")
		return false
	}
}