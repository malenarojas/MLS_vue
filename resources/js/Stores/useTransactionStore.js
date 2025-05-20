
import { defineStore } from 'pinia';
import { useLocalStorage } from '@vueuse/core';

export const useTransactionStepStore = defineStore('transactionStepStore', {
	state: () => ({
	  stepData: useLocalStorage("stepData", {}), 
	}),
	actions: {
	  saveStepData(transaction_id, step, data) {
		if (!this.stepData[transaction_id]) {
		  this.$patch((state) => {
			state.stepData[transaction_id] = {};
		  });
		}
		this.stepData[transaction_id][step] = data;
	  },
	  getStepData(transaction_id, step) {
		if (!this.stepData[transaction_id]) {
		  this.$patch((state) => {
			state.stepData[transaction_id] = {};
		  });
		}
		if(!this.stepData[transaction_id][step]){
			this.stepData[transaction_id][step] = {};
		}
		
		return this.stepData[transaction_id][step];
	  },
	},
  });
