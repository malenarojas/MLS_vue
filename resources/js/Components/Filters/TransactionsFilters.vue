<template>
	<div class="flex gap-2 text-xs">
			<MultiSelect 
				v-model="tipoTransaccion" 
				optionLabel="name" 
				optionValue="code"
				:options="tiposTransacciones"
				placeholder="Seleccionar tipo de transacción" 
				@change="getTodosLosDatos()" />

			<MultiSelect 
				v-model="mesSeleccionado" 
				optionLabel="name" 
				optionValue="code"
				filter 
				:options="meses" 
				placeholder="Mes"
				:maxSelectedLabels="3" 
				@change="getTodosLosDatos()" />

			<MultiSelect 
				v-model="anioSeleccionado" 
				optionLabel="name"
				optionValue="code" 
				:options="anio" 
				placeholder="Anio"
				@change="getTodosLosDatos()" />

		</div>
    <div class="bg-indigo-50 p-4 flex justify-between mt-4 text-sm">
			<div class="flex gap-2">
				<MultiSelect 
					v-model="oficinasSeleccionadas" 
					v-if="['Administrador', 'Soporte', 'Super Administrador'].includes(role)"
					optionLabel="name" 
					filter
					optionValue="code"
					:options="oficinas" 
					placeholder="Oficina"
					:maxSelectedLabels="3"
					@change="changeOffice"  />

				<MultiSelect 
					:disabled="oficinasSeleccionadas == 0" 
					v-model="agentesSeleccionados" 
					v-if="['Administrador', 'Soporte', 'Super Administrador', 'Broker'].includes(role)"
					optionLabel="name" 
					filter
					optionValue="code"
					:options="agentes" 
					placeholder="Agente" 
					:maxSelectedLabels="3" 
					@change="getTodosLosDatos()" />
			</div>

			<div class="gap-2 flex">

				<MultiSelect 
					v-model="departamentoSeleccionado"
					optionLabel="name" 
					filter 
					:options="departamentos" 
					placeholder="Departamento" 
					optionValue="code"
					:maxSelectedLabels="2" />

				<MultiSelect 
					v-if="departamentoSeleccionado.length > 0"
					v-model="provinciaSeleccionada"
					optionLabel="name" 
					filter 
					optionValue="code"
					:options="provincias" 
					placeholder="Provincia" 
					:maxSelectedLabels="2" />

				<MultiSelect 
					v-if="provinciaSeleccionada.length > 0"
					v-model="ciudadSeleccinada"
					optionLabel="name" 
					filter 
					:options="ciudades"
					placeholder="Ciudad" 
					optionValue="code"
					:maxSelectedLabels="2" />

				<MultiSelect 
					v-if="ciudadSeleccinada.length > 0"
					v-model="zonaSeleccionada"
					optionLabel="name" 
					filter 
					@value-change="changeZona()" 
					:options="zonas"
					placeholder="Zona"
					optionValue="code"
					:maxSelectedLabels="2" />
			</div>
		</div>
</template>
<script setup>
import apiClient from '@/src/constansts/axiosClient';
import { usePage } from '@inertiajs/vue3';
import { MultiSelect } from 'primevue';
import { onMounted, defineEmits, ref, watch } from 'vue';


const regiones = ref([
	{ name: 'Bolivia', cod: 120 },
]);
const departamentos = ref([]);
const provincias = ref([]);
const ciudades = ref([]);
const zonas = ref([]);

const page = usePage();
const role = page.props.role;

const emit = defineEmits(['getDatosTransacciones', 'getDatosTipo']);

let regionSeleccionada = defineModel('regionSeleccionada',{
		type: Array,
		default: 120
	});
let departamentoSeleccionado = defineModel('departamentoSeleccionado',{
		type: Array,
		default: []
	});
let provinciaSeleccionada = defineModel('provinciaSeleccionada',{
		type: Array,
		default: []
	});
let ciudadSeleccinada = defineModel('ciudadSeleccinada',{
		type: Array,
		default: []
	});
let zonaSeleccionada = defineModel('zonaSeleccionada',{
		type: Array,
		default: []
	});
let agentesSeleccionados = defineModel('agentesSeleccionados',{
		type: Array,
		default: []
	});
let oficinasSeleccionadas = defineModel('oficinasSeleccionadas',{
		type: Array,
		default: []
	});
let tipoTransaccion = defineModel('tipoTransaccion',{
		type: Array,
		default: []
	});
let mesSeleccionado = defineModel('mesSeleccionado',{
		type: Array,
		default: []
	});
let anioSeleccionado = defineModel('anioSeleccionado',{
		type: Array,
		default: []
	});

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

const anio = ref([
	{ 'name': '2024', "code": 2024 },
	{ 'name': '2025', "code": 2025 },
]);

