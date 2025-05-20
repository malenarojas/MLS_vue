import { defineStore } from 'pinia';
export const useCurrencyStore = defineStore('currencyStore', {
  state: () => ({
    selectedCurrency: []
  }),
  actions: {
    setCurrency(currency) {
      // Aquí guardás el objeto completo (Proxy(Object) con id, name, symbol, etc.)
      this.selectedCurrency = currency;
      console.log("✅ Moneda seteada en el store:", this.selectedCurrency);
    },
  },
  persist: true,
});
