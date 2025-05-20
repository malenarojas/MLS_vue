<template>
    <div class="rounded-xl shadow-md bg-indigo-50 p-4 border-indigo-200 border col-span-2 mb-auto ">
		<div class="flex justify-between items-center border-b pb-2">
			<div>
				<h2 class="font-semibold opacity-75 uppercase">Comisiones</h2>
			</div>

			<button class="rounded bg-gradient-to-t from-gray-300 to-gray-200 text-sm
				font-semibold py-1 px-2 border border-gray-300 hidden">
				<span class="opacity-85">Ver reporte</span>
			</button>
		</div>

		<div class="flex justify-start mt-2">
			<h3 class="text-2xl font-semibold opacity-75"> {{ Intl.NumberFormat(('en-US')).format(total) }}
			</h3>
		</div>

		<div>
			<client-only>
				<VueApexCharts ref="chart2" type="bar" height="200" class="w-full" :options="options" :series="series">
				</VueApexCharts>
			</client-only>
		</div>
	</div>
</template>
<script setup>
import { defineProps, watch, toRefs } from 'vue';
import VueApexCharts from "vue3-apexcharts";
import { ref } from 'vue';
import { split } from "postcss/lib/list";

const props = defineProps({
    dataComisiones: Array,
    tipoReporte: String,
    anioSeleccionado: Number,
});

const { dataComisiones } = toRefs(props);
const { tipoReporte } = toRefs(props);
const { anioSeleccionado } = toRefs(props);

let total = ref(0);
const chart2 = ref(null);




watch(dataComisiones, (newData) => {
	console.log(newData);
    renderComisiones(newData);
});


const options = ref({
	chart: {
		type: "bar",
		stacked: false, 
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
	yaxis: {
		labels: {
		formatter: function (value) {
			return value.toFixed(0);
		}
		}
	},
	xaxis: {
		categories: [], 
	},
	colors: ["#312e81", "#22bb77"], 
	fill: {
		type: "gradient",
		gradient: {
			shade: "dark",
			type: "vertical",
			gradientToColors: ["#4338ca"],
			stops: [0, 100],
		},
	},
	dataLabels: {
		enabled: false
	},
});

const series = ref([
	{
		name: "Año actual",
		data: [], 
	},
	{
		name: "Año anterior",
		data: [], 
	},
]);



function renderComisiones(dataComisiones) {
	const categorias = [];
	const valores = [];
	const valoresComprar = [];
	total.value = 0;

	for (let index = 0; index < dataComisiones['comisiones'].length; index++) {

		const comision = dataComisiones['comisiones'][index];
		const comisionComprar = dataComisiones['comisionesComprar'][index];

		if (comision[0]) {

			let fecha = comision[0]["mes"];

			if (tipoReporte.value == 'mensual') {

				fecha = split(comision[0]["mes"], '-');
				fecha = getNombreMes(fecha[1]);
				fecha = fecha.length > 3 ? fecha.substr(0, 3) : fecha;
			}

			categorias.push(fecha);
			valores.push(comision[0]["total_comisiones"]);
			if (tipoReporte.value == 'mensual') {
				valoresComprar.push(comisionComprar[0] ? comisionComprar[0]["total_comisiones"] : 0);
			}

			total.value = (parseFloat(total.value) + parseFloat(comision[0]["total_comisiones"])).toFixed(2);
		}

	}


	const porcentajes = valores.map((actual, index) => {
		const anterior = parseFloat(valoresComprar[index]);
		const cambio = anterior !== 0 ? ((parseFloat(actual) - anterior) / anterior) * 100 : 0;
		return anterior == 0 ? 100 : cambio.toFixed(2);
	});


	options.value.xaxis.categories = categorias;
	if (tipoReporte.value == 'mensual') {
		series.value = [
			{
				name: anioSeleccionado.value,
				data: valores,
			},
			{
				name: anioSeleccionado.value - 1,
				data: valoresComprar,
			},

		];

	} else {
		series.value = [
			{
				name: "Totales",
				data: valores.map(val => parseFloat(val))
			}
		];
	}


	if (chart2.value) {
		chart2.value.updateOptions({
			xaxis: { categories: categorias },
			dataLabels: {
				enabled: tipoReporte.value == 'mensual',
				formatter: function (val, opts) {
					if (opts.seriesIndex === 0) {
						const porcentaje = porcentajes[opts.dataPointIndex];
						return porcentaje + "%";
					}
					return "";
				},
				style: {
					fontSize: "12px",
					fontWeight: "bold",
					colors : ["#000000"],
				},
				offsetY: -20,
				
			},
			plotOptions: {
				bar: {
					dataLabels: {
						position: "top",
					},
				},
			},
			tooltip: {
				enabled: true,
			},
		});

	}
}

const getNombreMes = (numero) => {
	const mesCodigo = parseInt(numero, 10).toString();
	const mes = meses.value.find(m => m.code === mesCodigo);
	return mes ? mes.name : 'Mes no encontrado';
}

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
</script>