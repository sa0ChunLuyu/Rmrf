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
	if (!('delete_token' in opt)) {
		const token = getToken() ? getToken() : '';
		header['Authorization'] = 'Bearer ' + token
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