c<template>
	<div class="rounded bg-indigo-50 p-4 shadow-md">
		<h1 class="opacity-85 font-semibold text-lg">Pagos</h1>
		<p class="pb-2">
			(Ingresar fecha y monto recibido. Ingresar monto recibido debajo de
			Pago Agente)
		</p>

		<!-- Classes Table -->
		<div class="relative overflow-auto">
			<div class="overflow-x-auto">
				<table class="w-full order mb-2">
					<thead>
						<tr
							class="bg-indigo-100 text-center text-xs md:text-sm font-thin"
						>
							<th class="p-0">
								<span
									class="block py-2 px-3 border-r border-gray-300"
									>Agente</span
								>
							</th>
							<th class="p-0">
								<span
									class="block py-2 px-3 border-r border-gray-300"
									>Tipo de Comisión</span
								>
							</th>
							<th class="p-0">
								<span
									class="block py-2 px-3 border-r border-gray-300"
									>Esperado</span
								>
							</th>
							<th class="p-0">
								<span
									class="block py-2 px-3 border-r border-gray-300"
									>Fecha</span
								>
							</th>
							<th class="p-0">
								<span
									class="block py-2 px-3 border-r border-gray-300"
									>Recibido</span
								>
							</th>
							<th class="p-0">
								<span
									class="block py-2 px-3 border-r border-gray-300"
									>Pagos recibidos</span
								>
							</th>
							<th class="p-0">
								<span
									class="block py-2 px-3 border-r border-gray-300"
								></span>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr
							v-for="(payment, index) in store.getStepData(transactionId, 3)"
							:key="index"
							class="*:py-2 bg-white border text-xs md:text-sm text-center text-gray-800"
						>
							<td>{{ payment.agent_name }}</td>
							<td>
								<Select
									size="small"
									:disabled="payment.received"
									v-model="payment.type_commission"
									optionLabel="name"
									optionValue="code"
									:options="tipoComisiones"
								/>
							</td>

							<td>
								<InputNumber
									size="small"
									inputId="locale-user"
									:minFractionDigits="0" 
									:maxFractionDigits="2"
									v-model="payment.amount_expected"
									:disabled="payment.received"
									min="0"
								>
								</InputNumber>
							</td>
							<td class="p-2">
								<DatePicker
									v-model="payment.expected_payment_date"
									size="small"
									class="w-[125px]"
									showIcon
									iconDisplay="input"
									@value-change="corregirFechaEsperada(payment)"
									:disabled="payment.received"
								/>
							</td>
							<td class="w-[90px]">
								<InputNumber
									size="small"
									inputId="locale-user"
									:minFractionDigits="0" 
									:maxFractionDigits="2"
									:disabled="payment.received"
									v-model="payment.amount_received"
									min="0"
								>
								</InputNumber>
							</td>
							<td class="p-2">
								<div class="flex">
									<DatePicker
										v-if="payment.received"
										type="text"
										size="small"
										class="w-[95px]"
										@value-change="corregirFechaRecibida(payment)"
										v-model="payment.received_date"
									/>
									<Button
										v-if="!payment.received"
										label="Recibido"
										@click="payment.received = true; eventRecibido(payment);"
										size="small"
										class="w-full"
										severity="success"
										variant="raised"
									/>
									<Button
										v-if="payment.received"
										label="Reverse"
										size="small"
										class="ml-3"
										@click="payment.received = false; eventReverse(payment)"
										severity="success"
										variant="outlined"
									></Button>
								</div>
							</td>

							<td class="p-2">
								<button
									class="rounded-full text-white bg-gradient-to-t from-emerald-700 to-emerald-600 shadow-md py-1.5 px-2"
									@click="borrarAgenteComision(payment)"
								>
									<i class="pi pi-trash" />
								</button>
							</td>
						</tr>

						<tr
							class="bg-white border text-xs md:text-sm text-center text-gray-800"
						>
							<td class="p-2 font-bold" colspan="2">Total</td>

							<td class="p-2">{{ Intl.NumberFormat('de-DE').format(totalEsperado) }}</td>
							<td class="p-2"></td>
							<td class="p-2">{{ Intl.NumberFormat('de-DE').format(totalRecibido) }}</td>

							<td class="p-2"></td>
							<td class="p-2"></td>
						</tr>
						<tr
							class="bg-white border text-xs md:text-sm text-center text-gray-800"
						>
							<td class="p-2 font-bold" colspan="2">Pendiente</td>

							<td class="p-2">{{ Intl.NumberFormat('de-DE').format(totalPendiente) }}</td>
							<td class="p-2"></td>
							<td class="p-2">{{ Intl.NumberFormat('de-DE').format(totalRecibidoPendiente) }}</td>

							<td class="p-2"></td>
							<td class="p-2"></td>
						</tr>
					</tbody>
				</table>
				<div class="flex justify-end">
						<Button
						size="small"
						label="Agregar Pago"
						@click="visible = true"
					/>

				</div>
			</div>
		</div>
	</div>
	<Dialog
		v-model:visible="visible"
		modal
		header="Añadir Pago"
		:style="{ width: '50rem' }"
	>


		<div class="mb-8 flex gap-2 flex-col">
			<label for="text-sm ">Agente</label>
			<Select
				size="small"
				v-model="agenteSeleccionado"
				optionLabel="name"
				optionValue="code"
				filter
				:options="agentesEnComisiones"
				placeholder="Seleccionar"
			>
			</Select>
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
				:disabled="!agenteSeleccionado"
				@click="
					visible = false;
					selectAgente();
				"
			></Button>
		</div>
	</Dialog>
