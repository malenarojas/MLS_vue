<template>
	<AppLayout title="Dashboard">

	<div class="mx-auto flex bg-white">

		<main class="flex-1 md:p-4 animate__fadeIn w-full">

			<div v-if="oficinaSeleccionada" class="bg-indigo-50 border p-4 mb-4 flex gap-4">
				<img :src="'http://localhost:8000/' + dataOficinaSeleccionada.image" class="h-16 md:h-24" alt="">
				<div class="my-auto">
					<h1 class="text-2xl md:text-3xl font-bold opacity-75">{{ dataOficinaSeleccionada.name }}</h1>
					<h2 class="font-semibold opacity-70">{{ `${dataOficinaSeleccionada.city}
						${dataOficinaSeleccionada.province}, ${dataOficinaSeleccionada.country}` }}</h2>
				</div>
			</div>

			<div class="grid grid-cols-2 md:flex md:justify-end bg-indigo-50 border p-4 gap-4 w-ful text-sm">


				<Select size="small" placeholder="Departamento" :options="departamentos" v-model="departamentoSeleccionado"
				v-if="!(Array.isArray(role) && role.length == 0) && ['Super Administrador', 'Administrador', 'Soporte'].includes(role)"
					@change="changeDepartamento()"
					optionLabel="name" option-value="code">
				</Select>

				<select name="oficina" id="oficina"
					v-if="!(Array.isArray(role) && role.length == 0) && role != 'Agente' && role != 'Broker'"
					class="border border-slate-300/95 px-2 bg-white/65  
							 rounded-md shadow-sm py-1.5 *:text-black text-sm" v-model="oficinaSeleccionada" @change="changeOficina()">
					<option selected value="">Seleccionar Oficina</option>
					<option v-for="oficina in oficinas" :value="oficina.id">{{ oficina.name }}</option>
				</select>

				<select name="agente" id="agente" v-model="agenteSeleccionado" class="rounded p-1 text-black md:w-44"
					@change="obtenerTodosLosDatos()"
					v-if="oficinaSeleccionada && !(Array.isArray(role) && role.length == 0) && role != 'Agente'">
					<option selected value="">Seleccionar Agente</option>
					<option v-for="agente in agentes" :value="agente.id">{{ agente.name }}</option>
				</select>
			</div>

			<div class="flex flex-wrap justify-between my-4 gap-2">

				<div class="gird grid-cols-2 rounded-xl text-xs md:text-sm 
					overflow-hidden font-semibold border mb-auto shadow-md">
					<button @click="switchReportType('mensual')"
						:class="['p-2 btn-tipo-reporte', tipoReporte === 'mensual' ? 'activo' : '']">
						<span class="opacity-85">Mensual</span>
					</button>
					<button @click="switchReportType('acumulado')"
						:class="['p-2 btn-tipo-reporte', tipoReporte === 'acumulado' ? 'activo' : '']">
						<span class="opacity-85">Acumulado</span>
					</button>
				</div>
				

				<div class="grid grid-cols-2 items-center gap-2 md:grid-cols-4">

					<Select v-model="inDollars" :options="currencys" size="small" optionLabel="name" optionValue="id" checkmark :highlightOnSelect="false" class="w-full md:w-auto ml-auto" />

					<div>
						<MultiSelect v-model="mesesSeleccionados" optionLabel="name" filter :options="meses" placeholder="Meses"
							:maxSelectedLabels="2" size="small" />
					</div>

					<select name="" id="" class=" border border-slate-300/95 px-2 bg-blue-50/35
							 rounded-md shadow-sm py-1.5 *:text-black text-sm" v-model="anioSeleccionado" :value="2025">
						<option value="2025" selected>2025</option>
						<option value="2024">2024</option>
						<option value="2023">2023</option>
						<option value="2022">2022</option>
						<option value="2021">2021</option>
						<option value="2020">2020</option>
					</select>

					<button class="bg-gradient-to-t from-indigo-900 to-indigo-800 text-white
							font-semibold rounded-xl shadow-md py-2 px-2 col-span-2 md:col-span-1" @click="obtenerTodosLosDatos">
						Generar
					</button>

				</div>
			</div>

			<ResumenEjecutivo 
				:resumenData="dataResumenEjecutivo" 
				:tipoReporte="'mensual'"
				:months="mesesSeleccionados"
				:year="anioSeleccionado"
				:userRole="role"/>

			<div class="grid grid-cols-1 md:grid-cols-3 mb-4 gap-y-4 md:gap-4">
				
				<Commissions 
					:dataComisiones="dataComisiones" 
					:anioSeleccionado="anioSeleccionado"
					:tipoReporte="'mensual'"
					/>

				<Ranking 
					:itemsRanking="itemsRanking"
					:oficinaSeleccionada="oficinaSeleccionada"
					:agenteSeleccionado="agenteSeleccionado" 
					:seleccionarOficina="seleccionarOficina"
					:seleccionarAgente="seleccionarAgente"
					/>

				<Transactions
					:datosTransacciones = "datosTransaccionesGlobales"
					:tipoReporte="'mensual'"
					:anioSeleccionado="anioSeleccionado"
					:mesesSeleccionados="mesesSeleccionados"
					:oficinaSeleccionada="oficinaSeleccionada"
					:agenteSeleccionado="agenteSeleccionado" 
					@toggleLoading="toggleLoading($event)"
				/>


				<div
					class="rounded-xl shadow-md bg-indigo-50 p-4 border-indigo-200 border mb-auto col-span-2 md:col-span-1 h-full">
					<div class="flex justify-start items-center border-b pb-2">

						<h2 class="font-semibold opacity-75 uppercase">Nuevas Captaciones</h2>

					</div>
					<div class="mt-2">
						<h4 class="text-lg font-semibold uppercase opacity-85">Total: {{ totalCaptaciones }}</h4>
						<p class="text-xs opacity-75">Ventas: {{ totalCaptacionesVentas }}</p>
						<p class="text-xs opacity-75">Alquileres: {{ totalCaptacionesAlquileres }}</p>
					</div>
					<div class="card flex justify-center">
						<client-only>
							<VueApexCharts ref="chartCaptaciones" type="pie" height="200" class="w-auto" :options="optionsCaptaciones"
								:series="seriesCaptaciones">
							</VueApexCharts>
						</client-only>
					</div>
				</div>
				
				<ActiveAgents v-if="!agenteSeleccionado" :data="dataAgentesActivos"/>

				<div class="rounded-xl shadow-md bg-indigo-50 border-indigo-200 border
					mb-auto col-span-2">
					<div class="flex justify-start items-center border-b pb-2 p-4">

						<h2 class="font-semibold opacity-75 uppercase">Captaciones activas</h2>

					</div>

					<div>
						<div class="px-4 mt-2 text-end">
							<h3 class="font-semibold text-lg opacity-85">Bs. {{ totalInventario }}</h3>
							<span class="text-sm opacity-75 -translate-y-1 inline-block">Inventario actual para la
								venta</span>
						</div>
						<client-only>
							<VueApexCharts ref="chartCaptacionesActivas" type="pie" height="300" class="w-auto"
								:options="optionsCaptacionesActivas" :series="seriesCaptacionesActivas">
							</VueApexCharts>
						</client-only>
					</div>

				</div>

				<div class="rounded-xl shadow-md bg-indigo-50 border-indigo-200 border
					mb-auto p-4">
					<div class="flex justify-between items-center border-b pb-2">

						<h2 class="font-semibold opacity-75 uppercase">Promedios</h2>

						<button @click="clickExpandirPromedios">
							<i class="pi pi-window-maximize opacity-75"></i>
						</button>

					</div>

					<div>
						<span class="text-sm opacity-75">Precio promedio de captaciones</span>
						<h3 class="mb-2 text-3xl font-bold opacity-85">{{ precioPromedio }}</h3>

						<span class="text-sm opacity-75">Tiempo promedio en el mercado</span>
						<h3 class="mb-2 text-xl font-semibold opacity-85">{{ tiempoPromedio }}</h3>
					</div>
				</div>
			</div>
		</main>
	</div>
