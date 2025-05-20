<template>
	<AppLayout>
	<div class="p-2">
		<h1 class="font-bold text-2xl opacity-75">
			<i class="pi pi-file-check"></i> Reporte de pagos de transacciones
		</h1>

		<div class="mt-4 flex justify-end  gap-4 bg-indigo-50 border-indigo-200 border p-2">

			<Select optionValue="id" optionLabel="name" v-model="inDollars" :options="[
				{ 'name': 'Bs.', id: 0 },
				{ 'name': 'USD.', id: 1 }
			]" placeholder="Moneda" />

			<Select v-model="selectedOffice" optionLabel="name" filter :options="offices" placeholder="Oficina"
				:maxSelectedLabels="3" class="mb-auto"
				v-if="['Administrador', 'Super Administrador'].includes(role)" 
				/>
			<Select v-model="month" :aria-label="Mes" placeholder="Mes" option-label="name" :options="[
				{ code: 1, name: 'Enero' },
				{ code: 2, name: 'Febrero' },
				{ code: 3, name: 'Marzo' },
				{ code: 4, name: 'Abril' },
				{ code: 5, name: 'Mayo' },
				{ code: 6, name: 'Junio' },
				{ code: 7, name: 'Julio' },
				{ code: 8, name: 'Agosto' },
				{ code: 9, name: 'Septiembre' },
				{ code: 10, name: 'Octubre' },
				{ code: 11, name: 'Noviembre' },
				{ code: 12, name: 'Diciembre' }
			]" />
			<Select v-model="year" :aria-label="Año" option-label="name" :options="[
				{ code: 2025, name: '2025' },
				{ code: 2024, name: '2024' },
				{ code: 2023, name: '2023' },
				{ code: 2022, name: '2022' }
			]" placeholder="Año" />

			<SplitButton label="Generar" icon="pi pi-check" :model="buttonItems" @click="fetchTransactions()">
			</SplitButton>

		</div>

		<div class="flex justify-end gap-2 mt-2">
			<label for="expanded" class="font-semibold text-sm opacity-75">Reporte Expandido</label>
			<input type="checkbox" name="expanded" id="expanded" v-model="expanded">
		</div>

		<div class="overflow-x-auto mt-4">
			<table class="table-auto w-full">
				<thead class="text-sm">
					<tr class="*:border *:p-1">
						<th></th>
						<th class="row-span-6" colspan="6">Transacciones Residenciales</th>
						<th class="row-span-6" colspan="6">Transacciones Comerciales</th>
					</tr>
					<tr class="*:border	*:p-1 text-xs">
						<th>Nombre de asociado</th>
						<th>Captaciones Finalizadas</th>
						<th>Venta Finalizadas</th>
						<th>Volumenes Captacion</th>
						<th>Volumenes Venta</th>
						<th>Comisiones</th>
						<th>Comisiones a la fecha</th>
						<th>Captaciones Finalizadas</th>
						<th>Venta Finalizadas</th>
						<th>Volumenes Captacion</th>
						<th>Volumenes Venta</th>
						<th>Comisiones</th>
						<th>Comisiones a la fecha</th>

					</tr>
				</thead>
				<tbody class="text-xs text-center">
					<template v-for="transaction in agentActiveTransactions" :key="transaction.id">
						<tr class="border-t border-x text-start *:p-1">

							<td class="w-24">{{ transaction.agent_name }}</td>
							<td>{{ transaction.transactions_listing_res }}</td>
							<td>{{ transaction.transactions_selling_res }}</td>
							<td>{{ Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(transaction.transactions_listing_res_volume) }}
							</td>
							<td>{{ Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(transaction.transactions_selling_res_volume) }}
							</td>
							<td>{{ Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(transaction.amount_payment_res) }}</td>
							<td>{{ Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(transaction.payments_in_year_res) }}</td>
							<td class="border-l">{{ transaction.transactions_listing_com }}</td>
							<td>{{ transaction.transactions_selling_com }}</td>
							<td>{{ Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(transaction.transactions_listing_com_volume) }}
							</td>
							<td>{{ Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(transaction.transactions_selling_com_volume) }}
							</td>
							<td>{{ Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(transaction.amount_payment_com) }}</td>
							<td>{{ Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(transaction.payments_in_year_com) }}</td>
						</tr>

						<tr
							v-if="((Array.isArray(transaction.payments_res) && transaction.payments_res.length > 0) || 
							(Array.isArray(transaction.payments_com) && transaction.payments_com.length > 0) || 
							(typeof transaction.payments_res === 'object' && Object.keys(transaction.payments_res).length > 0) || 
							(typeof transaction.payments_com === 'object' && Object.keys(transaction.payments_com).length > 0)) && expanded">
							
							<td colspan="13" class="p-0">
								<table class="w-full border-x border-b">
									<thead>
										<tr class="*:text-start">
											<th></th>
											<th>TRR ID</th>
											<th>MLS</th>
											<th>Precio</th>
											<th>Fecha vendida</th>
											<th>Tipo</th>
											<th>Agente principal TRR</th>
											<th>Tipo C/R</th>
											<th>Cant. Recibida</th>
											<th>Dia de pago</th>
											<th>Fecha recepción</th>
											<th>Porcentaje</th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="payment in transaction.payments_res" :key="payment.id"
											class="*:border-b *:text-start">
											<td class="w-24 border-b-transparent"></td>
											<td>{{ payment.trr_report_id }}</td>
											<td>{{ payment.mls_id }}</td>
											<td class="!text-end px-2">{{ Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(payment.current_listing_price) }}
											</td>
											<td>{{ payment.sold_date }}</td>
											<td>{{ payment.payment_type_id == 2 && payment.amount_received < 0 ? 'Referido' : (payment.transaction_type_id == 1 ? 'L' : 'S') }}</td>
											<td>{{ payment.transaction_agent_id == payment.agent_id ? 'YES' : 'NO' }}
											</td>
											<td>RES</td>
											
											<td class="!text-end px-2">{{ payment.amount_received > 0 ? Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(payment.amount_received) : 
												'('+Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(payment.amount_received * -1 )+')' }}</td>

											<td>{{ payment.expected_payment_date }}</td>
											<td>{{ payment.received_date }}</td>
											<td>{{ payment.commission_percentage?parseFloat(payment.commission_percentage).toFixed(1) + "%": '-' }}</td>
										</tr>
										<tr v-for="payment in transaction.payments_com" :key="payment.id" class="*:border-b *:text-start">
											<td class="w-24 border-b-transparent"></td>
											<td>{{ payment.trr_report_id }}</td>
											<td>{{ payment.mls_id }}</td>
											<td class="!text-end px-2">{{ Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(payment.current_listing_price) }}
											</td>
											<td>{{ payment.sold_date }}</td>
											<td>{{ payment.payment_type_id == 2 && payment.amount_received < 0 ? 'Referido' : (payment.transaction_type_id == 1 ? 'L' : 'S') }}</td>
											<td>{{ payment.transaction_agent_id == payment.agent_id ? 'YES' : 'NO' }}
											</td>
											<td>COM</td>

											<td class="!text-end px-2">{{ payment.amount_received > 0 ? Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(payment.amount_received) : 
												'('+Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(payment.amount_received * -1 )+')' }}</td>

											<td>{{ payment.expected_payment_date }}</td>
											<td>{{ payment.received_date }}</td>
											<td>{{ payment.commission_percentage?parseFloat(payment.commission_percentage).toFixed(1) + "%": '-' }}</td>
										</tr>
									</tbody>
									<tfoot>

										<tr>
											<td colspan="9" class="text-right font-bold">Total: {{
												Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(parseFloat(transaction.amount_payment_res)
												+ parseFloat(transaction.amount_payment_com)) }}
											</td>
										</tr>
									</tfoot>
								</table>
							</td>
						</tr>

					</template>
					<tr class="font-bold border">
						<td class="font-bold w-24">Total agentes activos:</td>

						<td>
							{{ totalsActive.transactions_listing_res }}
						</td>
						<td>
							{{ totalsActive.transactions_selling_res }}
						</td>
						<td>
							{{
							Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(parseFloat(totalsActive.transactions_listing_res_volume))
							}}
						</td>
						<td>
							{{
							Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(parseFloat(totalsActive.transactions_selling_res_volume))
							}}
						</td>
						<td>
							{{ Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(parseFloat(totalsActive.amount_payment_res)) }}
						</td>
						<td>
							{{ Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(parseFloat(totalsActive.payments_in_year_res)) }}
						</td>
						<td>
							{{ totalsActive.transactions_listing_com }}
						</td>
						<td>
							{{ totalsActive.transactions_selling_com }}
						</td>
						<td>
							{{
							Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(parseFloat(totalsActive.transactions_listing_com_volume))
							}}
						</td>
						<td>
							{{
							Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(parseFloat(totalsActive.transactions_selling_com_volume))
							}}
						</td>
						<td>
							{{ Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(parseFloat(totalsActive.amount_payment_com)) }}
						</td>
						<td>
							{{ Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(parseFloat(totalsActive.payments_in_year_com)) }}
						</td>


					</tr>
					<template v-for="transaction in agentNoActiveTransactions" :key="transaction.id">
						<tr class="border-t border-x">

							<td class="w-24">{{ transaction.agent_name }}</td>
							<td>{{ transaction.transactions_listing_res }}</td>
							<td>{{ transaction.transactions_selling_res }}</td>
							<td>{{ Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(transaction.transactions_listing_res_volume) }}
							</td>
							<td>{{ Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(transaction.transactions_selling_res_volume) }}
							</td>
							<td>{{ Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(transaction.amount_payment_res) }}</td>
							<td>{{ Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(transaction.payments_in_year_res) }}</td>
							<td class="border-l">{{ transaction.transactions_listing_com }}</td>
							<td>{{ transaction.transactions_selling_com }}</td>
							<td>{{ Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(transaction.transactions_listing_com_volume) }}
							</td>
							<td>{{ Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(transaction.transactions_selling_com_volume) }}
							</td>
							<td>{{ Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(transaction.amount_payment_com) }}</td>
							<td>{{ Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(transaction.payments_in_year_com) }}</td>
						</tr>

						<tr
							v-if="(transaction.payments_res.length > 0 || transaction.payments_com.length > 0) && expanded">
							<td colspan="13" class="p-0">
								<table class="w-full border-x border-b">
									<thead>
										<tr>
											<th></th>
											<th>TRR ID</th>
											<th>MLS</th>
											<th>Precio</th>
											<th>Fecha vendida</th>
											<th>Tipo</th>
											<th>Agente principal TRR</th>
											<th>Tipo C/R</th>
											<th>Cant. Recibida</th>
											<th>Dia de pago</th>
											<th>Fecha recepción</th>
											<th>Porcentaje</th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="payment in transaction.payments_res" :key="payment.id"
											class="*:border-b">
											<td class="w-24 border-b-transparent"></td>
											<td>{{ payment.trr_report_id }}</td>
											<td>{{ payment.mls_id }}</td>
											<td>{{ Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(payment.current_listing_price) }}
											</td>
											<td>{{ payment.sold_date }}</td>
											<td>{{ payment.payment_type_id == 2 && payment.amount_received < 0 ? 'Referido' : (payment.transaction_type_id == 1 ? 'L' : 'S') }}</td>
											<td>{{ payment.transaction_agent_id == payment.agent_id ? 'YES' : 'NO' }}
											</td>
											<td>RES</td>
											<td>{{ Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(payment.amount_received) }}</td>
											<td>{{ payment.expected_payment_date }}</td>
											<td>{{ payment.received_date }}</td>
											<td>{{ payment.commission_percentage?parseFloat(payment.commission_percentage).toFixed(1) + "%": '-' }}</td>
										</tr>
										<tr v-for="payment in transaction.payments_com" :key="payment.id">
											<td class="w-24 border-b-transparent"></td>
											<td>{{ payment.trr_report_id }}</td>
											<td>{{ payment.mls_id }}</td>
											<td>{{ Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(payment.current_listing_price) }}
											</td>
											<td>{{ payment.sold_date }}</td>
											<td>{{ payment.payment_type_id == 2 && payment.amount_received < 0 ? 'Referido' : (payment.transaction_type_id == 1 ? 'L' : 'S') }}</td>
											<td>{{ payment.transaction_agent_id == payment.agent_id ? 'YES' : 'NO' }}
											</td>
											<td>COM</td>
											<td>{{ Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(payment.amount_received) }}</td>
											<td>{{ payment.expected_payment_date }}</td>
											<td>{{ payment.received_date }}</td>
											<td>{{ payment.commission_percentage ? parseFloat(payment.commission_percentage).toFixed(1) + "%" : '-' }}</td>
										</tr>
									</tbody>
									<tfoot>
										<tr>
											<td colspan="9" class="text-right font-bold">Total: {{
												Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(transaction.amount_payment_res +
												transaction.amount_payment_com) }}
											</td>
										</tr>
									</tfoot>
								</table>
							</td>
						</tr>

					</template>
					<tr class="font-bold border">
						<td class="font-bold w-24">Total agentes inactivos:</td>

						<td>
							{{ totalsInActive.transactions_listing_res }}
						</td>
						<td>
							{{ totalsInActive.transactions_selling_res }}
						</td>
						<td>
							{{
							Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(parseFloat(totalsInActive.transactions_listing_res_volume))
							}}
						</td>
						<td>
							{{
							Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(parseFloat(totalsInActive.transactions_selling_res_volume))
							}}
						</td>
						<td>
							{{ Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(parseFloat(totalsInActive.amount_payment_res)) }}
						</td>
						<td>
							{{ Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(parseFloat(totalsInActive.payments_in_year_res)) }}
						</td>
						<td>
							{{ totalsInActive.transactions_listing_com }}
						</td>
						<td>
							{{ totalsInActive.transactions_selling_com }}
						</td>
						<td>
							{{
							Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(parseFloat(totalsInActive.transactions_listing_com_volume))
							}}
						</td>
						<td>
							{{
							Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(parseFloat(totalsInActive.transactions_selling_com_volume))
							}}
						</td>
						<td>
							{{ Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(parseFloat(totalsInActive.amount_payment_com)) }}
						</td>
						<td>
							{{ Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(parseFloat(totalsInActive.payments_in_year_com)) }}
						</td>


					</tr>
				</tbody>
				<tfoot class="text-center font-bold text-xs">
					<tr>
						<td>Total oficina</td>
						<td>{{ totalsActive.transactions_listing_res + totalsInActive.transactions_listing_res }}</td>
						<td>{{ totalsActive.transactions_selling_res + totalsInActive.transactions_selling_res }}</td>
						<td>{{
							Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(parseFloat(totalsActive.transactions_listing_res_volume) +
							parseFloat(totalsInActive.transactions_listing_res_volume)) }}</td>
						<td>{{
							Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(parseFloat(totalsActive.transactions_selling_res_volume) +
							parseFloat(totalsInActive.transactions_selling_res_volume)) }}</td>
						<td>{{ Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(parseFloat(totalsActive.amount_payment_res) +
							parseFloat(totalsInActive.amount_payment_res)) }}</td>
						<td>{{ Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(parseFloat(totalsActive.payments_in_year_res) +
							parseFloat(totalsInActive.payments_in_year_res)) }}</td>
						<td>{{ totalsActive.transactions_listing_com + totalsInActive.transactions_listing_com }}</td>
						<td>{{ totalsActive.transactions_selling_com + totalsInActive.transactions_selling_com }}</td>
						<td>{{
							Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(parseFloat(totalsActive.transactions_listing_com_volume) +
							parseFloat(totalsInActive.transactions_listing_com_volume)) }}</td>
						<td>{{
							Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(parseFloat(totalsActive.transactions_selling_com_volume) +
							parseFloat(totalsInActive.transactions_selling_com_volume)) }}</td>
						<td>{{ Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(parseFloat(totalsActive.amount_payment_com) +
							parseFloat(totalsInActive.amount_payment_com)) }}</td>
						<td>{{ Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(parseFloat(totalsActive.payments_in_year_com) +
							parseFloat(totalsInActive.payments_in_year_com)) }}</td>
					</tr>
				</tfoot>

			</table>
		</div>
	</div>
	<Toast />
	</AppLayout>
