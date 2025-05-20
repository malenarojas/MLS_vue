<template>
	<AppLayout>
	<div class="text-base font-semibold pb-4">
		Trade Record Report - ID {{ dato.trr_report_id }}
	</div>
	<div class="rounded bg-indigo-50 p-4 shadow-md">
		<!-- <Button class="my-4 p-2" @click.prevent="printDiv" size="small" severity="info">
			<i class="pi pi-print"></i>
		</Button> -->
		<div class="grid grid-cols-12 gap-4">
			<div class="col-span-12 md:col-span-6 xl:col-span-6">
				<div class="flex gap-4">
					<img :src="imageUrl" class="h-32"
						alt="" />

					<div class="flex flex-col gap-1 justify-center">
						<h1 class="text-base">
							{{ dato.listing?.location?.number }}
							{{ dato.listing?.location?.first_address }}
							{{ dato.listing?.location?.second_address }}
							{{ dato.listing?.location?.zone?.name }}
							{{ dato.listing?.location?.city?.name }}
							{{ dato.listing?.location?.city?.province?.name }}
						</h1>
						<h2 class="opacity-75">
							Owner: {{ dato.listing?.owners[0]?.last_name }},
							{{ dato.listing?.owners[0]?.name }}
						</h2>
					</div>
				</div>
			</div>

			<div class="col-span-12 md:col-span-6 xl:col-span-6">
				<div class="flex justify-end gap-4">
					<div
						class="flex flex-col gap-1 justify-end items text-end pr-2 text-sm opacity-85 border-r-2 border-gray-400">
						<span v-if="dato.both == 1">
							{{
								"Lado de Captación y Venta"

							}}</span>

						<span v-else>

							{{
								(dato.transaction_type_id == 1)
									? "Lado de Captación"
									: "Lado Comprador(Venta)"
							}}</span>
						<span>ID captación: {{ dato.listing?.is_external==0?dato.listing?.MLSID:'' }}</span>
						<span>{{ dato.listing?.transaction_type.name }}</span>
						<span>{{
							dato.listing?.listing_information?.subtype_property?.name
						}}</span>
					</div>

					<div class="flex flex-col pl-2 justify-center">
						<h1 class="opacity-85 pb-2 font-medium text-sm">Estatus</h1>
						<Select size="small" v-model="dato.transaction_status_id" optionLabel="name" optionValue="id"
							@change="updateStatus(dato.id, dato.transaction_status_id)" :options="estados"
							v-if="permissions.findIndex(permission => permission == 'change status') >= 0
							|| ![1,2,5,6].includes(dato.transaction_status_id)" />

						<h2 v-if="permissions.findIndex(permission => permission == 'change status') == -1
							&& [1,2,5,6].includes(dato.transaction_status_id)">
							{{estados.find(estado => estado.id == dato.transaction_status_id)?.name ?? '-' }}</h2>

						<a :href="`/transactions/create?transaction_id=${dato.internal_id}`" class="mt-2">
							<Button class="inline-block w-full"
							size="small" label="Editar"
							v-if="[3,4].includes(dato.transaction_status_id)|| permissions.findIndex(permission => permission == 'edit transaction') >= 0" />
						</a>

					</div>
				</div>
			</div>
			<!--Detalles de la Transacción -->
			<div class="col-span-12 md:col-span-4 xl:col-span-4">
				<h1 class="opacity-85 pb-2 font-medium text-sm">
					Detalles de la Transacción
				</h1>
				<ul class="bg-white shadow overflow-hidden rounded">
					<li>
						<div class="px-4 py-2 sm:px-6">
							<div class="mt-2 flex items-center justify-between">
								<p class="text-sm font-medium text-gray-800">
									Precio de la Captación:
								</p>
								<p class="font-bold text-gray-800 text-sm">
									{{
										currencyFormat(
											parseFloat(
												dato.listing?.listing_prices[0]?.amount
											).toFixed(2),
											{
												style: "decimal",
												currency: "BOB",
												minimumFractionDigits: 2,
											}
										)
									}}
									Bs.
								</p>
							</div>
						</div>
					</li>

					<li>
						<div class="px-4 py-2 sm:px-6">
							<div class="mt-2 flex items-center justify-between">
								<p class="text-sm font-medium text-gray-800">Sold Price:</p>
								<p class="font-bold text-gray-800 text-sm">
									{{
										currencyFormat(dato.current_listing_price, {
											style: "decimal",
											currency: "BOB",
											minimumFractionDigits: 2,
										})
									}}
									Bs.
								</p>
							</div>
						</div>
					</li>

					<li>
						<div class="px-4 py-2 sm:px-6">
							<div class="mt-2 flex items-center justify-between">
								<p class="text-sm font-medium text-gray-800">
									Fecha de Captación
								</p>
								<p class="font-bold text-gray-800 text-sm">
									{{
										moment(dato.listing?.date_of_listing).format("DD/MM/YYYY")
									}}
								</p>
							</div>
						</div>
					</li>

					<li>
						<div class="px-4 py-2 sm:px-6">
							<div class="mt-2 flex items-center justify-between">
								<p class="text-sm font-medium text-gray-800">Fecha de Cierre</p>
								<p class="font-bold text-gray-800 text-sm">
									{{ moment(dato.sold_date).format("DD/MM/YYYY") }}
								</p>
							</div>
						</div>
					</li>
				</ul>
			</div>
			<!--Detalles de la Transacción -->

			<!--Comisiones -->
			<div class="col-span-12 md:col-span-8 xl:col-span-8">
				<h1 class="opacity-85 pb-2 font-medium text-sm">Comisiones</h1>
				<table class="bg-white shadow overflow-hidden rounded text-sm w-full px-5 text-gray-800"
					style="min-height: 220px">
					<tbody>
						<tr class="px-2">
							<td></td>
							<td class="" style="width: 220px">Cantidad</td>
							<td style="width: 124px">Representation</td>
						</tr>

						<tr>
							<td class="px-2"><strong>Total</strong></td>
							<td>
								<strong>{{
									currencyFormat(parseFloat(sumTotalComission).toFixed(2), {
										style: "decimal",
										currency: "BOB",
										minimumFractionDigits: 2,
									})
								}}
									Bs.</strong>
							</td>
							<td>&nbsp;</td>

							<td>
								<strong>{{
									currencyFormat(
										parseFloat(sumTotalPorcentajeComission).toFixed(2),
										{
											style: "decimal",
											currency: "BOB",
											minimumFractionDigits: 2,
										}
									)
								}}%</strong>
							</td>
						</tr>
						<tr class="border-0 border-t-2">
							<td colspan="4" class="px-2">
								<strong>Agente</strong>
							</td>
						</tr>

						<tr v-for="(item, index) in com1" :key="index">
							<td class="px-2" v-if="item.commission_type_id == 1">
								{{ item.agent?.user.name_to_show }}
							</td>
							<td class="px-2" v-if="item.commission_type_id == 1">
								<strong>
									{{
										currencyFormat(
											parseFloat(item.total_commission_amount).toFixed(2),
											{
												style: "decimal",
												currency: "BOB",
												minimumFractionDigits: 2,
											}
										)
									}}
									Bs.
								</strong>
							</td>
							<td class="px-2" v-if="item.commission_type_id == 1">
								<strong v-if="item.transaction.transaction_type_id == 1"> Lado Captador </strong>
								<strong v-if="item.transaction.transaction_type_id == 2"> Lado Comprador </strong>
							</td>

							<td class="px-2" v-if="item.commission_type_id == 1">
								<strong>{{
									parseFloat(item.total_commission_percentage).toFixed(2)
								}}%</strong>
							</td>
						</tr>


						<tr class="border-0 border-t-2">
							<td colspan="4" class="px-2">
								<strong>Referidos</strong>
							</td>
						</tr>
						<tr v-for="(item, index) in com1" :key="index">
							<td class="px-2" v-if="item.commission_type_id == 2">
								{{ item.agent?.user.name_to_show }}
							</td>
							<td class="px-2" v-if="item.commission_type_id == 2">
								<strong>
									{{
										currencyFormat(
											parseFloat(item.total_commission_amount).toFixed(2),
											{
												style: "decimal",
												currency: "BOB",
												minimumFractionDigits: 2,
											}
										)
									}}
									Bs.
								</strong>
							</td>
							<td class="px-2" v-if="item.commission_type_id == 2">
								<strong v-if="item.transaction.transaction_type_id == 1"> Lado Captador </strong>
								<strong v-if="item.transaction.transaction_type_id == 2"> Lado Comprador </strong>
							</td>


							<td class="px-2" v-if="item.commission_type_id == 2">
								<strong>{{
									parseFloat(item.total_commission_percentage).toFixed(2)
								}}%</strong>
							</td>
						</tr>
						<tr v-for="(item, index) in com2" :key="index">
							<td class="px-2" v-if="item.commission_type_id == 2">
								{{ item.agent?.user.name_to_show }}
							</td>
							<td class="px-2" v-if="item.commission_type_id == 2">
								<strong>
									{{
										currencyFormat(
											parseFloat(item.total_commission_amount).toFixed(2),
											{
												style: "decimal",
												currency: "BOB",
												minimumFractionDigits: 2,
											}
										)
									}}
									Bs.
								</strong>
							</td>
							<td class="px-2" v-if="item.commission_type_id == 2">
								<strong> Lado Comprador(venta) </strong>
							</td>

							<td class="px-2" v-if="item.commission_type_id == 2">
								<strong>{{
									parseFloat(item.total_commission_percentage).toFixed(2)
								}}%</strong>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<!--Comisiones -->

			<!--Pagos-->
			<div class="col-span-12 md:col-span-12 xl:col-span-12 overflow-x-auto">
				<h1 class="opacity-85 pb-2 text-sm">Pagos</h1>
				<table class="w-full order mb-2 text-sm">
					<thead>
						<tr class="bg-indigo-200 text-center text-xs md:text-sm font-semibold">
							<th class="p-0 font-semibold">
								<span class="block py-2 px-3 border-r border-gray-300">Agente</span>
							</th>

							<th class="p-0 font-semibold">
								<span class="block py-2 px-3 border-r border-gray-300">Esperado</span>
							</th>
							<th class="p-0 font-semibold">
								<span class="block py-2 px-3 border-r border-gray-300">Fecha</span>
							</th>
							<th class="p-0 font-semibold">
								<span class="block py-2 px-3 border-r border-gray-300">Recibido</span>
							</th>
							<th class="p-0 font-semibold">
								<span class="block py-2 px-3 border-r border-gray-300">Tipo de Comisión</span>
							</th>
							<th class="p-0 font-semibold">
								<span class="block py-2 px-3 border-r border-gray-300">Pagos recibidos</span>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="(item, index) in pay1" :key="index"
							class="*:py-2 bg-white border text-xs md:text-sm text-center text-gray-800">
							<td>{{ item.agent?.user.name_to_show }}</td>
							<td>
								{{
									currencyFormat(parseFloat(item.amount_expected).toFixed(2), {
										style: "decimal",
										currency: "BOB",
										minimumFractionDigits: 2,
									})
								}}
								Bs.
							</td>
							<td>
								{{ moment(item.expected_payment_date).format("DD/MM/YYYY") }}
							</td>
							<td>
								{{
									currencyFormat(parseFloat(item.amount_received).toFixed(2), {
										style: "decimal",
										currency: "BOB",
										minimumFractionDigits: 2,
									})
								}}
								Bs.
							</td>

							<td class="p-2">
								{{ item.payment_type_id == 1 ? "Comisión" : "Referido" }}
							</td>
							<td class="p-2">
								{{
									item.received_date != null
										? moment(item.received_date).format("DD/MM/YYYY")
										: ""
								}}
							</td>
						</tr>

						<tr v-for="(item, index) in pay2" :key="index"
							class="*:py-2 bg-white border text-xs md:text-sm text-center text-gray-800">
							<td>{{ item.agent?.user.name_to_show }}</td>
							<td>
								{{
									currencyFormat(parseFloat(item.amount_expected).toFixed(2), {
										style: "decimal",
										currency: "BOB",
										minimumFractionDigits: 2,
									})
								}}
								Bs.
							</td>
							<td>
								{{ moment(item.expected_payment_date).format("DD/MM/YYYY") }}
							</td>
							<td>
								{{
									currencyFormat(parseFloat(item.amount_received).toFixed(2), {
										style: "decimal",
										currency: "BOB",
										minimumFractionDigits: 2,
									})
								}}
								Bs.
							</td>

							<td class="p-2">
								{{ item.payment_type_id == 1 ? "Comisión" : "Referido" }}
							</td>
							<td class="p-2">
								{{
									item.received_date != null
										? moment(item.received_date).format("DD/MM/YYYY")
										: ""
								}}
							</td>
						</tr>

						<tr class="bg-white border text-xs md:text-sm text-center text-gray-800">
							<td class="p-2 font-semibold">Total</td>

							<td class="p-2 font-semibold">
								{{
									currencyFormat(parseFloat(sumTotalPayment).toFixed(2), {
										style: "decimal",
										currency: "BOB",
										minimumFractionDigits: 2,
									})
								}}
								Bs.
							</td>

							<td class="p-2"></td>
							<td class="p-2 font-semibold">
								{{
									currencyFormat(
										parseFloat(sumTotalAmountReceived).toFixed(2),
										{
											style: "decimal",
											currency: "BOB",
											minimumFractionDigits: 2,
										}
									)
								}}
								Bs.
							</td>

							<td class="p-2"></td>
							<td class="p-2"></td>
						</tr>
					</tbody>
				</table>
			</div>

			<!--Compradores -->
			<div class="col-span-12 md:col-span-4 xl:col-span-4">
				<h1 class="opacity-85 pb-2 font-medium text-sm">Compradores</h1>
				<ul v-if="dato.both==1" class="bg-white shadow overflow-hidden rounded">
					<li v-for="(item, index) in dato.listing?.buyers">
						<div class="px-4 py-2 sm:px-6">
							<div class="mt-2 flex items-center justify-between">
								<p class="font-semibold text-gray-800 text-sm">
									{{ item.name }}
								</p>
							</div>
						</div>
						<div class="px-4 pb-2 sm:px-6">
							<div class="mt-0 flex items-center justify-between">
								<p class="text-sm font-medium text-gray-800">
									{{ item.mobile }}
								</p>
							</div>
						</div>
					</li>
				</ul>

				<ul v-else class="bg-white shadow overflow-hidden rounded">
					<li v-for="(item, index) in dato.listing?.buyers">
                        <div  v-if="dato.transaction_type_id==2" >

                            <div class="px-4 py-2 sm:px-6">
                                <div class="mt-2 flex items-center justify-between">
                                    <p class="font-semibold text-gray-800 text-sm">
                                        {{ item.name }}
                                    </p>
                                </div>
                            </div>
                            <div class="px-4 pb-2 sm:px-6">
                                <div class="mt-0 flex items-center justify-between">
                                    <p class="text-sm font-medium text-gray-800">
                                        {{ item.mobile }}
                                    </p>
                                </div>
                            </div>
                        </div>
					</li>
				</ul>
			</div>
			<!--Compradores -->

			<!--Financiación -->
			<div class="col-span-12 md:col-span-8 xl:col-span-8">
				<h1 class="opacity-85 pb-2 font-medium text-sm">Financiación</h1>
				<ul class="bg-white shadow overflow-hidden rounded">
					<li>
						<div class="px-4 py-2 sm:px-6">
							<div class="mt-2 flex items-center justify-between">
								<p class="text-sm font-medium text-gray-800">
									Banco - {{ dato.bank?.name }}
								</p>
							</div>
						</div>

						<div class="px-4 pb-2 sm:px-6">
							<div class="mt-0 flex items-center justify-between">
								<p class="text-sm font-medium text-gray-800">
									Valor Financiado:
									{{
										dato.amount_financed !== null
											? parseFloat(dato.amount_financed).toFixed(2)
											: "-"
									}}
									Bs.
								</p>
							</div>
						</div>
					</li>
				</ul>
			</div>

			<!--Financiación -->
		</div>
	</div>
	</AppLayout>