</AppLayout>
</template>

<script setup>
import { onMounted, ref, computed } from "vue";
import VueApexCharts from "vue3-apexcharts";
import { usePage, router } from "@inertiajs/vue3";
import { useIsLoadingStore } from "@/Stores/isLoadingCharts";
import { useToast } from "primevue";

import apiClient from "@/src/constansts/axiosClient";
import  MultiSelect  from 'primevue/multiselect'; 
import AppLayout from "@/Layouts/AppLayout.vue";

import ResumenEjecutivo from "@/Components/Dashboard/Cards/ResumenEjecutivo.vue";
import ActiveAgents from "@/Components/Dashboard/ActiveAgents.vue";
import Commissions from "@/Components/Dashboard/Commissions.vue";
import Ranking from "@/Components/Dashboard/Ranking.vue";
import Transactions from "@/Components/Dashboard/Transactions.vue";

const props = defineProps({
	msg: String,
	// user: Object,
});

const page = usePage();
const toast = useToast();
const user = page.props.user;
const role = page.props.role;

const meses = ref([
	{ 'name': 'Enero', code: '1' },
	{ 'name': 'Febrero', code: '2' },
	{ 'name': 'Marzo', code: '3' },
	{ 'name': 'Abril', code: '4' },
	{ 'name': 'Mayo', code: '5' },
	{ 'name': 'Junio', code: '6' },
	{ 'name': 'Julio', code: '7' },
	{ 'name': 'Agosto', code: '8' },
	{ 'name': 'Septiembre', code: '9' },
	{ 'name': 'Octubre', code: '10' },
	{ 'name': 'Noviembre', code: '11' },
	{ 'name': 'Diciembre', code: '12' },
])

