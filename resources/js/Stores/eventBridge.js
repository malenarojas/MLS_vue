import { defineStore } from 'pinia';

export const useEventBridgeStore = defineStore('eventBridge', {
  state: () => ({
    clearMapCallback: null,
    visibleListings: []
  }),

  actions: {
    setClearMapCallback(callback) {
      console.log('📌 Callback registrado en Pinia');
      this.clearMapCallback = callback;
    },
    updateVisibleListings(listings) {
        this.visibleListings = listings
    },

    triggerClearMapCallback() {
      console.log('📢 Trigger desde el mapa');
      if (typeof this.clearMapCallback === 'function') {
        this.clearMapCallback();
      } else {
        console.warn('⚠️ No hay callback registrado');
      }
    }
  }
});
