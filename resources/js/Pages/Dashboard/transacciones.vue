<template>
	<AppLayout>

	<div class="p-4">
		<h1 class="font-bold text-2xl opacity-75 mb-4"><i class="pi pi-arrow-right-arrow-left"></i> Transacciones</h1>

		<TransactionFilters 
			v-model:regionSeleccionada="regionSeleccionada"
			v-model:departamentoSeleccionado="departamentoSeleccionado"
			v-model:provinciaSeleccionada="provinciaSeleccionada"
			v-model:ciudadSeleccinada="ciudadSeleccinada"
			v-model:zonaSeleccionada="zonaSeleccionada"
			v-model:agentesSeleccionados="agentesSeleccionados"
			v-model:oficinasSeleccionadas="oficinasSeleccionadas"
			v-model:tipoTransaccion="tipoTransaccion"
			v-model:mesSeleccionado="mesSeleccionado"
			v-model:anioSeleccionado="anioSeleccionado"
			@getDatosTransacciones="getDatosTransacciones()" />

		<div class="grid md:grid-cols-2 gap-4 mt-4">
			<client-only>
				<VueApexCharts type="bar" height="400" :options="optionsTipos" :series="seriesTipos" ref="chartTipos"
					class="w-full" />
			</client-only>

			<client-only>
				<VueApexCharts type="bar" height="400" :options="optionsSubtipos" :series="seriesSubtipos" ref="chartSubTipos"
					class="w-full" />
			</client-only>
		</div>



		<!-- <div class="w-full overflow-x-auto mt-4" v-if="transaccionesDetalladas != []">
			
			<DataTable paginator :rows="5" :rowsPerPageOptions="[5, 10, 20, 50]" 
			:value="transaccionesDetalladas" stripedRows  class="text-sm"
			@row-click="getComisionesTransaccion" selectionMode="single" :metaKeySelection="metaKey" dataKey="transaction_id">
				<Column field="transaction_id" sortable  header="Id"></Column>
				<Column field="MLSID" sortable  header="MLSID"></Column>
				<Column field="transaction_type_id" sortable  header="Tipo transaccion"></Column>
				<Column field="current_listing_price" sortable  header="Precio captacion"></Column>
				<Column field="land_m2" sortable  header="m2"></Column>
				<Column field="number_bedrooms" sortable  header="Numero de habitaciones"></Column>
				<Column field="number_bathrooms" sortable  header="Numero de baños"></Column>
				<Column field="sold_date" sortable  header="Fecha de venta"></Column>
			</DataTable>
		</div>
		<div class="w-full overflow-x-auto mt-4" v-if="comisionesTransaccion != []">
			
			<DataTable paginator :rows="5" :rowsPerPageOptions="[5, 10, 20, 50]" 
			:value="comisionesTransaccion" stripedRows  class="text-sm"
			selectionMode="single" :metaKeySelection="metaKey" dataKey="id">
				<Column field="id" sortable  header="Id"></Column>
				<Column field="total_commission_amount" sortable  header="Monto"></Column>
				<Column field="type" sortable  header="Tipo"></Column>
				<Column field="total_commission_percentage" sortable  header="Porcentaje"></Column>
			
			</DataTable>
		</div> -->

	</div>
	</AppLayout>
</template>

<script setup>
import { onMounted, ref } from "vue";
import apiClient from '@/src/constansts/axiosClient';
import VueApexCharts from "vue3-apexcharts";
import TransactionFilters from '@/Components/Filters/TransactionsFilters.vue';
import AppLayout from "@/Layouts/AppLayout.vue";

let optionsTipos = ref([]);
let seriesTipos = ref([]);
const subtipoName = ref(null);

const props = defineProps({
		office_id : {
			type: Number,
			default: 0
		},
		agent_id : {
			type: Number,
			default: 0
		},
		mes : {
			type: Number,
			default: 0
		},
		anio : {
			type: Number,
			default: 0
		},
		type : {
			type: String,
			default: 'venta'
		}
});

let tipoTransaccion = ref([]);


let departamentoSeleccionado = ref([]);
let provinciaSeleccionada = ref([]);
let ciudadSeleccinada = ref([]);
let zonaSeleccionada = ref([]);
let regionSeleccionada = ref([]);
regionSeleccionada.value = 120;
let mesSeleccionado = ref ([]);
let anioSeleccionado = ref ([]);
let oficinasSeleccionadas = ref([]);
let agentesSeleccionados = ref([]);


const type = props.type;
tipoTransaccion.value = [(type == 'venta' ? 1 : type == 'alquiler' ? 2 : 3)];

let comisionesTransaccion = ref([]);

const getComisionesTransaccion = async (event) => {

	const data = {
		'transaction_id' : event.data.transaction_id,
	}
	console.log(data);
	const response = await apiClient.post('/dashboard/get-comisiones', data);
	if(response.status == 200) {
		console.log(response.data);
		comisionesTransaccion.value = response.data;
		comisionesTransaccion.value = comisionesTransaccion.value.map(commission => {
			commission.total_commission_amount = `${Intl.NumberFormat('en-US').format(commission.total_commission_amount)} ${commission.referral_commission_amount_currency?? 'USD' }`;
			commission.type = commission.commission_type_id == 1 ? 'Comision' : 'Referido';
			commission.total_commission_percentage = `${commission.total_commission_percentage}  %`;
			return commission;
		});
		console.log(comisionesTransaccion.value);
	} 
	
}

