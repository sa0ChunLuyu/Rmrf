import {
	defineStore
} from 'pinia';

export const useStore = defineStore('counter', {
	state: () => ({
		api_map: {},
		count: 0,
		loading: 0,
	}),
	actions: {
		loadingStart() {
			this.loading++
		},
		loadingDone() {
			this.loading--
			if (this.loading < 0) this.loading = 0
		}
	},
});