</template>

<script setup>
	import apiClient from "@/src/constansts/axiosClient";
	import { onMounted, ref, computed } from "vue";
	import AppLayout from "@/Layouts/AppLayout.vue";
	import { usePage } from "@inertiajs/vue3";
	import { useToast } from "primevue";
	import { useIsLoadingStore } from "@/Stores/isLoadingCharts";

	const toast = useToast();
	let startDate = ref();
	let endDate = ref();
	let offices = ref([]);
	let selectedOffice = ref(null);
	let transactions = ref({});
	let expanded = ref(false);
	let month = ref();
	let year = ref();
	const page = usePage();
	const role = page.props.role;
	const loadingStore = useIsLoadingStore();
	let inDollars =ref(0);

	const buttonItems = [
		{
			label: 'Generar',
			icon: 'pi pi-check',
			command: () => {
				fetchTransactions();
			}
		},
		{
			label: 'Exportar',
			icon: 'pi pi-file-excel',
			command: () => {
				exportTransactions();
			}
		},
		{
			label: 'Exportar solo pagos',
			icon: 'pi pi-file-excel',
			command: () => {
				exportOnlyPayments();
			}
		},
		{
			label: 'Imprimir',
			icon: 'pi pi-print',
			command: () => {
				if(!year.value || !month.value) {
					toast.add({
						severity: 'error',
						summary: 'Error',
						detail: 'Debe seleccionar un mes y un año',
						life: 5000
					});
					return;
				}
				const startDate = new Date(year.value.code, month.value.code - 1, 1).toISOString().split('T')[0];
				const endDate = new Date(year.value.code, month.value.code, 0).toISOString().split('T')[0];
				window.location.href = `/payments/get-payments-transaction-pdf-get/${startDate}/${endDate}/${selectedOffice.value.code}/${inDollars.value}/${month.value.code}/${year.value.code}`;
			}
		}
	]

	let totalsActive = computed(() => {
		return Object.values(agentActiveTransactions.value).reduce((acc, transaction) => {
			return {
				transactions_listing_res: acc.transactions_listing_res + transaction.transactions_listing_res,
				transactions_selling_res: acc.transactions_selling_res + transaction.transactions_selling_res,
				transactions_listing_res_volume: acc.transactions_listing_res_volume + transaction.transactions_listing_res_volume,
				transactions_selling_res_volume: acc.transactions_selling_res_volume + transaction.transactions_selling_res_volume,

				amount_payment_res: acc.amount_payment_res + parseFloat(transaction.amount_payment_res),
				payments_in_year_res: acc.payments_in_year_res + parseFloat(transaction.payments_in_year_res),

				transactions_listing_com: acc.transactions_listing_com + transaction.transactions_listing_com,
				transactions_selling_com: acc.transactions_selling_com + transaction.transactions_selling_com,
				transactions_listing_com_volume: acc.transactions_listing_com_volume + transaction.transactions_listing_com_volume,
				transactions_selling_com_volume: acc.transactions_selling_com_volume + transaction.transactions_selling_com_volume,
				amount_payment_com: acc.amount_payment_com + parseFloat(transaction.amount_payment_com),
				payments_in_year_com: acc.payments_in_year_com + parseFloat(transaction.payments_in_year_com),
			}
		}, {
			transactions_listing_res: 0,
			transactions_selling_res: 0,
			transactions_listing_res_volume: 0,
			transactions_selling_res_volume: 0,

			amount_payment_res: 0,
			payments_in_year_res: 0,

			transactions_listing_com: 0,
			transactions_selling_com: 0,
			transactions_listing_com_volume: 0,
			transactions_selling_com_volume: 0,
			amount_payment_com: 0,
			payments_in_year_com: 0,
		});
	});
	let totalsInActive = computed(() => {
		return Object.values(agentNoActiveTransactions.value).reduce((acc, transaction) => {
			return {
				transactions_listing_res: acc.transactions_listing_res + transaction.transactions_listing_res,
				transactions_selling_res: acc.transactions_selling_res + transaction.transactions_selling_res,
				transactions_listing_res_volume: acc.transactions_listing_res_volume + transaction.transactions_listing_res_volume,
				transactions_selling_res_volume: acc.transactions_selling_res_volume + transaction.transactions_selling_res_volume,

				amount_payment_res: acc.amount_payment_res + parseFloat(transaction.amount_payment_res),
				payments_in_year_res: acc.payments_in_year_res + parseFloat(transaction.payments_in_year_res),

				transactions_listing_com: acc.transactions_listing_com + transaction.transactions_listing_com,
				transactions_selling_com: acc.transactions_selling_com + transaction.transactions_selling_com,
				transactions_listing_com_volume: acc.transactions_listing_com_volume + transaction.transactions_listing_com_volume,
				transactions_selling_com_volume: acc.transactions_selling_com_volume + transaction.transactions_selling_com_volume,
				amount_payment_com: acc.amount_payment_com + parseFloat(transaction.amount_payment_com),
				payments_in_year_com: acc.payments_in_year_com + parseFloat(transaction.payments_in_year_com),
			}
		}, {
			transactions_listing_res: 0,
			transactions_selling_res: 0,
			transactions_listing_res_volume: 0,
			transactions_selling_res_volume: 0,

			amount_payment_res: 0,
			payments_in_year_res: 0,

			transactions_listing_com: 0,
			transactions_selling_com: 0,
			transactions_listing_com_volume: 0,
			transactions_selling_com_volume: 0,
			amount_payment_com: 0,
			payments_in_year_com: 0,
		});
	});

	let agentActiveTransactions = computed(() => {
		return Object.values(transactions.value).filter(transaction => transaction.is_active);
	});
	
	let agentNoActiveTransactions = computed(() => {
		return Object.values(transactions.value).filter(transaction => !transaction.is_active);
	});

	
	const defaultStartDate = new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0];
	const defaultEndDate = new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0).toISOString().split('T')[0];


	startDate.value = defaultStartDate;
	endDate.value = defaultEndDate;
	

	async function fetchTransactions() {
		if(!year.value || !month.value) {
			toast.add({
				severity: 'error',
				summary: 'Error',
				detail: 'Debe seleccionar un mes y un año',
				life: 5000
			});
			return;
		}

		loadingStore.setLoading(true);

		const startDate = new Date(year.value.code, month.value.code - 1, 1).toISOString().split('T')[0];
		const endDate = new Date(year.value.code, month.value.code, 0).toISOString().split('T')[0];

		

		if(!['Administrador', 'Super Administrador'].includes(role)) {

			const agent = page.props.user?.agent?? null;
			
			if(agent) {
				selectedOffice.value = {
					'code' : page.props.user.agent.office.id,
					'name' : page.props.user.agent.office.name
				};
			}
		}

		const data = {
			'office_id' : selectedOffice.value?.code,
			'month' : month.value.code,
			'year' : year.value.code,
			'inDollars' : inDollars.value,
			'start_date': startDate,
			'end_date': endDate
		}

		console.log(data);

		apiClient.get('payments/get-payments-transaction', {
			params : data,
			timeout: 60000
		}).then((response) => {
			if(response.status == 200) {
				transactions.value = response.data;
			}
		}).catch((error) => {
			console.error('Error fetching transactions:', error);
			toast.add({
				severity: 'error',
				summary: 'Error',
				detail: 'Error al obtener los datos, contacte a sistemas',
				life: 5000
			});
		}).finally(() => {
			loadingStore.setLoading(false);
		});

	}

	function exportOnlyPayments () {

		if(!year.value || !month.value) {
			toast.add({
				severity: 'error',
				summary: 'Error',
				detail: 'Debe seleccionar un mes y un año',
				life: 5000
			});
			return;
		}

		loadingStore.setLoading(true);
		const startDate = new Date(year.value.code, month.value.code - 1, 1).toISOString().split('T')[0];
		const endDate = new Date(year.value.code, month.value.code, 0).toISOString().split('T')[0];

		if(!['Administrador', 'Super Administrador'].includes(role)) {

			const agent = page.props.user?.agent?? null;

			if(agent) {
				selectedOffice.value = {
					'code' : page.props.user.agent.office.id,
					'name' : page.props.user.agent.office.name
				};
			}
		}


		apiClient.get(`/payments/get-only-payments-excel/${selectedOffice.value.code}/${startDate}/${endDate}/${inDollars.value}/${month.value.code}/${year.value.code}`, {
			responseType: 'blob',
			timeout : 60000
		}).then((response) => {
			loadingStore.setLoading(false);
			const url = window.URL.createObjectURL(new Blob([response.data]));
			const officesName = selectedOffice.value.name;

			const link = document.createElement('a');
			link.href = url;
			link.setAttribute('download','Pagos '+ officesName + '_' + year.value.code + '_' + month.value.code + '.xlsx');

			document.body.appendChild(link);
			link.click();
			link.remove();

		}).catch((error) => {
			loadingStore.setLoading(false);
			console.error('Error descargando el archivo:', error);
		});
	}

