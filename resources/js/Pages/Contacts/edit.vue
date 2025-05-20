<template>
    <AppLayout>
	<div>
		<div class="grid grid-cols-1 md:grid-cols-4 gap-3">
			<Toast />

			<div
				class="col-span-3 bg-white rounded shadow-md transition-all space-y-3 p-3 duration-300 hover:-translate-y-1 hover:shadow-lg animate-slide-up"
				style="animation-delay: 0.1s">

				<div class="grid grid-cols-1 md:grid-cols-2  gap-4">
					<div class="col-span-1 flex justify-start">
						<img class="h-24 rounded-full sm:mx-0 sm:shrink-0"
							src="https://iconnect10-stage.gryphtech.com/Custom/remax/images/contact-no-photo.svg" alt="Woman's Face">
						<div class="text-center my-auto sm:text-left">
							<div class="space-y-2">
								<p class="text-lg text-black font-semibold">
									{{ datos.name }} {{ datos.last_name }}
								</p>

								<button class="h-7 w-7 mx-0.5 bg-green-500 text-white hover:bg-green-600 rounded-full">
									<i class="fa-solid fa-mobile-screen-button"></i>
								</button>
								<button class="h-7 w-7 mx-0.5 bg-blue-500 text-white hover:bg-blue-600 rounded-full">
									<i class="fa-solid fa-phone"></i>
								</button>
								<button class="h-7 w-7 mx-0.5 bg-red-500 text-white hover:bg-red-600 rounded-full">
									<i class="fa-solid fa-envelope"></i>
								</button>
							</div>
						</div>
					</div>
					<div class="col-span-1">
						<div class="row pb-3">

							<Rating v-model="datos.rating" :pt="{
								offIcon: { class: 'h-12' }
							}" />


						</div>
						<div class="grid grid-cols-2 gap-2">
							<div class="col-span-2 md:col-span-1">
								<label for="username"> Fuente del Prospecto</label>
								<Select size="small" fluid v-model="datos.prospect_id" :invalid="contactStore.errors.prospect_id"
									optionValue="key_name" optionLabel="value" filter :options="propects" placeholder="Seleccionar">
								</Select>
								<Message v-if="contactStore.errors.prospect_id" severity="error" variant="simple" size="small">
									{{ contactStore.errors.prospect_id[0] }}
								</Message>
							</div>
							<div class="col-span-2 md:col-span-1">
								<label for="username">Etapa</label>
								<Select size="small" fluid v-model="datos.stage_id" :invalid="contactStore.errors.stage_id"
									optionValue="key_name" optionLabel="value" filter :options="stages" placeholder="Seleccionar">
								</Select>

							</div>
						</div>

					</div>
				</div>
				<div class="grid grid-cols-1 gap-4">
					<label for="username">Nota</label>
					<IconField>
						<InputIcon class="pi pi-file-edit" />
						<InputText v-model="datos.quick_note" size="small" class="w-full" />
					</IconField>
					<div class="flex  flex-wrap items-center">
						<div v-for="category, index in categories">
							<Chip class="m-1 py-0 rounded " :pt="{root:{class:'bg-[#ddcdf3] px-2'}}" v-if="contactStore.datos.categories.includes(category.id)" >
								<div
								class="text-primary-contrast text-[13px] rounded  flex items-center justify-center my-0  px-1">
								{{category.name}}</div>
							</Chip>
						</div>
						<Button label="Elija Categorías..." @click="visibleCat = true" class="h-9 font-bold text-base" size="small"
							severity="info" variant="text" />
					</div>
				</div>
			</div>

			<div
				class="col-span-4 md:col-span-1 lg:col-span-1 bg-transparent rounded space-y-1 transition-all duration-300 hover:-translate-y-1 animate-slide-up"
				style="animation-delay: 0.1s">
				<Button label="Agregar propiedad" icon="pi pi-plus" severity="success" size="small" class="w-full text-end"
					iconPos="right" @click="visibleTemp = true" />
				<Button label="Agregar Búsqueda Dinámica" icon="pi pi-plus" severity="info" size="small" class="w-full"
					iconPos="right" />
				<Button label="Agregar seguimiento" icon="pi pi-plus" severity="info" size="small" class="w-full"
					iconPos="right" @click="visibleTemp = true" />
				<Button label="Agregar visita de propiedad" icon="pi pi-plus" severity="info" size="small" class="w-full"
					iconPos="right" @click="visibleTemp = true" />
				<Button label="Tarea de contacto" icon="pi pi-plus" severity="info" size="small" class="w-full" iconPos="right"
					@click="visibleTemp = true" />
				<Button label="Send Marketing" icon="pi pi-plus" severity="info" size="small" class="w-full" iconPos="right" />
			</div>

			<div
				class="col-span-3 bg-white rounded shadow-md transition-all p-3 duration-300 hover:-translate-y-1 hover:shadow-lg animate-slide-up"
				style="animation-delay: 0.1s">
				<Tabs v-model:value="activeTab" scrollable>
					<TabList class="tablist">
						<Tab v-for="tab in tabs" :key="tab.name" :value="tab.value" class="inline-block flex-shrink-0">
							<div class="flex items-center gap-1">
								<i :class="`${tab.icon}`"></i>
								<span>{{ tab.label }}</span>
							</div>
						</Tab>
					</TabList>
					<TabPanels class="m-0">
						<TabPanel v-for="tab in tabs" :key="tab.name" :value="tab.value">
							<!-- Renderiza cada componente del tab -->
							<component :is="tab.component" />
						</TabPanel>
					</TabPanels>
				</Tabs>
				<div class="flex justify-end gap-2">
					<Button as="router-link" to="/contacts" label="Cancelar" size="small" severity="secondary"></Button>
					<Button type="button" label="Guardar" @click.prevent="updateForm" size="small"></Button>
				</div>
			</div>

			<div
				class="col-span-4 md:col-span-1 lg:col-span-1 bg-white rounded shadow-md space-y-1 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg animate-slide-up"
				style="animation-delay: 0.1s">
				<h2 class="text-center p-2 font-semibold">Eventos</h2>
			</div>
		</div>
	</div>

	<!-- dialog temp-->
	<Dialog v-model:visible="visibleTemp" modal header="Crear" :style="{ width: '30rem' }">
		<div class="flex  flex-col">
			<h3>
				Muy pronto
			</h3>
		</div>
		<div class="flex justify-end gap-2">
			<Button type="button" label="Cancelar" size="small" severity="secondary" @click="visibleTemp = false"></Button>
			<Button type="button" label="Guardar" size="small" @click="visibleTemp = false;"></Button>
		</div>
	</Dialog>

	<!-- dialog Categorias-->
	<Dialog v-model:visible="visibleCat" modal header="Categorias"  :style="{ width: '30rem' }"
	 :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
		<div class="flex  flex-col">
			<h3>
				Muy pronto
			
				<div class="grid grid-cols-12 gap-1  mt-5 col-span-12">
					<div :key="category.id" class="col-span-12 py-1 px-2 flex items-center" :class="bgColorCate(category.type)"
						v-for="category, index in categories">
						<CheckboxName :checked="contactStore.datos.categories.includes(category.id)"
							@update:checked="check(category.id, $event)" :fieldId="'category.id'" :label="category.name"
							:key="category.id">
						</CheckboxName>
					</div>
					<div>
						<div class="flex ">
							<div v-for="item,index in lisType" class="px-2 m-2" :class="bgColorCate(item.type)" :key="index">
								{{ item.name }}
							</div>
						</div>
					</div>
				</div>

			</h3>
		</div>
		<div class="flex justify-end gap-0 pt-3">
			<Button type="button" label="Cancelar" class="mr-3" size="small" severity="secondary" @click="visibleCat = false"></Button>
			<Button type="button" label="Aceptar" size="small" @click="visibleCat = false;"></Button>
		</div>
	</Dialog>