let tiposTransacciones = ref([
	{code : 1, name: 'Ventas'},
	{code : 2, name: 'Alquileres'},
	{code : 3, name: 'Anticreticos'},
]);

watch(() => regionSeleccionada.value, async (nuevoRegion) => {
	if (nuevoRegion) {
		await getDepartamentos();
		emit('getDatosTransacciones');
	}
});

watch(() => departamentoSeleccionado.value, async (nuevoDepartamento) => {
	if (nuevoDepartamento) {
		await getProvincias(nuevoDepartamento);
		emit('getDatosTransacciones');
	}
});

watch(() => provinciaSeleccionada.value, async (nuevaProvincia) => {
	if (nuevaProvincia) {
		await getCiudades(nuevaProvincia);
		emit('getDatosTransacciones');
	}
});
watch(() => ciudadSeleccinada.value, async (nuevaCiudad) => {
	if (nuevaCiudad) {
		await getZonas(nuevaCiudad);
		emit('getDatosTransacciones');
	}
});
async function changeOffice () {
	agentesSeleccionados.value = '';
	
	if(nuevaOficina){
		await getAgentes(nuevaOficina);
	}
	emit('getDatosTransacciones');
}
watch(() => agentesSeleccionados.value, async (nuevoAgente) => {
	if (nuevoAgente) {
		emit('getDatosTransacciones');
	}
});
watch(() => tipoTransaccion.value, async (nuevoTipo) => {
	if (nuevoTipo) {
		emit('getDatosTransacciones');
	}
});

async function getDepartamentos() {
	const response = await apiClient.post('/dashboard/get-departamentos', {
		'region_id': regionSeleccionada.value ?? 120
	});
	if (response.status == 200) {
		departamentos.value = response.data.map(departamento => {
			return {
				code: departamento.id,
				name: departamento.name,
			}
		});
	}
}
async function getTodosLosDatos () {
	emit('getDatosTransacciones');
}

async function getProvincias(state_ids) {
	const response = await apiClient.post('/dashboard/get-provincias', {
		'state_id': state_ids
	});
	if (response.status == 200) {
		provincias.value = response.data.map(provincia => {
			return {
				code: provincia.id,
				name: provincia.name,
			}
		});
	}
}

async function getCiudades(province_ids) {
	const response = await apiClient.post('/dashboard/get-ciudades', {
		'province_id': province_ids
	});
	if (response.status == 200) {
		ciudades.value = response.data.map(ciudad => {
			return {
				code: ciudad.id,
				name: ciudad.name,
			}
		});
	}
}


async function changeZona() {
	emit('getDatosTransacciones');
}


async function getZonas(city_ids) {
	const response = await apiClient.post('/dashboard/get-zonas', {
		'city_id': city_ids
	});
	if (response.status == 200) {
		zonas.value = response.data.map(zona => {
			return {
				code: zona.id,
				name: zona.name,
			}
		});;
	}
}

let oficinas = ref([]);

async function getOficinas () {
	const data = {
		'province_ids' : provinciaSeleccionada.value ?? [],
		'city_ids' : ciudadSeleccinada.value ?? [],
	}
	const response = await apiClient.post('/dashboard/get-oficinas-por-ubicacion', data);
	oficinas.value = response.data.map(oficina => {
		return {
			code : oficina.id,
			name : oficina.name
		}
	})
}

const agentes = ref([]);

async function getAgentes (office_ids) {
	const data = {
		'province_ids' : provinciaSeleccionada.value ?? [],
		'city_ids' : ciudadSeleccinada.value ?? [],
		'office_id' : office_ids ?? [],
	}
	const response = await apiClient.post('/dashboard/get-agentes-por-ubicacion', data);
	agentes.value = response.data.map(agente => {
		return {
			code : agente.id,
			name : agente.name
		}
	})
}

// function cargarFiltrosFechas () {
// 	mesSeleccionado.value = [meses.value.find(mes => parseInt(mes.code) == parseInt(props.mes))?.code];
// 	anioSeleccionado.value = [anio.value.find(anio => anio.code == props.anio)?.code];
// }

async function cargarFiltrosOficinasAgentes () {

	// oficinasSeleccionadas.value = props.office_id ? [oficinas.value.find(oficina => oficina.code == props.office_id)] : [];
	if(oficinasSeleccionadas.value.length > 0) {
		await getAgentes(oficinasSeleccionadas.value);
	} 
	// if(agentesSeleccionados.value.length > 0) {
	// 	agentesSeleccionados.value = [agentes.value.find(agente => agente.code == props.agent_id)];
	// }
}



onMounted( async () => {
	getDepartamentos();
	await getOficinas();
	await cargarFiltrosOficinasAgentes();
	emit('getDatosTransacciones');
});
</script>