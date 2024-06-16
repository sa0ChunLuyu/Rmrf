import {
	createSSRApp
} from 'vue'
import * as Pinia from 'pinia';
import App from './App.vue'
import $lu from './lu'

uni.$lu = $lu
export function createApp() {
	const app = createSSRApp(App)
	app.use(Pinia.createPinia())
	return {
		app,
		Pinia
	}
}
