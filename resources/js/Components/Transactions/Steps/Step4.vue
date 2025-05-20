<template>
	<div class="rounded bg-indigo-50 p-4 shadow-md">
		<h1 class="opacity-85 font-semibold text-lg">Detalles de la transacción</h1>

		<div class="mt-4 bg-white rounded p-4 border">
			<h2 class="opacity-85 font-semibold">
				<i class="pi pi-user text-xl" />
				<span v-if="(!isEmptyObject(store.getStepData(transactionId, 1)) && (store.getStepData(transactionId, 1)['side'] == 'both' || store.getStepData(transactionId, 1)['side']=='listing'))"><i class="pi pi-dollar -translate-x-1 text-xs" />Comision Compra/Locatario</span>
				<span v-if="(!isEmptyObject(store.getStepData(transactionId, 1)) && (store.getStepData(transactionId, 1)['side'] == 'selling' ))"><i class="pi pi-home -translate-x-1 text-xs" />Comision Captador/Locator</span>
			</h2>

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
					<tr v-for="agente in store.getStepData(transactionId, 4)" class="*:py-2 *:border-b">
						<td>{{ agente.agent_name }}</td>
						<td>
							<Select size="small" :disabled="!puedeEditar" v-model="agente.commission_type_id" optionValue="id" optionLabel="name"  class="" :options="tipoComisiones" />
						</td>
						<td>
							<InputNumber
								v-model="agente.total_commission_amount"
								size="small"
								min="0"
								:minFractionDigits="0"
								:maxFractionDigits="2"
								:disabled="!puedeEditar"
								>
							</InputNumber>
						</td>
						<td>
							{{ agente.total_commission_percentage }} %
						</td>
						<td>
							<button
							v-if="agente.can_delete && puedeEditar"
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
						<td>{{ Intl.NumberFormat('en-US').format(totalComision) }}</td>
						<td>{{ Intl.NumberFormat('en-US').format(totalPorcentaje) }} %</td>
						<td><Button size="small" label="Agregar agente" @click="visible = true" v-if="puedeEditar"/></td>
					</tr>
				</tfoot>

			</table>


		</div>
	</div>


	<Dialog
		v-model:visible="visible"
		modal
		header="Añadir agente"
		:style="{ width: '40rem' }"
	>
		<div class="mb-8 flex gap-2 flex-col">
			<label for="text-sm ">Oficina</label>
			<Select
				v-model="oficinasSeleccionadas"
				size="small"
				optionLabel="name"
				filter
				:options="oficinasTransaccion"
				placeholder="Seleccionar"
				@value-change="changeOficina()"
			>
			</Select>
		</div>

		<div class="mb-8 flex gap-2 flex-col">
			<label for="text-sm ">Agente</label>
			<div class="flex gap-2">
				<Select
					:disabled="!oficinasSeleccionadas"
					size="small"
					v-model="agentesSeleccionados"
					optionLabel="name"
					filter
					class="flex-auto"
					:options="agentes"
					placeholder="Seleccionar"
				>
				</Select>
				<div :class="{ hidden: !oficinasSeleccionadas.is_external }">
					<Button
						label="Nuevo agente externo"
						size="small"
						class="shadow-md text-[10px] p-0"
						@click="
							visible = false;
							visibleNewAgente = true;
						"
					/>
				</div>
			</div>
		</div>

		<div class="flex justify-end gap-2">
			<Button
				type="button"
				label="Cancelar"
				size="small"
				severity="secondary"
				@click="visible = false"
			></Button>
			<Button
				type="button"
				label="Guardar"
				size="small"
				:disabled="!agentesSeleccionados"
				@click="
					visible = false;
					selectAgente();
				"
			></Button>
		</div>
	</Dialog>

	<!--new user external-->
	<Dialog
		v-model:visible="visibleNewAgente"
		modal
		header="Crear nuevo agente externo"
		:style="{ width: '20rem' }"
	>
		<div class="mb-0 flex gap-2 flex-col">
			<Form
				v-slot="$form"
				:initial-values="formNuevo"
				:resolver="resolver"
				@submit="onFormSubmit"
				class="flex flex-col gap-2 w-full"
			>
				<div class="mb-0 flex gap-2 flex-col">
					<label for="text-sm ">Oficina Externa</label>
					<Select
						disabled
						v-model="oficinasSeleccionadas"
						size="small"
						optionLabel="name"
						filter
						:options="oficinasTransaccion"
						placeholder="Seleccionar"
						@value-change="changeOficina()"
					>
					</Select>
				</div>

				<div class="flex flex-col gap-1">
					<label for="text-sm ">Nombre</label>
					<InputText
						name="first_name"
						type="text"
						class="w-full"
						placeholder="Nombre"
						size="small"
						v-model="formNuevo.user.first_name"
						fluid
					/>
					<Message
						v-if="$form.first_name?.invalid"
						severity="error"
						size="small"
						variant="simple"
						>{{ $form.first_name.error.message }}</Message
					>
				</div>
				<div class="flex flex-col gap-1">
					<label for="text-sm ">Apellido</label>
					<InputText
						name="apellido"
						type="text"
						class="w-full"
						placeholder="Apellido"
						size="small"
						v-model="formNuevo.user.last_name"
						fluid
					/>
					<Message
						v-if="$form.last_name?.invalid"
						severity="error"
						size="small"
						variant="simple"
						>{{ $form.last_name.error.message }}</Message
					>
				</div>

				<div class="flex flex-col gap-1">
					<label for="text-sm ">Télefono</label>
					<InputText
						name="phone_number"
						type="text"
						class="w-full"
						placeholder="Télefono"
						size="small"
						v-model="formNuevo.user.phone_number"
						fluid
					/>
					<Message
						v-if="$form.phone_number?.invalid"
						severity="error"
						size="small"
						variant="simple"
						>{{ $form.phone_number.error.message }}</Message
					>
				</div>

				<div class="flex flex-col gap-1">
					<label for="text-sm ">Email</label>
					<InputText
						name="email"
						type="text"
						class="w-full"
						placeholder="email"
						size="small"
						v-model="formNuevo.user.email"
						fluid
					/>
					<Message
						v-if="$form.email?.invalid"
						severity="error"
						size="small"
						variant="simple"
						>{{ $form.email.error.message }}</Message
					>
				</div>

				<div class="flex justify-end gap-4">
					<Button
						type="button"
						label="Cancelar"
						size="small"
						severity="secondary"
						@click="visibleNewAgente = false"
					></Button>

					<Button
						type="submit"
						seerity="primary"
						label="Guardar"
						size="small"
						severity="secondary"
					/>
				</div>
			</Form>
		</div>
	</Dialog>
	<!--new user external-->