function exportTransactions() {
	
	if(!year.value || !month.value) {
			toast.add({
				severity: 'error',
				summary: 'Error',
				detail: 'Debe seleccionar un mes y un año',
				life: 5000
			});
			return;
		}
	loadingStore.setLoading(true);
	const startDate = new Date(year.value.code, month.value.code - 1, 1).toISOString().split('T')[0];
	const endDate = new Date(year.value.code, month.value.code, 0).toISOString().split('T')[0];


	apiClient.get(`/payments/get-payments-transaction-excel/${selectedOffice.value.code}/${startDate}/${endDate}/${inDollars.value}/${month.value.code}/${year.value.code}`, {
		responseType: 'blob',
		timeout : 60000
	}).then((response) => {
		loadingStore.setLoading(false);
		const url = window.URL.createObjectURL(new Blob([response.data]));
		const officeName = selectedOffice.value.name;

		const link = document.createElement('a');
		link.href = url;
		link.setAttribute('download', officeName + '_' + year.value.code + '_' + month.value.code + '.xlsx');

		document.body.appendChild(link);
		link.click();
		link.remove();

	}).catch((error) => {
		loadingStore.setLoading(false);
		console.error('Error descargando el archivo:', error);
	});
}

async function fetchOffices() {
	const response = await apiClient.post('/dashboard/get-oficinas-por-ubicacion');
	offices.value = response.data.map(office => {
		return {
			code: office.id,
			name: office.name
		}
	});
}

onMounted(() => {
	fetchOffices();
});


</script>
