<template>

	<div class="mt-4 p-4 bg-indigo-50 w-full flex gap-4">
		<MultiSelect v-model="mesSeleccionado" optionLabel="name" filter :options="meses" placeholder="Mes"
			:maxSelectedLabels="3" @change="obtenerTodo()" :pt="{
					root: {class: 'custom-multiselect-root'},
					overlay: {class: 'custom-multiselect-overlay'},
					label: {class: 'custom-multiselect-label'},
					dropdownIcon: {class: 'custom-dropdown-icon'},
					filterIcon: {class: 'custom-filter-icon hidden'},
					header: {class: 'custom-multiselect-header'},
					option: {class: 'custom-multiselect-option'}
			}" />

		<MultiSelect v-model="anioSeleccionado" optionLabel="name" filter :options="anios" placeholder="Años"
			:maxSelectedLabels="3" @change="obtenerTodo()" :pt="{
					root: {class: 'custom-multiselect-root'},
					overlay: {class: 'custom-multiselect-overlay'},
					label: {class: 'custom-multiselect-label'},
					dropdownIcon: {class: 'custom-dropdown-icon'},
					filterIcon: {class: 'custom-filter-icon hidden'},
					header: {class: 'custom-multiselect-header'},
					option: {class: 'custom-multiselect-option'}
		}" />
	</div>

	<div class="grid grid-cols-2 gap-4">
		<client-only>
			<VueApexCharts type="bar" height="400" :options="optionsTipos" :series="seriesTipos" ref="chartTipos"
				class="w-full" />
		</client-only>
		<client-only>
			<VueApexCharts type="bar" height="400" :options="optionsSubipos" :series="seriesSubtipos" ref="chartSubtipos"
				class="w-full" />
		</client-only>

	</div>




</template>

<script setup>
import { useRoute } from 'vue-router';
import { onMounted, ref, watchEffect } from "vue";
import axios from 'axios';
import apiClient from '@/src/constansts/axiosClient';
import VueApexCharts from "vue3-apexcharts";



const route = useRoute();

const meses = ref([
	{ name : "Enero", code: "1" },
	{ name : "Febrero", code:"2" },
	{ name : "Marzo", code:"3" },
	{ name : "Abril", code:"4" },
	{ name : "Mayo", code:"5" },
	{ name : "Junio", code:"6" },
	{ name : "Julio", code:"7" },
	{ name : "Agosto", code:"8" },
	{ name : "Septiembre", code:"9" },
	{ name : "Octubre", code: "10" },
	{ name : "Noviembre", code: "11" },
	{ name : "Diciembre", code: "12" },
]);

const anios = ref([
	{name : "2024", code : "2024"},
	{name : "2023", code : "2023"},
	{name : "2022", code : "2022"},
	{name : "2021", code : "2021"},
	{name : "2020", code : "2020"},
])

async function obtenerDatosCaptaciones () {

	const data = {
		'meses' : mesSeleccionado.value.map(mes => {
			return mes.code
		}),
		'anios' : anioSeleccionado.value.map(anio => {
			return anio.code
		}),
		'type' : route.query.type
	}

	const response = await apiClient.post('/dashboard/captaciones', data);
	renderTipos(response.data);
}

async function obtenerDatosSubtipos (subtipo_id) {
	const data = {
		'meses' : mesSeleccionado.value.map(mes => {
			return mes.code
		}),
		'anios' : anioSeleccionado.value.map(anio => {
			return anio.code
		}),
		'type' : route.query.type,
		'type_id' : subtipo_id
	}

	console.log(data);

	const response = await apiClient.post('/dashboard/captaciones', data);
	console.log(response);

	renderSubtipos(response.data);
}

let chartSubipos= ref(null);
const seriesSubtipos = ref([]);
const optionsSubipos= ref([]);

