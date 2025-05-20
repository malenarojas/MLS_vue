<template>
    <AppLayout>
	<div class="container pt-2">
		<div class="flex justify-between items-center my-4 px-4 py-2 rounded-lg">
			<h1 class="text-2xl font-semibold tracking-wider">
				Contactos
				<span class="text-base ml-4 font-light tracking-wide">
					Administra tus contactos
				</span>
			</h1>
			<Button size="small" @click="visibleModalCrear = true" label="Agregar un nuevo contacto" />
		</div>

		<div class="grid grid-cols-12 gap-3 items-center mx-2">

			<div
				class="col-span-12 md:col-span-4  bg-white rounded shadow-md transition-all space-y-3 p-3 duration-300 hover:-translate-y-1 hover:shadow-lg animate-slide-up"
				style="animation-delay: 0.1s">En desarrollo </div>
		</div>

	</div>
	<!--modal tipo captacion-->
	<Dialog v-model:visible="visibleModalCrear" modal header="Crear Nuevo Contacto" :style="{ width: '30rem' }">
		{{ contactStore }}
		<div class="grid grid-cols-12 gap-1">
			<div class="col-span-12">
				<label for="text-sm">Nombre</label>
				<InputText size="small" type="text" v-model="datos.name" name="name" class="w-full my-1" />
				<Message v-if="contactStore.errors.name" severity="error" class="w-full" variant="simple" size="small">
					{{ contactStore.errors.name[0] }}
				</Message>
			</div>


			<div class="col-span-12">
				<label for="text-sm">Apellidos</label>
				<InputText size="small" type="text" v-model="datos.last_name" name="last_name" class="w-full my-1" />
				<Message v-if="contactStore.errors.last_name" severity="error" variant="simple" size="small">
					{{ contactStore.errors.last_name[0] }}
				</Message>
			</div>
			<div class="col-span-12">
				<label for="text-sm"> Perfil de contacto</label>
				<div class="mb- flex gap-4 flex-row">
					<div class="flex items-center gap-2 text-[14px]" v-for="(item,index) in tipos" :key="item.key_name">
						<RadioButton v-model="datos.profile_type_id" :inputId="item.key_name" name="tipo" :value="item.key_name" />
						<label for="capt1">{{ item.value }}</label>
					</div>
				</div>
				<Message v-if="contactStore.errors.profile_type_id" severity="error" variant="simple" size="small">
					{{ contactStore.errors.profile_type_id[0] }}
				</Message>
			</div>

			<div class="col-span-12">
				<label for="text-sm">Correo</label>
				<InputText size="small" type="email" v-model="datos.email" name="email" class="w-full my-1" />
			</div>
			<div class="col-span-12">
				<label for="text-sm">Celular</label>
				<InputText size="small" type="text" v-model="datos.mobile" name="mobile" class="w-full my-1" />
			</div>


			<div class="col-span-12">
				<div class="flex justify-end gap-2">
					<Button type="button" label="Cancelar" size="small" severity="secondary"
						@click="visibleModalCrear = false"></Button>
					<Button type="button" label="Aceptar" :loading="isLoad" size="small" :disabled="!datos.name"
						@click="storeForm"></Button>
				</div>
			</div>

		</div>
	</Dialog>
	<!--modal tipo captacion-->
</AppLayout>
</template>
<script setup>
import { ref } from "vue";

import { storeToRefs } from "pinia";
import { useContactStore } from "@/Stores/useContactStore";
import { router } from "@inertiajs/vue3";
import { useAppStore } from "@/Stores/useAppStore";
import apiClient from "@/src/constansts/axiosClient";
import { onMounted } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";

const storeApp = useAppStore();

const { lang } = storeApp;
const contactStore = useContactStore();
const { datos, errors, isLoad } = storeToRefs(contactStore);
const visibleModalCrear = ref(false);
let tipos = ref([])
const storeForm = async () => {

	try {
		await contactStore.store();
		if (contactStore.datos.id) {

			router.visit(`/contacts/${contactStore.datos.id}/edit`);
		}
		//return response.data;
	} catch (error) {

		console.log("errors", error)
	}
}

async function getSelects(key, valor) {
	valor.valor = []
	const data = {
		'code': lang,
		'category': key
	};

	const response = await apiClient.post('/options/get-select', data);
	console.log('e ',response)
	valor.value = response.data;
}
onMounted(() => {
	getSelects('Contact.profileType',tipos)
})
</script>
