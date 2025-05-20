<template>
	<div class="rounded bg-indigo-50 p-4 shadow-md">
		<h1 class="opacity-85 text-lg text-semibold mb-4 font-semibold"><i class="pi pi-user text-lg"/> <i class="pi pi-search mr-1 text-[10px] -translate-x-1"/>Elije un comprador</h1>

		<div class="flex gap-4">
			<InputText size="small" v-model="inputBuscar" class="w-96" />
			<Button size="small" @click="buscarContactos()" label="Buscar" />
		</div>

		<div class="w-full p-4 max-h-[400px] overflow-y-auto">
			<DataTable size="small" class="text-sm" @change="console.log(contactosSeleccionados);" v-model:selection="contactosSeleccionados" :value="contactos" dataKey="id"
				tableStyle="min-width: 50rem">
				<Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
				<Column field="id" header="ID"></Column>
				<Column field="name" header="Nombre"></Column>
				<Column field="mobile" header="Telefono"></Column>
				<Column field="email" header="Email"></Column>
			</DataTable>
		</div>
	</div>
</template>
<script setup>
import apiClient from "@/src/constansts/axiosClient";

let contactos = ref([]);
let contactosSeleccionados = ref([]);
let inputBuscar = ref(null);

async function buscarContactos() {
	console.log(inputBuscar.value);
	if (inputBuscar.value) {
		const response = await apiClient.get('/contacts/search', {
			params: {
				name: inputBuscar.value
			}
		});

		if(response.status == 200) {
			contactos.value = response.data;
			if(contactosSeleccionados.value) {
				contactosSeleccionados.value.forEach(contacto => {
					contactos.value.unshift(contacto);
				});
			}
		}
	}else{
		contactos.value = [];
	}
}

</script>