<template>
    <div :class="['rounded-xl shadow-md bg-indigo-50 p-4 border-indigo-200 border col-span-1', 
					chartTransaccionesExpandido ? 'md:col-span-3' : 'md:col-span-2'] ">
					<div class="flex justify-between items-center border-b pb-2">

						<h2 class="font-semibold opacity-75 uppercase">Transacciones</h2>
						<button @click="expandirChartTransacciones()">
							<i class="pi pi-window-maximize opacity-75"></i>
						</button>

					</div>
					<div :class="['mt-4 flex justify-between',chartTransaccionesExpandido ? '' : 'hidden']">
						<div>
							<div class="flex gap-2 items-center">
								<h3 class="font-bold text-xl opacity-75">Total: {{ totalTransacciones }}</h3>
								<span
									:class="['font-bold', totalTransacciones > totalTransaccionesComparar ? 'text-green-500' : 'text-red-500']">
									({{ totalTransaccionesComparar > 0 ? ((totalTransacciones -
									totalTransaccionesComparar) /
									totalTransaccionesComparar
									*
									100).toFixed(2) : 100 }} %)
								</span>
							</div>
							<h3 class="font-semibold  opacity-65">Anterior: {{ totalTransaccionesComparar }}</h3>
						</div>
						<select name="" id="" class="mb-auto px-2" v-model="tipoTransaccion" @change="getTransacciones()">
							<option value="0">2 side/1 side</option>
							<option value="2">2 side</option>
							<option value="1">1 side</option>
						</select>
					</div>
					<div class="justify-center flex w-full">
						
						<client-only class="w-full">

							<VueApexCharts type="bar" :options="optionsTransacciones" :series="seriesTransacciones"
								ref="chartTransacciones" :height="chartTransaccionesExpandido ? '500' : '300'" class="w-full" />	
						</client-only>
					</div>
				</div>
</template>
<script setup>

import { ref, watch, defineEmits } from 'vue';
import { split } from "postcss/lib/list";
import VueApexCharts from 'vue3-apexcharts';
import apiClient from '@/src/constansts/axiosClient';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    datosTransacciones: Array,
	tipoReporte: String,
	anioSeleccionado: String,
	mesesSeleccionados: Array,
	agenteSeleccionado: String,
	oficinaSeleccionada: String,
});

const emit  = defineEmits(['toggleLoading']);

const oficinaSeleccionada = ref(props.oficinaSeleccionada);
const agenteSeleccionado = ref(props.agenteSeleccionado);


let totalTransacciones = ref(0);
let totalTransaccionesComparar = ref(0);
let chartTransaccionesExpandido = ref(false);
const chartTransacciones = ref(null);

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

watch(() => props.datosTransacciones, (datosTransacciones) => {
	renderTransacciones(datosTransacciones);
});


let tipoTransaccion = ref(0);

