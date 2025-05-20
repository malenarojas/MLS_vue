<template>
	<div class="rounded bg-indigo-50 p-4 shadow-md">
		<h1 class="opacity-85 text-lg text-semibold mb-4 font-semibold"><i class="pi pi-user text-lg" /> <i
				class="pi pi-search mr-1 text-[10px] -translate-x-1" />Elije un comprador</h1>

		<div class="flex gap-4">
			<InputText size="small" v-model="inputBuscar" class="w-96" />
			<Button size="small" @click="buscarContactos()" label="Buscar" />
			<Button label="Crear" severity="info" @click="visible = true" />
		</div>

		<div class="w-full p-4 max-h-[400px] overflow-y-auto">
			<DataTable size="small" class="text-sm"
				v-model:selection="contactosSeleccionados" :value="contactos" dataKey="id"
				tableStyle="min-width: 50rem">
				<Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
				<Column field="id" header="ID"></Column>
				<Column field="name" header="Nombre"></Column>
				<Column field="mobile" header="Telefono"></Column>
				<Column field="email" header="Email"></Column>
			</DataTable>
		</div>
	</div>

	<Dialog v-model:visible="visible" modal header="Crear Comprador" :style="{ width: '35rem' }">
		
		<div class="mb-2">
			<label for="nombres" class="text-sm font-semibold opacity-75">Nombres <span class="text-red-500">*</span>
			</label>
			<InputText size="small" type="text" v-model="nombres" class="w-full" />
		</div>

		<div class="mb-2">
			<label for="apellidos" class="text-sm font-semibold opacity-75">Apellidos <span class="text-red-500">*</span></label>
			<InputText size="small" type="text" v-model="apellidos" class="w-full" />
		</div>
		
		<div class="mb-2">
			<label for="correo" class="text-sm font-semibold opacity-75">Email </label>
			<InputText size="small" type="email" v-model="correo" class="w-full " />
		</div>

		<div class="mb-2">
			<label for="text" class="text-sm font-semibold opacity-75">Teléfono </label>
			<InputText size="small" type="number" v-model="telefono" placeholder="591789456123" class="w-full" />
		</div>



		<div class="flex justify-end gap-2">
			<Button type="button" label="Cancelar" severity="secondary" @click="visible = false"></Button>
			<Button type="button" label="Crear" 
			@click="
				visible = false;
				crearContacto();
			"></Button>
		</div>
	</Dialog>
</template>
<script setup>
import apiClient from "@/src/constansts/axiosClient";
import { useTransactionStepStore } from '@/stores/useTransactionStore';
import { usePage } from "@inertiajs/vue3";
import { ref, computed, watch, onMounted  } from "vue";
import { useToast } from "primevue";

const toast = useToast();
const page = usePage();
const transactionId = page.props.transaction_id;

const store = useTransactionStepStore();


let contactos = ref([]);
const contactosSeleccionados = computed({
	get() {
		return store.getStepData(transactionId, 6) || [];
	},
	set(value) {
		store.saveStepData(transactionId, 6, value);
	}
});

let inputBuscar = ref(null);
let visible = ref(false);
let nombres = ref();
let telefono = ref();
let correo = ref();
let apellidos = ref();

watch(contactosSeleccionados.value, async (newVal) => {
	console.log("ENTROOOO");
	store.saveStepData(transactionId, 6, newVal);
	await buscarContactos();
	eliminarDuplicados();
});
watch(contactosSeleccionados, async (newVal) => {
	console.log("ENTROOOO");
	store.saveStepData(transactionId, 6, newVal);
	await buscarContactos();
	eliminarDuplicados();
});

function eliminarDuplicados () {
	const contador = {};
	if(!isEmptyObject(contactosSeleccionados.value)){
		contactosSeleccionados.value.forEach(selected => {
			contador[selected.id] = 0;
		});
		contactos.value = contactos.value.filter(contacto => {
			if (contador[contacto.id] == 0) {
				contador[contacto.id]++;
				return true;
			}
			return false;
		});
	}
}

function isEmptyObject(obj) {
  return obj && Object.keys(obj).length === 0;
}

async function buscarContactos() {
	if (inputBuscar.value && inputBuscar.value.length > 2) {
		const response = await apiClient.get('/contacts/search', {
			params: {
				name: inputBuscar.value
			}
		});

		if(response.status == 200) {
			contactos.value = response.data;

			refreshContactList();
		}
	}else{
		contactos.value = [];
		if (contactosSeleccionados.value.length > 0) {
			contactosSeleccionados.value.forEach(contacto => {
				contactos.value.unshift(contacto);
			});
		}
	}
}

function refreshContactList () {
	if(contactosSeleccionados.value) {
				contactosSeleccionados.value.forEach(contacto => {
					if(contactos.value.findIndex(item => contacto.id == item.id) == -1) {
						contactos.value.unshift(contacto);
					}
				});
			}
}

async function crearContacto () {
	const data = {
		name : nombres.value,
		last_name : apellidos.value,
		email : correo.value,
		mobile : telefono.value,
		profile_type_id : 'buyer_tenant'
	}

	const response = await apiClient.post('/contacts/store', data);
	if(response.status == 201) {

		const newContact = {
			id: response.data.id,
			name: response.data.name,
			email: response.data.email,
			mobile: response.data.mobile,
			profile_type_id : 'buyer_tenant'
		}

		contactosSeleccionados.value = [...contactosSeleccionados.value, newContact];

		refreshContactList();
	}


}

async function fetchStepData (step) {

const response = await apiClient.get('/transactions/get-step-data', {
	params : {
		step : step,
		internal_id : transactionId
	}
});

return response.data;
} 

onMounted( async () => {
	store.saveStepData(transactionId, 6, await fetchStepData(6))
	//buscarContactos();
});
</script>