</template>
<script setup>
// Importaciones
import { onMounted, ref } from "vue";
import moment from "moment";
import printJS from "print-js";
import { useRoute, useRouter } from "vue-router";
import { useNumberHelpers } from "@/Composables/useNumberHelpers";
import AppLayout from "@/Layouts/AppLayout.vue";
// import { useAuthStore } from "@/stores/useAuthStore";
import { usePage, Link } from "@inertiajs/vue3";
import apiClient from "@/src/constansts/axiosClient";
import { useToast } from "primevue";

const { currencyFormat } = useNumberHelpers();
// Variables reactivas (ref, reactive)
const route = useRoute();
const router = useRouter();
const dato = ref([]);
const com1 = ref([]);
const com2 = ref([]);
const pay1 = ref([]);
const pay2 = ref([]);
const estados = ref([]);
const sumTotalComission = ref(0);
const sumTotalPayment = ref(0);
const sumTotalAmountReceived = ref(0);
const sumTotalPorcentajeComission = ref(0);
const rutaPdf = ref();
const page = usePage();
const imageUrl = ref("");

const toast = useToast();

// Propiedades computadas

// Métodos

const datos = async () => {
	try {
		const response = await apiClient.get(
			"/transactions/get-transaction/" + page.props.transaction_id
		);
		const st = await apiClient.get("/transactions/get-transaction-statuses");

		dato.value = response.data.tr[0];
		com1.value = response.data.com1;
		com2.value = response.data.com2;
		pay1.value = response.data.pay1;
		pay2.value = response.data.pay2;
		imageUrl.value = response.data.image_url;
		estados.value = st.data;

		console.log(imageUrl.value);

		sumCommission();
		sumCommisionPorcentaje();
		sumPaymentExpected();
		sumPaymentReceived();
	} catch (error) {
		throw error.response.data;
	}
};

