<template>
	<div class="rounded bg-indigo-50 p-4 shadow-md">
		<h1 class="opacity-85 font-semibold text-lg">Financiación</h1>
		<p class="opacity-85 text-sm mb-4">
			Por favor, indique un banco y el monto que puede haber sido financiado
			para esta transacción. Proporcionar estos detalles ayudará a RE/MAX a
			comprender los niveles de negocios generados para cada Banco.
		</p>

		<table class="w-50 bg-white mt-4 opacity-75 text-sm">
			<thead class="table-auto border">
				<tr>
					<th class="pb-1">Banco</th>
					<th class="pb-1">Valor Financiado</th>
				</tr>
			</thead>
			<tbody class="text-start">
				<tr class="*:p-2 *:border">
					<td>
						<Select
							filter
							size="small"
							label="Banco"
							v-model="bancoSeleccionado"
							optionLabel="name"
							optionValue="code"
							class="w-72"
							:options="bancos"
						/>
					</td>
					<td>
						<InputNumber
							size="small"
							v-model="monto"
							class="w-48"
							:minFractionDigits="2"
						>
						</InputNumber>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</template>
<script setup>
import apiClient from "@/src/constansts/axiosClient";
import { useTransactionStepStore } from "@/stores/useTransactionStore";
import { usePage } from "@inertiajs/vue3";
import { ref, computed, onMounted  } from "vue";
import { useToast } from "primevue";

const toast = useToast();
const page = usePage();
const transactionId = page.props.transaction_id;

const store = useTransactionStepStore();


let bancoSeleccionado = computed({
	get() {
		return store.getStepData(transactionId, 7).bank_id;
	},
	set(value) {
		store.getStepData(transactionId, 7).bank_id = value;
	},
});
let monto = computed({
	get() {
		return store.getStepData(transactionId, 7).amount_financed;
	},
	set(value) {
		store.getStepData(transactionId, 7).amount_financed = value;
	},
});

let bancos = ref([]);

async function fetchBancos() {
	const response = await apiClient.get("/banks/get-all");
	if (response.status == 200) {
		bancos.value = response.data.map((banco) => {
			return {
				code: banco["id"],
				name: banco["name"],
			};
		});
	}
}

onMounted(async () => {
	console.log("adsadsa");
	await fetchBancos();
});
</script>