</AppLayout>
</template>
<script setup>
import { ref, shallowRef, onMounted } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue';

import PersonalTab from './tabs/PersonalTab.vue';
import Personal from './tabs/Personal.vue';
import { storeToRefs } from "pinia";
import { useContactStore } from "@/Stores/useContactStore";
import { useRoute, useRouter } from "vue-router";
import CheckboxName from '@/Components/CheckboxName.vue';
import { useToast } from 'primevue/usetoast';
import { useAppStore } from "@/Stores/useAppStore";
import { useIsLoadingStore } from '@/Stores/isLoadingCharts';
import apiClient from '@/src/constansts/axiosClient';
const visibleTemp = ref(false);
const visibleCat = ref(false);
const storeApp = useAppStore();
const { lang } = storeApp;
const router = useRouter();
const route = useRoute();
const contactStore = useContactStore();
const { datos } = storeToRefs(contactStore);
const activeTab = ref("0");
const toast = useToast();
const perfiles = ref([])
let propects = ref([])
let stages = ref([])
let categories = ref([])
// Configuración de tabs

const props = defineProps({
    contact_id: {
        type: Number,
        required: true
    }
});

const contact_id = props.contact_id;

const tabs = shallowRef([
	{ name: "detalle", label: "Detalles del contacto", value: "0", icon: "fa-solid fa-user", component: PersonalTab },
	{ name: "propiedades", label: "Propiedades", value: "1", icon: "fa-solid fa-home", component: Personal },
	{ name: "documentos", label: "Documentos", value: "2", icon: "fa-solid fa-book", component: Personal },
	{ name: "comprador", label: "Nueva Coincidencia de Comprador", value: "3", icon: "fa-solid fa-people-group", component: Personal },
	{ name: "tareas", label: "Tareas", value: "4", icon: "fa-solid fa-list-check", component: Personal },
	{ name: "notas", label: "Notas", value: "5", icon: "fa-regular fa-note-sticky", component: Personal },
	{ name: "marketing", label: "Marketing", value: "6", icon: "fa-solid fa-bullhorn", component: Personal },
]);
const lisType=ref([
	{
		'name':'System',
		'type':1
	},
	{
		'name':'Office',
		'type':2
	},
	{
		'name':'Agent',
		'type':3
	},
])