async function getDatosTransacciones() {
	console.log('getDatosTransacciones');
	const data = {
		'porpertySubtypename': subtipoName.value,
		'months': mesSeleccionado.value,
		'years': anioSeleccionado.value,
		'types': tipoTransaccion.value,
		'state_ids': departamentoSeleccionado.value?? [],
		'province_ids': provinciaSeleccionada.value ??[],
		'city_ids': ciudadSeleccinada.value ??[],
		'zone_ids': zonaSeleccionada.value ??[],
		'office_ids': oficinasSeleccionadas.value ??[],
		'agent_ids': agentesSeleccionados.value ?? [],
	};
	const response = await apiClient.get('/dashboard/transactions/get-transactions-by-type', {
		params: data,
		timeout: 30000,
	});
	if (response.status == 200) {
		renderTransaccionesTipos(response.data);
		// emit(['getDatosTransacciones'], response.data);
	}
}

function clickColumnaTipo(config, chartContext, event) {
	subtype_selected_name = tiposNames[config.dataPointIndex];
	getDatosTipo(subtype_selected_name);
}

async function getDatosTipo(nombreTipo) {

	const data = {
		'propertyTypeName': nombreTipo,
		'months': mesSeleccionado.value,
		'years': anioSeleccionado.value,
		'types': tipoTransaccion.value,
		'state_ids': departamentoSeleccionado.value?? [],
		'province_ids': provinciaSeleccionada.value ??[],
		'city_ids': ciudadSeleccinada.value ??[],
		'zone_ids': zonaSeleccionada.value ??[],
		'office_ids': oficinasSeleccionadas.value ??[],
		'agent_ids': agentesSeleccionados.value ?? [],
	};
	const response = await apiClient.get('/dashboard/transactions/get-transactions-by-type', {
		params: data,
		timeout: 30000,
	});
	if(response.status == 200) {
		console.log(response.data);
		renderSubtipos(response.data);
	}
	else{

	}
}

const chartTipos = ref(null);
let tiposNames = ref([]);
function renderTransaccionesTipos(data) {
	const categorias = [];
	const series = [];

	Object.entries(data).forEach(([key, tipoTransaccion]) => {
		series.push(tipoTransaccion.transactions);
		categorias.push(tipoTransaccion.property_type);
	});
	
	tiposNames = categorias;
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
			text: 'Transacciones por tipo',
			align: 'center'
		}
	};

	seriesTipos.value = [
		{
			name: 'Cantidad',
			data: series,
		}
	];

	chartTipos.value.updateOptions(optionsTipos.value);
}

let subtype_selected_name = null;

let optionsSubtipos = ref([]);
let seriesSubtipos = ref([]);

let subTiposNames = ref([]);

function renderSubtipos(data) {
	const series = [];
	const categorias = [];
	console.log(data);
	data.forEach((transaccion) => {
		series.push(transaccion.transactions);
		categorias.push(transaccion.property_type);
	});
	console.log(series);
	console.log(categorias);
	subTiposNames.value = categorias;
	optionsSubtipos.value = {
		chart: {
			type: 'bar',
			height: 350,
			stacked: false,
			toolbar: {
				show: false
			},
			events : {
				dataPointSelection: (event, chartContext, config) => {
					clickColumnaSubTipo(config, chartContext, event);
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
			text: 'Transacciones por subtipo',
			align: 'center'
		}
	};

	seriesSubtipos.value = [
		{
			name: 'Cantidad',
			data: series,
		}
	];
}

let transaccionesDetalladas = ref([]);

async function clickColumnaSubTipo (config, chartContext, event) {
	console.log(config);
	subtipoName.value = subTiposNames.value[config.dataPointIndex];
	transaccionesDetalladas.value = await getDetallesTransacciones(subtipoName.value);

	transaccionesDetalladas.value = transaccionesDetalladas.value.map(transaccion => {

		transaccion.current_listing_price = `${Intl.NumberFormat('en-US').format(transaccion.current_listing_price)} Bs`; 
		transaccion.transaction_type_id = transaccion.transaction_type_id == 1 ? 'L' : 'S'; 
		
		return transaccion;
	});
	console.log(transaccionesDetalladas.value);
}

async function getDetallesTransacciones (subtipoName) {
	const data = {
		'porpertySubtypename': subtipoName,
		'mes': mesSeleccionado.value,
		'anio': anioSeleccionado.value,
		'type': tipoTransaccion.value,
		'state_id': departamentoSeleccionado.value?? [],
		'province_id': provinciaSeleccionada.value ??[],
		'city_id': ciudadSeleccinada.value ??[],
		'zone_id': zonaSeleccionada.value ??[],
		'office_id': oficinasSeleccionadas.value ??[],
		'agent_id': agentesSeleccionados.value ?? [],
	}
	console.log(data);
	const response = await apiClient.post('/dashboard/get-detalles-transacciones', data, {
		timeout: 30000,
	});

	if(response.status == 200) {
		console.log(response.data);
		return response.data;
	}else{
		//Agregar logica para mostrar errores
		return [];
	}
}
onMounted(() => {
	
	if(props.mes)
	{
		mesSeleccionado.value = [props.mes];
	}

	if(props.anio)
	{
		anioSeleccionado.value = [parseInt(props.anio)];
	}
	if(props.office_id)
	{
		oficinasSeleccionadas.value = [parseInt(props.office_id)];
	}
	if(props.agent_id)
	{
		agentesSeleccionados.value = [parseInt(props.agent_id)];
	}

})
</script>