async function getTransacciones() {
	console.log(props.mesesSeleccionados);
	const data = {
		mesesSeleccionados: props.mesesSeleccionados ? props.mesesSeleccionados.map(mes => mes.code) :
			[1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
		anioSeleccionado: props.anioSeleccionado ?? '2025',
		tipoReporte: props.tipoReporte,
		oficina_id: props.oficinaSeleccionada,
		agente_id: props.agenteSeleccionado,
		tipo_transaccion: tipoTransaccion.value
	};
	emit('toggleLoading', true);
	const response = await apiClient.post('/dashboard/get-transacciones', data, {
		timeout: 30000,
	});
	if (response.status == 200) {
		renderTransacciones(response.data);
	}
	emit('toggleLoading', false);
}

function expandirChartTransacciones () {
	chartTransaccionesExpandido.value = !chartTransaccionesExpandido.value;
	renderTransacciones(props.datosTransacciones);
}

const optionsTransacciones = ref({
	chart: {
		type: "bar",
		stacked: true,
		toolbar: {
			show: true,
		},
	},
	plotOptions: {
		bar: {
			horizontal: false,
			columnWidth: "50%",
		},
	},
	dataLabels: {
		enabled: false,
	},
	xaxis: {
		categories: [],
	},
	yaxis: {
		title: {
			text: "Valores",
		},
	},
	colors: ["#22bb77", "#ff9f2d", "#93C5FD", "#f52718"],
	legend: {
		position: "top",
	},
});

// Series iniciales del gráfico
const seriesTransacciones = ref([
	{
		name: "Ventas",
		data: [30, 40, 45, 50],
	},
	{
		name: "Alquileres",
		data: [20, 30, 25, 35],
	},
]);

function renderTransacciones(datosTransacciones) {
	const categorias = [];
	const seriesVentas = [];
	const seriesAlquileres = [];
	const seriesAnticreticos = [];
	const seriesExternas = [];

	const seriesVentasComparar = [];
	const seriesAlquileresComparar = [];
	const seriesAnticreticosComparar = [];
	const seriesExternasComparar = [];

	const totalesPorMes = [];
	const totalesPorMesComparar = [];
	const comparacionTotalesPorcentaje = [];


	totalTransacciones.value = 0
	totalTransaccionesComparar.value = 0
	
	datosTransacciones.forEach(datoTransaccionesMes => {
		const data = datoTransaccionesMes['transaccionesVentas']['0'] || datoTransaccionesMes['transaccionesAlquileres']['0'] || datoTransaccionesMes['transaccionesAnticreticos']['0'] || datoTransaccionesMes['transaccionesExternas']['0'];
		if (data) {
			let fecha = data.mes;
			if (props.tipoReporte == 'mensual') {
				const fechaSplited = split(fecha, '-');
				fecha = getNombreMes(fechaSplited[1]);
				fecha = fecha.length < 3 ? fecha : fecha.substr(0, 3);
			}
			categorias.push(fecha);
			const ventas = datoTransaccionesMes['transaccionesVentas'] && datoTransaccionesMes['transaccionesVentas'].length != 0 ? datoTransaccionesMes['transaccionesVentas']['0'].total : 0;
			const alquileres = datoTransaccionesMes['transaccionesAlquileres'] && datoTransaccionesMes['transaccionesAlquileres'].length != 0 ? datoTransaccionesMes['transaccionesAlquileres']['0'].total : 0;
			const anticreticos = datoTransaccionesMes['transaccionesAnticreticos'] && datoTransaccionesMes['transaccionesAnticreticos'].length != 0 ? datoTransaccionesMes['transaccionesAnticreticos']['0'].total : 0;
			const externas = datoTransaccionesMes['transaccionesExternas'] && datoTransaccionesMes['transaccionesExternas'].length != 0 ? datoTransaccionesMes['transaccionesExternas']['0'].total : 0;
			
			seriesVentas.push(ventas);
			seriesAlquileres.push(alquileres);
			seriesAnticreticos.push(anticreticos);
			seriesExternas.push(externas);
			
			const totalPorMes = ventas + alquileres + anticreticos + externas;
			totalesPorMes.push(totalPorMes);

			const ventasComparar = datoTransaccionesMes['transaccionesVentasComparar'] && datoTransaccionesMes['transaccionesVentasComparar'].length != 0 ? datoTransaccionesMes['transaccionesVentasComparar']['0'].total : 0;
			const alquileresComparar = datoTransaccionesMes['transaccionesAlquileresComparar'] && datoTransaccionesMes['transaccionesAlquileresComparar'].length != 0 ? datoTransaccionesMes['transaccionesAlquileresComparar']['0'].total : 0;
			const anticreticosComparar = datoTransaccionesMes['transaccionesAnticreticosComparar'] && datoTransaccionesMes['transaccionesAnticreticosComparar'].length != 0 ? datoTransaccionesMes['transaccionesAnticreticosComparar']['0'].total : 0;
			const externasComparar = datoTransaccionesMes['transaccionesExternasComparar'] && datoTransaccionesMes['transaccionesExternasComparar'].length != 0 ? datoTransaccionesMes['transaccionesExternasComparar']['0'].total : 0;

			const totalPorMesComparar = ventasComparar + alquileresComparar + anticreticosComparar + externasComparar;
			totalesPorMesComparar.push(totalPorMesComparar);

			const diferencia = totalPorMes - totalPorMesComparar;
			comparacionTotalesPorcentaje.push(totalPorMesComparar > 0 ? (diferencia / totalPorMesComparar * 100).toFixed(2) : 100);

			totalTransacciones.value += totalPorMes;
			totalTransaccionesComparar.value += totalPorMesComparar;

			if (chartTransaccionesExpandido.value) {

				seriesVentasComparar.push(ventasComparar);
				seriesAlquileresComparar.push(alquileresComparar);
				seriesAnticreticosComparar.push(anticreticosComparar);
				seriesExternasComparar.push(externasComparar);
				
			}
		}

	});

	const totals = seriesVentas.map((venta, index) => {
		return venta + seriesAlquileres[index] + seriesAnticreticos[index] + seriesExternas[index];
	});

	const totalAnterior = seriesVentasComparar.map((venta, index) => {
		return venta + seriesAlquileresComparar[index] + seriesAnticreticosComparar[index] + seriesExternasComparar[index];
	});

	if(chartTransaccionesExpandido.value){
		seriesTransacciones.value = [
		{
			name: "Ventas",
			data: seriesVentas,
			group: 'actual'
		},
		{
			name: "Alquileres",
			data: seriesAlquileres,
			group: 'actual'
		},
		{
			name: "Anticreticos",
			data: seriesAnticreticos,
			group: 'actual'
		},
		{
			name: "Externas",
			data: seriesExternas,
			group: 'actual'
		},
		{
			name: "Ventas Año Pasado",
			data: seriesVentasComparar,
			group: 'pasado'
		},
		{
			name: "Alquileres Año Pasado",
			data: seriesAlquileresComparar,
			group: 'pasado'
		},
		{
			name: "Anticreticos Año Pasado",
			data: seriesAnticreticosComparar,
			group: 'pasado'
		},
		{
			name: "Externas Año Pasado",
			data: seriesExternasComparar,
			group: 'pasado'
		},
		];

	} else {
		seriesTransacciones.value = [
			{
				name: "Ventas",
				data: seriesVentas,
				group: 'actual'
			},
			{
				name: "Alquileres",
				data: seriesAlquileres,
				group: 'actual'
			},
			{
				name: "Anticreticos",
				data: seriesAnticreticos,
				group: 'actual'
			},
			{
				name: "Externas",
				data: seriesExternas,
				group: 'actual'
			},
		];
	}
	const indicesPasado = [4, 5, 6, 7];

	const colores = ["#22bb77", "#ff9f2d", "#93C5FD", "#f52718", "#22bb77", "#ff9f2d", "#93C5FD", "#f52718"];

	
	function getColorsWithOpacity() {
		const indicesPasado = [4, 5, 6, 7]; 
		
		return colores.map((color, index) => {
			const opacity = indicesPasado.includes(index) ? 0.3 : 1; 
			return `rgba(${parseInt(color.slice(1, 3), 16)}, ${parseInt(color.slice(3, 5), 16)}, ${parseInt(color.slice(5, 7), 16)}, ${opacity})`;
		});
	}
	optionsTransacciones.value.xaxis.categories = categorias;
	if(chartTransaccionesExpandido.value) {
		optionsTransacciones.value = {
			chart: {
				type: 'bar',
				stacked: true, 
			},
			plotOptions: {
				bar: {
					columnWidth: '60%',
				},
			},
			dataLabels: {
				enabled: true,
				style: {
					fontSize: '12px',
					colors: [],
					fontWeight: 'bold',
					
				},
				formatter: function (val, opts) {
					const porcentajeActual = val / totals[opts.dataPointIndex] * 100;

					if (indicesPasado.includes(opts.seriesIndex)) {
							const porcentajepasado = val / totalAnterior[opts.dataPointIndex] * 100;
							return `(${porcentajepasado.toFixed(2)} %)`
						}
					
					return val + " (" + porcentajeActual.toFixed(2) + "%)";
				},
				dropShadow: { //Lo lentea
					enabled: false,
					top: 0.75,
					left: 0.75,
					blur: 0.75,
					color: '#000',
					opacity: 0.45
				},
				verticalOffset: -10, 
			},
			xaxis: {
				categories: categorias,
			},
			fill: {
				colors: getColorsWithOpacity(),
			},
			legend: {
				position: 'top',
			},
			annotations: {
				yaxis: [],
				xaxis: totalesPorMes.map((total, index) => {
					
					const primeraAnotacion = {
						x: categorias[index],
						y: total + 5,  
						borderColor: '#000',
						borderWidth: 0.5,
						label: {
							text: total.toString(),
							style: {
								color: '#000',
								background: '#fff',
								fontSize: '14px',
								fontWeight: 'bold',
							},
						},
					};

					
					const segundaAnotacion = {
						x: categorias[index],
						y: totalesPorMesComparar[index] + 10,  
						borderColor: '#000',
						borderWidth: 0.5,
						label: {
							text: `${totalesPorMesComparar[index].toString()}`, 
							style: {
								color: 'rgba(0, 0, 0, 0.5)',
								background: `rgba(255, 255, 255, 0.5)`,
								fontSize: '14px',
								fontWeight: 'bold',
							},
							offsetX : 25,
						},
					};
					const antotacionPorcentaje = {
						x: categorias[index],
						y: totalesPorMesComparar[index] + 10,  
						borderColor: '#000',
						borderWidth: 0.5,
						label: {
							text: `${comparacionTotalesPorcentaje[index].toString()} %`, 
							style: {
								color: comparacionTotalesPorcentaje[index] > 0 ? 'rgba(10, 207, 6, 0.5)' : 'rgba(207, 6, 6, 0.5)',
								background: `rgba(255, 255, 255, 0.5)`,
								fontSize: '14px',
								fontWeight: 'bold',
							},
							offsetX : 46,
						},
					};

					return [primeraAnotacion, segundaAnotacion, antotacionPorcentaje];
				}).flat(), 
			},
		}

		optionsTransacciones.value.dataLabels.style.colors = Array(categorias.length).fill('rgba(0,0,0,1)').map((color, idx) => {
			if (indicesPasado.includes(idx)) {
				return 'rgba(0,0,0,0.7)';
			}
			return color; 
		});
		
	} else {
		optionsTransacciones.value = {
			chart: {
				type: "bar",
				stacked: true,
				toolbar: {
					show: true,
				},
			},
			plotOptions: {
				bar: {
					horizontal: false,
					columnWidth: "60%",
				},
			},
			dataLabels: {
				enabled: false,
			},
			xaxis: {
				categories: categorias,
			},
			yaxis: {
				title: {
					text: "Valores",
				},
			},
			colors: ["#22bb77", "#ff9f2d", "#93C5FD", "#f52718",],
			legend: {
				position: "top",
			},
		}
	}
	optionsTransacciones.value.chart.events = {
		dataPointSelection: (event, chartContext, config) => {
			clickColumnaTransacciones(config, chartContext, event);
		},
	}
	chartTransacciones.value.updateOptions(optionsTransacciones.value);
	
}


function clickColumnaTransacciones (config) {
	if(config.seriesIndex < 3){
		const columnaSeleccionada = parseInt(config.dataPointIndex);
		const anio = props.anioSeleccionado;
		const type = config.seriesIndex == 0 ? 'venta' : 
					config.seriesIndex == 1 ? 'alquiler' : 'anticretico';
		const office_id = props.oficinaSeleccionada ?? '';
		const agent_id = props.agenteSeleccionado ?? '';

		const mesSeleccionado = meses.value.find(mes => mes.name.substring(0, 3) ==  optionsTransacciones.value.xaxis.categories[columnaSeleccionada])
		
		const query = 
		{ 
			mes: mesSeleccionado.code, 
			anio: anio, 
			type: type,
		}
		
		if(office_id){
			query.office_id = office_id;
		}
		if(agent_id) {
			query.agent_id =agent_id;
		}

		router.get('/dashboard/transacciones', query, {
			preserveState: true
		});
	}

}

const getNombreMes = (numero) => {
	const mesCodigo = parseInt(numero, 10).toString();
	const mes = meses.value.find(m => m.code === mesCodigo);
	return mes ? mes.name : 'Mes no encontrado';
}
</script>