import {defineStore} from 'pinia'

export const useStore = defineStore('main', {
  state: () => {
    return {
      api_map: {},
      loading: 0
    }
  }
})
export const useConfig = createGlobalState(() => useStorage('APP_CONFIG', JSON.parse(localStorage.getItem('APP_CONFIG') ?? '{}')))