const departamentos = ref([
	{ 'name': 'Santa Cruz', code: '3' },
	{ 'name': 'La Paz', code: '2' },
	{ 'name': 'Cochabamba', code: '1' },
	{ 'name': 'Resto del pais', code: 'other' },
	
]);

const currencys = ref([
	{ 'name': 'Bs.', id: 0 },
	{ 'name': 'USD.', id: 1 },
])

const inDollars = ref(0);

const chartCaptaciones = ref(null);


const anioSeleccionado = ref("2025");

const oficinas = ref([]);
let oficinaSeleccionada = ref('');
let dataOficinaSeleccionada = ref([]);

let departamentoSeleccionado = ref(0);

let datosTransaccionesGlobales = [];

let precioPromedio = ref(0);
let tiempoPromedio = ref(0);

let dataAgentesActivos = ref([]);
let dataComisiones = ref([]);

//Para resumen ejecutivo

let totalInventario = ref(0);

let cantidadCaptacionesActivas = ref(0);

const mesesSeleccionados = ref();

let dataResumenEjecutivo = ref([]);

const optionsCaptaciones = ref({
	chart: {
		type: "pie",
	},
	labels: ['Venta', 'Alquiler', 'Anticretico'],
	legend: {
		position: "bottom",
	},
	colors: ["#1E3A8A", "#3B82F6", "#93C5FD"],
});

const seriesCaptaciones = ref([100]);

