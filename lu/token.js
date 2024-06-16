import $config from '@/config.js'
const TokenKey = $config.token;
export function getToken() {
	return uni.getStorageSync(TokenKey);
}
export function setToken(token) {
	uni.setStorageSync(TokenKey, token);
}
export function delToken() {
	uni.removeStorageSync(TokenKey);
}