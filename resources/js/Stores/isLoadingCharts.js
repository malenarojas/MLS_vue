import { defineStore } from 'pinia'

export const useIsLoadingStore = defineStore('filtro', {
  state: () => ({
    isLoading: false,
  }),
  actions: {
    setLoading(isLoading) {
      this.isLoading = isLoading;
    },
		
  },
});