</template>
<script setup>
// Importaciones
import { computed, ref } from "vue";
import moment from "moment";
import { useTransactionStepStore } from '@/stores/useTransactionStore';
import { useRoute } from "vue-router";
import { usePage } from "@inertiajs/vue3";

const transactionId = usePage().props.transaction_id

// Variables reactivas (ref, reactive)

const hoy = moment().format("DD/MM/YYYY");
const route = useRoute();

const tipoComisiones = ref([
	{ code: 1, name: "Comision" },
	{ code: 2, name: "Referidos" },
]);
let visible = ref(false);
const store = useTransactionStepStore();
let agenteSeleccionado = ref([]);

// Propiedades computadas

const totalRecibido = computed(() => {
  let total = 0;
  const stepData3 = store.getStepData(transactionId, 3);
	
  if (!isEmptyObject(stepData3)) {
		const stepDataArray = Array.isArray(stepData3) ? stepData3 : Object.values(stepData3);
		stepDataArray.forEach((payment) => {
			total += parseFloat(payment.amount_received || 0);
		});
	}

  if (total > -0.01 && total < 0.01) {
		
		return "0.00";
	}

	return total.toFixed(2);
});

const totalEsperado = computed(() => {
  let total = 0;
  const stepData3 = store.getStepData(transactionId, 3);

	if (!isEmptyObject(stepData3)) {
		const stepDataArray = Array.isArray(stepData3) ? stepData3 : Object.values(stepData3);
		stepDataArray.forEach((payment) => {
			total += parseFloat(payment.amount_expected || 0);
		});
	}
	
  if (total > -0.01 && total < 0.01) {
		
		return "0.00";
	}

	return total.toFixed(2);
});

const agentesEnComisiones = computed(() => {
	const comisiones = store.getStepData(transactionId, 2);
	const agentes = [];

	comisiones.forEach(comision => {
		if(agentes.findIndex(item => item.code == comision.agent_id) == -1) {
			agentes.push(
				{
					code : comision.agent_id,
					name: comision.agent_name
				}
			)
		}
	});

	return agentes;
})

const totalPendiente = computed(() => {
  const comisiones = store.getStepData(transactionId, 2);
  let total = 0;

  if (!isEmptyObject(comisiones) || (Array.isArray(comisiones) && comisiones.length > 0)) {
    comisiones.forEach((comision) => {
      if (comision) {
        total += parseFloat(comision.total_commission_amount || 0);
      }
    });
    total -= parseFloat(totalEsperado.value || 0);
  }

	if (total > -0.01 && total < 0.01) {
		
		return "0.00";
	}
	return total.toFixed(2);
});

const totalRecibidoPendiente = computed(() => {
	const tr = parseFloat(totalRecibido.value).toFixed(2);
	const total = parseFloat(totalEsperado.value) - tr;
	
	if (total > -0.01 && total < 0.01) {
		
		return "0.00";
	}
	return total.toFixed(2);
}) ;

// Métodos



const borrarAgenteComision = (payment) => {
	
	store.saveStepData(transactionId,3,store.getStepData(transactionId, 3).filter((item) => item != payment));
};

const eventRecibido = (payment) => {
	payment.received_date = hoy; 
	console.log(payment);

};

const eventReverse = (payment) => {
	payment.received_date = null;

};



const selectAgente = () => {
	
	store.getStepData(transactionId, 3).push({
		agent_name: agentesEnComisiones.value.find(item => item.code == agenteSeleccionado.value).name,
		agent_id: agentesEnComisiones.value.find(item => item.code == agenteSeleccionado.value).code,
		type_commission: 1,
		payment_id : null,
		amount_expected: 0,
		amount_received: 0,
		expected_payment_date: hoy,
		received_date: null,
		received : false
	});

};

function corregirFechaEsperada(payment) {
    const formattedDate = moment(payment.expected_payment_date).format('DD/MM/YYYY');
    payment.expected_payment_date = formattedDate;
}

function corregirFechaRecibida(payment) {
    const formattedDate = moment(payment.received_date).format('DD/MM/YYYY');
    payment.received_date = formattedDate;
}


function isEmptyObject(obj) {
  return obj && Object.keys(obj).length === 0;
}

// Watchers

// Ganchos de ciclo de vida

// Composables (lógica reutilizable)

</script>
