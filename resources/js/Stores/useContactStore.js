import { defineStore } from "pinia";
import apiClient from "@/src/constansts/axiosClient";
import { useRoute, useRouter } from "vue-router";
const router = useRouter();
const route = useRoute();

export const useContactStore = defineStore("contactStore", {
	state: () => ({
		datos: {
			profile_type_id:'buyer_tenant',
			type_buyer_commission:'Amount Fixed',
			sell:0,
			categories:[]
		},
		errors: {},
		isLoad: false,

	}),

	actions: {
		async store() {
			this.isLoad = true;
			this.clearErrors();
			try {
				const response = await apiClient.post("/contacts/store", this.datos);
				console.log(response.data);
				this.isLoad = false;
				this.datos = response.data;
			
				//return response.data;
			} catch (error) {
				console.log("err ", error);
				this.isLoad = false;
				this.errors = error.errors; // Guardar errores en el estado
			}
		},
		async update(id) {
			this.isLoad = true;
			this.clearErrors();
			try {
				const response = await apiClient.put(`/contacts/${id}`, this.datos);
				console.log(response.data);
				this.isLoad = false;
				this.datos = response.data;
				//location.reload()
				
			} catch (error) {
				console.log("err ", error);
				this.isLoad = false;
				this.errors = error.errors; // Guardar errores en el estado
			}
		},
		clearErrors() {
			this.errors = {}; // Método para limpiar errores
		},

		async show(id) {
			try {
				const response = await apiClient.get(`/contacts/${id}`);
				console.log(response);
				this.datos = response.data;
				this.datos.categories = response.data.categories;
			} catch (error) {
				console.log("e ", error);
				throw error.errors		}
		},
	},
	getters: {},
	//persist:true
});