</template>

<script setup>

import apiClient from "@/src/constansts/axiosClient";
import { useTransactionStepStore } from '@/Stores/useTransactionStore';
import { useFiltros } from '@/Composables/dashboard/filtros';
import { yupResolver } from "@primevue/forms/resolvers/yup";
import * as yup from "yup";
import { usePage } from "@inertiajs/vue3";
import { ref, computed, onMounted  } from "vue";
import { useToast } from "primevue";
import { Form } from '@primevue/forms';
const toast = useToast();
const page = usePage();
let agentesComisiones = ref([]);
let visible = ref(false);
const transactionId = page.props.transaction_id;


let visibleNewAgente = ref(false);
const formNuevo = ref({
	office_id: "",
	region_id: 120,
	user: {
		first_name: "",
		last_name: "",
		username: "agent" + Math.floor(Math.random() * 90000),
		password: "123456789",
		phone_number: "",
		email: "",
		name_to_show: "",
	},
});

const onFormSubmit = async ({ valid }) => {
	if (valid) {
		formNuevo.value.user.name_to_show =
			formNuevo.value.user.first_name +
			" " +
			formNuevo.value.user.last_name;
		formNuevo.value.agent_status_id = 1;
		const response = await apiClient.post(
			"/agents/agents_external",
			formNuevo.value
		);

		visibleNewAgente.value=false;
		toast.add({
			severity: "success",
			summary: "Agente Externo Creado.",
			life: 3000,
		});
		changeOficina()
		visible.value=true;
	}
};