const obtenerDatosCharts = async () => {

	if(oficinaSeleccionada.value && role != 'Agente') {
		obtenerDataOficina();
		getAgentes();
	}
	
	const data = {
		mesesSeleccionados: mesesSeleccionados.value ? mesesSeleccionados.value.map(mes => mes.code) :
			[1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
		anioSeleccionado: anioSeleccionado.value ?? '2024',
		tipoReporte: 'mensual',
		oficina_id : oficinaSeleccionada.value,
		agente_id : agenteSeleccionado.value,
		departamento_id : departamentoSeleccionado.value,
		inDollars : inDollars.value,
	};

	toggleLoading(true);
	const response = await apiClient.post('/dashboard/get-datos-charts', data);

	dataComisiones.value = response.data[0]['comisiones'];

	renderCaptaciones(response.data[0]['captaciones']);
	itemsRanking.value = response.data[0]['ranking'];

	datosTransaccionesGlobales = response.data[0]['transacciones'];
	toggleLoading(false);

}

const obtenerDatosSecundarios = async () => {
	const data = {
		mesesSeleccionados: mesesSeleccionados.value ? mesesSeleccionados.value.map(mes => mes.code) :
			[1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
		anioSeleccionado: [anioSeleccionado.value] ?? ['2024'],
		tipoReporte: 'mensual',
		oficina_id : [oficinaSeleccionada.value],
		agente_id : [agenteSeleccionado.value],
		departamento_id : departamentoSeleccionado.value,
	};

	const response = await apiClient.post('/dashboard/get-datos-secundarios', data, {
		timeout : 30000
	});
	if(response.status == 200) {
		dataAgentesActivos.value = {
			'comisionesAgentes' : response.data['comisionesAgentes'],
			'totalAgentes' : response.data['agentesActivos']['totalAgentes'],
			'totalAgentesIngresados' : response.data['agentesActivos']['agentesIngresados'],
			'totalAgentesRetirados' : response.data['agentesActivos']['agentesRetirados'],
		}
	
		renderCaptacionesActivas(response.data['captacionesActivas']);
		tiempoPromedio.value = convertirDias(response.data['tiempoPromedio']);
		precioPromedio.value = '$ ' + Intl.NumberFormat(('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })).format(parseFloat(response.data['precioPromedio']));
	}

	const responseInventario = await apiClient.post('/dashboard/get-inventario', data, {
		timeout : 30000
	});
	if(responseInventario.status == 200) {
		totalInventario.value = Intl.NumberFormat(('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })).format(parseFloat(responseInventario.data.amount));
		cantidadCaptacionesActivas.value = responseInventario.data.quantity;
	}
}

let optionsCaptacionesActivas = ref({
	chart: {
		type: "line",
	},
	dataLabels: {
		enabled: false,
	},
	xaxis: {
		categories: [],
	},
	stroke: {
    curve: "smooth", 
    width: 3,  
    colors: ["#22bb77"],  
  },
	
	colors: ["#22bb77"],
	legend: {
		position: "top",
	},
});
let seriesCaptacionesActivas = ref([]);
let chartCaptacionesActivas = ref(null);

function renderCaptacionesActivas(captacionesActivas) {
	const series = [];
	const categorias = [];

	if(captacionesActivas.length == 1) {
		optionsCaptacionesActivas.value.chart.type = 'bar';
	}else{
		optionsCaptacionesActivas.value.chart.type = 'line';
	}

	captacionesActivas.forEach(item => {
		const mesEncontrado = (meses.value.find(mes => mes.code == item.mes )).name;
		const mesAbreviado = mesEncontrado.substring(0,3);
		const anioAbreviado = item.anio.substring(2,4);
		categorias.push(`${mesAbreviado} ${anioAbreviado}` )
		series.push(item.captacionesActivas)
	});

	optionsCaptacionesActivas.value.xaxis.categories = categorias;
	
	seriesCaptacionesActivas.value = [
		{
			name: 'Captaciones activas',
			data: series
		}
	];

	chartCaptacionesActivas.value.updateOptions(optionsCaptacionesActivas.value);
}

const obtenerResumenEjecutivo = async () => {

	const data = {
		'mesesSeleccionados' : mesesSeleccionados.value ? mesesSeleccionados.value.map(mes => mes.code) :
			[1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
			'anioSeleccionado' : anioSeleccionado.value ?? '2025',
			'office_id' : oficinaSeleccionada.value,
			'agent_id' : agenteSeleccionado.value,
			'mensual' : true,
			'departamento_id' : departamentoSeleccionado.value ?? '',
			'inDollars' : inDollars.value,
			
	}

	const response = await apiClient.post('/dashboard/get-resumen-ejecutivo', data)
	.catch(error => {
		toast.add({ severity: 'error', summary: 'Error al obtener el resumen ejecutivo', life: 3000 });
	});
	if(response.status == 200) {

		dataResumenEjecutivo.value = [
			{
				'valueA' : response.data['transactions'],
				'valueB' : response.data['transactionsLastYear'],
				'goal' : response.data['transactionsGoal'],
			},
			{
				'valueA' : response.data['totalTransactionsAmount'],
				'valueB' : response.data['totalTransactionsAmountLastYear'],
				'goal' : response.data['transactionVolumeGoal'],
			},
			{
				'valueA' : response.data['totalPaymentAmount'],
				'valueB' : response.data['totalPaymentAmountLastYear'],
				'goal' : response.data['paymentAmountGoal'],
			},
			{
				'valueA' : response.data['activeAgents'],
				'valueB' : response.data['activeAgentsLastYear'],
				'goal' : response.data['agentsGoal'],
			},
			{
				'valueA' : response.data['activeListings'],
				'valueB' : response.data['activeListingsLastYear'],
				'goal' : response.data['listingsGoal'],
			}
			/*{
				'valueA' : response.data['timeInMarket'],
				'valueB' : response.data['timeInMarketLastYear'],
				'goal' : response.data['timeInMarketGoal'],
			},*/
		];
	}
}

async function obtenerTodosLosDatos () {
	if(role == 'Broker' || role == 'Agente') {
		// oficinaSeleccionada.value = oficinas.value.filter(oficina => oficina.id == storeAuth.office.id);
		// if(oficinaSeleccionada.value.length > 0) {
		// 	oficinaSeleccionada.value = oficinaSeleccionada.value[0].id;
		// }else{
		// 	oficinaSeleccionada.value = '';
		// }
		console.log(agenteSeleccionado.value);
		await obtenerDataOficina();
		console.log(agenteSeleccionado.value);
		await getAgentes();
		console.log(agenteSeleccionado.value);

	}
	
	await obtenerDatosCharts();
	console.log(agenteSeleccionado.value);

	await obtenerResumenEjecutivo();
	console.log(agenteSeleccionado.value);

	await obtenerDatosSecundarios();
	console.log(agenteSeleccionado.value);	
}

const seleccionarOficina = (office_id) => {
	oficinaSeleccionada.value = office_id;
	changeOficina();
}

const seleccionarAgente  = (agent_id) => {
	agenteSeleccionado.value = agent_id;
	obtenerTodosLosDatos();
}

async function changeAgente () {
	obtenerTodosLosDatos();
	obtenerDataAgente();
} 

function obtenerDataAgente () {
	const data = {
		agente_id : agenteSeleccionado.value,
	}

	//const response = await apiClient.post('')
}

async function changeOficina() {
	
	agenteSeleccionado.value = '';
	
	obtenerTodosLosDatos();
	obtenerDataOficina();
	if(oficinaSeleccionada.value) {
		getAgentes();
	}
}

async function changeDepartamento() {
	const response = await apiClient.post('/dashboard/get-oficinas-por-ubicacion', {
		state_id : departamentoSeleccionado.value
	});
	if(response.status == 200) {
		oficinas.value = response.data;
		obtenerTodosLosDatos();
	}
}

async function obtenerDataOficina() {
	const url = `/offices/${oficinaSeleccionada.value}`;
	const response = await apiClient.get(url);
	if(response.status == 200) {
		dataOficinaSeleccionada.value = response.data;
	}
	
	const data = {
		mesesSeleccionados: mesesSeleccionados.value ? mesesSeleccionados.value.map(mes => mes.code) :
			[1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
		anioSeleccionado: anioSeleccionado.value ?? '2024',
		tipoReporte: 'mensual',
		oficina_id: oficinaSeleccionada.value,
	}
	// const responseRanking = await apiClient.post(`/offices/get-ranking`);
	// if(response.status == 200) {
	// 	dataOficinaSeleccionada.value.ranking = response.data;
	// }


}


async function getOficinas () {
	const response = await apiClient.post('/dashboard/get-oficinas-por-ubicacion');
	if(response.status == 200){
		oficinas.value = response.data;
	}
}

const agentes = ref([]);
let agenteSeleccionado = ref('');

async function getAgentes () {
	const response = await apiClient.post('/dashboard/get-agentes', {
		oficina_id : oficinaSeleccionada.value
	});
	if(response.status == 200){
		agentes.value = response.data;
	}
}

let itemsRanking = ref();	

function clickExpandirPromedios() {
	const oficina = oficinaSeleccionada.value ?? '';
	const agente = agenteSeleccionado.value ?? '';

	const query = {
		meses: mesesSeleccionados.value ?? [],
		anio: anioSeleccionado.value,
	};

	if (oficina) {
		query.oficina = oficina;
	}
	if (agente) {
		query.agente = agente;
	}

	router.visit('/dashboard/promedios', {
		method: 'get',
		data: query,
		preserveState: true,
	});
}


let totalCaptaciones = ref(0);
let totalCaptacionesVentas = ref(0);
let totalCaptacionesAlquileres = ref(0);

function renderCaptaciones(datosCaptaciones) {
	optionsCaptaciones.value.chart.events = {
		dataPointSelection: (event, chartContext, config) => {
			clickParteCaptaciones(config, chartContext, event);
		},
	}
	seriesCaptaciones.value = [
		datosCaptaciones['captacionesVentas'],
		datosCaptaciones['captacionesAlquileres'],
		datosCaptaciones['captacionesAnticreticos'],
	];

	chartCaptaciones.value.updateOptions(optionsCaptaciones.value);

	totalCaptacionesVentas = datosCaptaciones['captacionesVentas'];
	totalCaptacionesAlquileres = datosCaptaciones['captacionesAlquileres'];
	totalCaptaciones = parseInt(datosCaptaciones['captacionesVentas']) + parseInt(datosCaptaciones['captacionesAlquileres']) + parseInt(datosCaptaciones['captacionesAnticreticos']);
}

function clickParteCaptaciones (config, chartContext, event) {


	const listaTipos = ['venta', 'alquiler', 'anticretico'];
	const type = listaTipos[config.dataPointIndex];

	const meses = mesesSeleccionados.value ?  mesesSeleccionados.value.map(mes => {
		return mes.code
	}) : [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
	const anio = anioSeleccionado.value;

	const url = router.resolve({
			path: '/dashboard/captaciones',
			query: { meses: meses, anio: anio, type: type }
		}).href;
	
	window.open(url, '_blank');
} 

const tipoReporte = ref("mensual");

function switchReportType(tipo) {
	tipoReporte.value = tipo;
	setMesesSeleccionados();
	obtenerTodosLosDatos();
}

function convertirDias(dias) {
    const anios = Math.floor(dias / 365); 
    dias = dias % 365; 
    const meses = Math.floor(dias / 30);
    dias = parseInt(dias % 30);

	if(anios) {
		return `${anios} Años, ${meses} Meses, ${dias} Días`;
	}else{
		return `${meses} Meses, ${dias} Días`;
	}
    
}

const menu = ref();
const sidebar = ref();

const toggle = (event) => {
	menu.value.toggle(event);
};

const expandAll = () => {
	for (let node of items.value) {
		expandNode(node);
	}

	expandedKeys.value = { ...expandedKeys.value };
};

const collapseAll = () => {
	expandedKeys.value = {};
};

const expandNode = (node) => {
	if (node.items && node.items.length) {
		expandedKeys.value[node.key] = true;

		for (let child of node.items) {
			expandNode(child);
		}
	}
};
const overlay = ref();

//menu btn
function toggleMobileMenu() {
	sidebar.value.classList.toggle("translate-x-0");
	overlay.value.classList.toggle("hidden");
	setTimeout(() => overlay.value.classList.toggle("opacity-0"), 0);
	document.body.style.overflow = sidebar.value.classList.contains(
		"translate-x-0"
	)
		? "hidden"
		: "";
}

function setMesesSeleccionados () {

	const currentDate = new Date();
	const currentYear = currentDate.getFullYear();
	const currentMonth = currentDate.getMonth() + 1; 

	if(tipoReporte.value == 'acumulado'){
		const mesesPasados = Array.from({ length: currentMonth  }, (_, index) => ({
			name: new Intl.DateTimeFormat('es', { month: 'long' }).format(new Date(currentYear, index)).charAt(0).toUpperCase() + 
				new Intl.DateTimeFormat('es', { month: 'long' }).format(new Date(currentYear, index)).slice(1),
			code: (index + 1).toString(),
		}));

		mesesSeleccionados.value = mesesPasados;

		anioSeleccionado.value = currentYear.toString();
	}else{
		mesesSeleccionados.value =  Array.from({ length: 1  }, (_, index) => ({
			name: new Intl.DateTimeFormat('es', { month: 'long' }).format(new Date(currentYear, currentMonth - 1)).charAt(0).toUpperCase() + 
				new Intl.DateTimeFormat('es', { month: 'long' }).format(new Date(currentYear, currentMonth - 1)).slice(1),
			code: (currentMonth).toString(),
		}));

		console.log(mesesSeleccionados.value);

		anioSeleccionado.value = currentYear.toString();
	}
	
}

onMounted(async () => {

	setMesesSeleccionados();
	if(role == 'Broker') {
		oficinaSeleccionada.value = user.agent.office.id;
	}
	else{
		if(role == 'Agente'){
			oficinaSeleccionada.value = user.agent.office.id;
			agenteSeleccionado.value = user.agent.id;
		}else{ //Administrador
			await getOficinas();
		}
	}
	
	obtenerTodosLosDatos();

});


const loadingStore = useIsLoadingStore();

const toggleLoading = (isLoading) => {
  loadingStore.setLoading(isLoading);  
};

</script>
<style scoped>
.read-the-docs {
	color: #888;
}

.btn-tipo-reporte {
	background-color: white;
	transition: all 0.3s ease;
}

.btn-tipo-reporte.activo {
	background: linear-gradient(to bottom, #4f46e5, #4338ca);
	/* Indigo-600 to Indigo-700 */
	color: white;
	font-weight: bold;
	box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
}

::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}

::-webkit-scrollbar-thumb {
  background-color: rgba(0, 0, 0, 0.3);
  border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
  background-color: rgba(0, 0, 0, 0.5);
}

::-webkit-scrollbar-track {
  background-color: rgba(0, 0, 0, 0.1);
  border-radius: 10px;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
  transform: translateY(-10%);
}
.fade-enter-to, .fade-leave-from {
  opacity: 1;
  transform: translateY(0);
}
</style>
