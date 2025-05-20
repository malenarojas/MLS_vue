import { defineStore } from "pinia";
import type { ChangeUser } from "@/src/interfaces/UserChange";
import type { UserForm } from "@/src/interfaces/UserForm";
import { reactive, ref } from "vue";
import apiClient from "@/src/constansts/axiosClient";

export const useUserStoreLogin = defineStore('userStoreLogin', () => {
	const listUserChange = reactive<ChangeUser>({
		agents: [],
		offices: [],
	});

	const acquisitionForm = reactive<UserForm>({});

	const isLoginModalVisible = ref(false);

	const setAcquisition = (data: ChangeUser) => {
		listUserChange.agents = data.agents;
		listUserChange.offices = data.offices;
	}

	// Actualiza la información del formulario de adquisición
	const updateAcquisitionForm = (step: keyof typeof acquisitionForm, data: Partial<typeof acquisitionForm>) => {
		console.log(acquisitionForm, data)
		if (typeof acquisitionForm[step] === 'object' && acquisitionForm[step] !== null) {
			Object.assign(acquisitionForm[step], data);
		}
	};

	const loginAs = async (id: string): Promise<any> => {

		try {
			const response = await apiClient.post('/loginas', { user_id: id });
			const dato = response.data.data;
			return response.data;
		} catch (error) {
			console.error(`Error al actualizar el agente con ID ${id}:`, error);
			throw new Error('No se pudo actualizar el agente.');
		}
	};
	// Resetea los datos del formulario
	const resetAcquisitionForm = () => {
		acquisitionForm.officeId = '';
	};

	// Modal actions
	const showLoginModal = () => {
		isLoginModalVisible.value = true;
	};

	const hideLoginModal = () => {
		isLoginModalVisible.value = false;
	};

	return {
		listUserChange,
		acquisitionForm,
		isLoginModalVisible,
		// Almacenar datos para el
		setAcquisition,
		// Métodos para actualizar el estado del formulario
		updateAcquisitionForm,
		resetAcquisitionForm,
		// Modal actions
		showLoginModal,
		hideLoginModal,
	};
}, {
	persist: true,
});