function renderSubtipos (data) {
	const serie = [];
	const categorias = [];
	
	data.forEach(item => {
		categorias.push(item.name);
		serie.push(item.total);
	});

	optionsSubipos.value = {
		chart: {
			type: 'bar',
			height: 350,
			stacked: false,
			toolbar: {
				show: false
			},
			events: {
				// dataPointSelection: (event, chartContext, config) => {
				// 	clickColumnaTipo(config, chartContext, event);
				// },
			}
		},
		plotOptions: {
			bar: {
				horizontal: true,
				columnWidth: '50%'
			}
		},
		xaxis: {
			categories: categorias,
			labels: {
				formatter: function (val) {
					return val;
				}
			}
		},
		yaxis: {
			title: {
				text: 'Cantidad'
			}
		},
		title: {
			text: 'Captaciones por subtipo',
			align: 'center'
		}
	}

	seriesSubtipos.value = [
		{
			name: 'Cantidad',
			data: serie,
		}
	];

	chartSubipos.value.updateOptions(optionsSubipos.value);

} 

let chartTipos= ref(null);
const tipos_id = ref([]);

function renderTipos(data) {
	const serie = [];
	const categorias = [];
	const ids = [];

	data.forEach(item => {
		serie.push(item.total);
		ids.push(item.id);
		categorias.push(item.name?? 'Sin especificar');
	});
	tipos_id.value = ids;

	optionsTipos.value = {
		chart: {
			type: 'bar',
			height: 350,
			stacked: false,
			toolbar: {
				show: false
			},
			events: {
				dataPointSelection: (event, chartContext, config) => {
					clickColumnaTipo(config, chartContext, event);
				},
			}
		},
		plotOptions: {
			bar: {
				horizontal: true,
				columnWidth: '50%'
			}
		},
		xaxis: {
			categories: categorias,
			labels: {
				formatter: function (val) {
					return val;
				}
			}
		},
		yaxis: {
			title: {
				text: 'Cantidad'
			}
		},
		title: {
			text: 'Captaciones por tipo',
			align: 'center'
		}
	}

	seriesTipos.value = [
		{
			name: 'Cantidad',
			data: serie,
		}
	];

	chartTipos.value.updateOptions(optionsTipos.value);

}

function clickColumnaTipo (config, chartContext, event) {
	console.log(config.dataPointIndex);
	const tipo_id = tipos_id.value[config.dataPointIndex];
	obtenerDatosSubtipos(tipo_id);
}

let mesSeleccionado = ref([]);
let anioSeleccionado = ref([]);

let optionsTipos = ref([]);
let seriesTipos = ref([]);

async function obtenerTodo() {
	await obtenerDatosCaptaciones();
	if(tipos_id.value.length > 0 ){
		await obtenerDatosSubtipos();
	}
}

onMounted(async () => {

	mesSeleccionado.value = meses.value.filter(mes =>
		route.query.meses.includes(mes.code)
	);
	anioSeleccionado.value = anios.value.filter(anio =>
		route.query.anio.includes(anio.code)
	);
	await obtenerDatosCaptaciones();
});


definePageMeta({
  middleware: 'auth',
});

</script>


<style scoped>
.p-multiselect {
	all: unset;
	/* Desactiva todos los estilos por defecto de PrimeVue */
}

.custom-multiselect-root {
	background: linear-gradient(to top, #4c51bf, #7c3aed) !important;
	color: white !important;
	font-weight: bold;
	border-radius: 12px;
	box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
	display: flex;
	gap: 8px;
	align-items: center;
	text-align: center;
}

.custom-multiselect-overlay {
	background-color: #f1f5f9 !important;
	border: 1px solid #cbd5e1 !important;
	padding: 12px !important;
	border-radius: 8px !important;
	max-height: 300px;
	overflow-y: auto;
}

.custom-multiselect-root .p-multiselect-label {
	color: white !important;
}

.custom-multiselect-root .p-multiselect-label.p-placeholder {
	color: white !important;
}

.custom-multiselect-label {
	color: white !important;
	font-size: 7px !important;
}

.custom-dropdown-icon {
	color: white !important;
}

.custom-filter-icon {
	display: none !important;
}

.custom-multiselect-header {
	display: flex;
	gap: 12px;
	padding: 10px;
}

.custom-multiselect-option {
	display: flex;
	gap: 8px;
	padding: 8px !important;
	border-bottom: 1px solid #e2e8f0 !important;
}
</style>