const sumCommission = () => {
	var comm1 = com1.value.reduce((accumulator, currentValue) => {
		return accumulator + parseFloat(currentValue["total_commission_amount"]);
	}, 0);
	var comm2 = com2.value.reduce((accumulator, currentValue) => {
		return accumulator + parseFloat(currentValue["total_commission_amount"]);
	}, 0);

	sumTotalComission.value = comm1 + comm2;
};
const sumCommisionPorcentaje = () => {
	var pc1 = com1.value.reduce((accumulator, currentValue) => {
		return (
			accumulator + parseFloat(currentValue["total_commission_percentage"])
		);
	}, 0);
	var pc2 = com2.value.reduce((accumulator, currentValue) => {
		return (
			accumulator + parseFloat(currentValue["total_commission_percentage"])
		);
	}, 0);
	sumTotalPorcentajeComission.value = pc1 + pc2;
};
const sumPaymentExpected = () => {
	var p1 = pay1.value.reduce((accumulator, currentValue) => {
		return accumulator + parseFloat(currentValue["amount_expected"]);
	}, 0);
	var p2 = pay2.value.reduce((accumulator, currentValue) => {
		return accumulator + parseFloat(currentValue["amount_expected"]);
	}, 0);
	sumTotalPayment.value = p1 + p2;
};
const sumPaymentReceived = () => {
	var pp1 = pay1.value.reduce((accumulator, currentValue) => {
		return accumulator + parseFloat(currentValue["amount_received"]);
	}, 0);
	var pp2 = pay2.value.reduce((accumulator, currentValue) => {
		return accumulator + parseFloat(currentValue["amount_received"]);
	}, 0);
	sumTotalAmountReceived.value = pp1 + pp2;
};

const printDiv = () => {
	printJS({
		printable:
			apiClient.defaults.baseURL +
			"/transactions/get-transaction/" +
			route.params.id +
			"/pdfdown",
		type: "pdf",
		showModal: true,
		docBaseUrl: apiClient.defaults.baseURL,
	});
};

const updateStatus = async (id, status) => {
	await apiClient.get(
		"/transactions/update-status/" + id + "/" + status
	).then(response => {
		if(response.status == 200){
			toast.add({
				severity: "success",
				summary: "Success",
				detail: "Estatus actualizado correctamente",
				life: 3000,
			});
		}
		else{
			toast.add({
				severity: "warn",
				summary: "Advertencia",
				detail: response.data.message,
				life: 3000,
			});
		}
	}).catch(error => {
		toast.add({
			severity: "error",
			summary: "Error",
			detail: "Error al actualizar el estatus",
			life: 3000,
		});

		console.error(error);
	});
	
};

const permissions = page.props.permissions || [];

// Watchers

// Ganchos de ciclo de vida
onMounted(() => {
	datos();
});

// Composables (lógica reutilizable)
</script>
