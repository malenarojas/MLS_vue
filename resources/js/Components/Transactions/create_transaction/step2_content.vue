<template>
	<div class="rounded bg-indigo-50 p-4 shadow-md">
		<h1 class="opacity-85 font-semibold text-lg">Detalles de la transacción</h1>

		<div class="mt-4 bg-white rounded p-4 border">
			<h2 class="opacity-85 font-semibold"><i class="pi pi-user text-xl" /> <i
					class="pi pi-home -translate-x-1 text-xs" />Comision captador/locator</h2>

			<table class="w-full mt-4 opacity-75 text-sm">
				<thead class="table-auto  border-b">
					<tr>
						<th class="pb-1">Agente</th>
						<th class="pb-1">Tipo de comision</th>
						<th class="pb-1">Cantidad</th>
						<th class="pb-1">Porcentaje</th>
					</tr>
				</thead>
				<tbody class="text-center">
					<tr v-for="agente in agentesComisiones" class="*:py-2 *:border-b">
						<td>{{ agente.name }}</td>
						<td>
							<Select size="small" v-model="agente.tipo_comision" optionLabel="name" class="" :options="tipoComisiones" />
						</td>
						<td>
							<InputNumber @value-change="actualizarTotales()" size="small" v-model="agente.cantidad">
							</InputNumber>
						</td>
						<td>
							{{ agente.porcentaje }} %
						</td>
						<td>
							<button 
							class="rounded-full text-white bg-gradient-to-t 
							from-emerald-700 to-emerald-600 shadow-md py-1.5 px-2"
							@click="borrarAgenteComision(agente)">
							<i class="pi pi-trash" /></button>
						</td>
					</tr>
				</tbody>
				<tfoot class="text-center">
					<tr class="*:py-2">
						<td></td>
						<td>Comision total</td>
						<td>{{ totalComision }}</td>
						<td>{{ totalPorcentaje }} %</td>
						<td><Button size="small" label="Agregar agente" @click="visible = true" /></td>
					</tr>
				</tfoot>

			</table>

			
		</div>
	</div>

	<Dialog v-model:visible="visible" modal header="Añadir agente" :style="{ width: '50rem' }">
		
		<div class="mb-8 flex gap-2 flex-col">
			<label for="text-sm ">Oficina</label>
			<Select v-model="oficinasSeleccionadas" size="small" optionLabel="name" filter
			:options="oficinas" placeholder="Seleccionar" @value-change="changeOficina()">
			</Select>
		</div>

		<div class="mb-8 flex gap-2 flex-col">
			<label for="text-sm ">Agente</label>
			<Select :disabled="!oficinasSeleccionadas" size="small" v-model="agentesSeleccionados" optionLabel="name" filter
			:options="agentes" placeholder="Seleccionar">

			</Select>
		</div>

		<div class="flex justify-end gap-2">
			<Button type="button" label="Cancelar" size="small" severity="secondary" @click="visible = false"></Button>
			<Button type="button" label="Guardar" size="small" :disabled="!agentesSeleccionados" @click="visible = false; selectAgente();"></Button>
		</div>


	</Dialog>

</template>

<script setup>

import apiClient from "@/src/constansts/axiosClient";

let agentesComisiones = ref([]);
let visible = ref(false);

const tipoComisiones = ref([
	{code : 1 , name : 'Comision'},
	{code : 2 , name : 'Referidos'},
])

let totalComision = ref(0);
let totalPorcentaje = ref(0);

function actualizarTotales () {
	totalComision.value = 0;
	totalPorcentaje.value = 0;
	agentesComisiones.value.forEach(agente => {
		totalComision.value += agente.cantidad;
		totalPorcentaje.value += agente.porcentaje;
	});
}

function borrarAgenteComision (agente) {
	agentesComisiones.value = agentesComisiones.value.filter(a => a.id !== agente.id);
	actualizarTotales();
}

import { useFiltros } from '@/composables/dashboard/filtros';

function changeOficina () {
	agentesSeleccionados.value = '';
		if(oficinasSeleccionadas.value){
			getAgentes();
		}
}

async function getAgentes() {
	const data = {
		'office_id': oficinasSeleccionadas.value ? [oficinasSeleccionadas.value.code] :  [],
	};
	const response = await apiClient.post('/dashboard/get-agentes-por-ubicacion', data);
	agentes.value = response.data.map(agente => ({
		code: agente.id,
		name: agente.name
	}));
}

function selectAgente () {
	console.log(agentesSeleccionados.value);
	agentesComisiones.value.push({
		name: agentesSeleccionados.value.name,
		id: agentesSeleccionados.value.code,
		tipo_comision : ref({name : 'Comision', code: 1}),
		cantidad : ref(0),
		porcentaje: ref(0),
	});

	console.log(agentesComisiones.value);
}

const {
	oficinas,
	oficinasSeleccionadas,
	agentes,
	agentesSeleccionados,
	getOficinas,
} = useFiltros();

onMounted(() => {
	getOficinas();
})
</script>