//check categorias
const check = (optionId, checked) => {
	if (checked) {
		contactStore.datos.categories.push(optionId);
	} else {
		contactStore.datos.categories.splice(contactStore.datos.categories.indexOf(optionId), 1);
	}
};

const loadingStore = useIsLoadingStore();

const toggleLoading = (isLoading) => {
	loadingStore.setLoading(isLoading);
};

async function getPerfiles() {

	perfiles.value = []
	const data = {
		'code': lang,
		'category': 'Contact.Title'
	};
	const response = await apiClient.post('/options/get-select', data);
	perfiles.value = response.data;
}
async function getSelects(key, valor) {
	valor.valor = []
	const data = {
		'code': lang,
		'category': key
	};
	const response = await apiClient.post('/options/get-select', data);

	valor.value = response.data;
}

const bgColorCate = (type) => {

	let color = ''
	switch (type) {
		case 1:
			color = 'bg-[#ddcdf3]'
			break;
		case 2:

			color = 'bg-[#C7E6F8]'
			break;
		case 3:
			color = 'bg-[#D8EFCD]'
			break;

		default:
			break;
	}
	return color
}
const updateForm = async () => {

	try {
		await contactStore.update(contact_id);
		//toast.add({ severity: "success", summary: "Success", detail: "Actualizado exitosamente", life: 3000 });

		//return response.data;
	} catch (error) {

		console.log("errors", error)
	}
}

async function getCategories() {

	categories.value = []
	const data = {
		'agent_id': 1,

	};
	const response = await apiClient.post('/options/get-categories', data);
	categories.value = response.data;
}
toggleLoading(true);
onMounted(() => {

	contactStore.show(contact_id)
	getPerfiles();
	getSelects('Contact.LeadSource', propects)
	getSelects('LeadStages', stages)
	getCategories()
	toggleLoading(false);
});

</script>

<style scoped>
.p-button.p-component.p-button-sm {
	display: flex;
	justify-content: space-between;
}

.p-tabpanels {
	padding: 4px;
	margin: 0px;
}
</style>