const resolver = ref(
	yupResolver(
		yup.object().shape({
			nombre: yup.string().required("Campo requerido"),
			apellido: yup.string().required("Campo requerido"),
			email: yup.string().email().required("Campo requerido"),
			phone_number: yup.string().required("Campo requerido"),
		})
	)
);

const tipoComisiones = ref([
	{id : 1 , name : 'Comision'},
	{id : 2 , name : 'Referidos'},
])

const store = useTransactionStepStore();



const totalComision = computed(() => {
  let total = 0;

  const stepData4 = store.getStepData(transactionId, 4);
  const soldPrice = store.getStepData(transactionId, 1)?.sold_price || 0;

  if (!isEmptyObject(stepData4) || (Array.isArray(stepData4) && stepData4.length > 0)) {
    stepData4.forEach((agente) => {
      total += parseFloat(agente.total_commission_amount || 0);
    });
  }

  return total.toFixed(2);
});

const totalPorcentaje = computed(() => {
  let total = 0;

  const stepData4 = store.getStepData(transactionId, 4);
  const soldPrice = store.getStepData(transactionId, 1)?.sold_price || 0;

  if (!isEmptyObject(stepData4) || (Array.isArray(stepData4) && stepData4.length > 0)) {
    stepData4.forEach((agente) => {
      const porcentaje = (agente.total_commission_amount * 100) / soldPrice || 0;
      agente.total_commission_percentage = porcentaje.toFixed(2);
      total += parseFloat(porcentaje);
    });
  }

  return total.toFixed(2);
});

const puedeEditar = computed(() => {
	const transaction_id = transactionId;
  	return store.getStepData(transaction_id,1).side == 'both' ||
		store.getStepData(transaction_id, 1).side == 'listing' ||
		store.getStepData(transaction_id, 1).is_external ||
		(page.props.permissions && page.props.permissions.includes('edit transactions'));
});

function borrarAgenteComision (agente) {

	let stepData = store.getStepData(transactionId, 4);
	const indiceBorrar = store.getStepData(transactionId, 4).findIndex(item => item == agente);
	console.log(stepData.splice(indiceBorrar, 1));
}



function changeOficina () {
	agentesSeleccionados.value = '';
		if(oficinasSeleccionadas.value){
			getAgentes();
		}
}

async function getAgentes() {
	const data = {
		office_id: oficinasSeleccionadas.value
			? [oficinasSeleccionadas.value.code]
			: [],
		transaction_internal_id: transactionId,
	};

	formNuevo.value.office_id = oficinasSeleccionadas.value.office_id;

	const response = await apiClient.post(
		"/dashboard/get-agentes-por-ubicacion",
		data
	);
	agentes.value = response.data.map((agente) => ({
		code: agente.id,
		name: agente.name,
	}));
}

function selectAgente () {
	console.log(agentesSeleccionados.value);
	store.getStepData(transactionId, 4).push({
		agent_name: agentesSeleccionados.value.name,
		agent_id : agentesSeleccionados.value.code,
		commission_id: null,
		can_delete: true,
		total_commission_amount : 0,
		total_commission_percentage: 0,
		commission_type_id : 1,
	});

	console.log(agentesComisiones.value);
}

function isEmptyObject(obj) {
  return obj && Object.keys(obj).length === 0;
}

const {
	oficinas,
	oficinasSeleccionadas,
	agentes,
	agentesSeleccionados,
	oficinasTransaccion,
	getOficinasTransaccion,
	getOficinas,
} = useFiltros();

onMounted(() => {
	//getOficinas();
	getOficinasTransaccion();
});
</script>
