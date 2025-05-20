<template>
	<AppLayout title="Dashboard">
	<div class="p-4" id="dataTable">
		<div class="flex justify-between">
			<div class="">
				<h1 class="font-bold text-2xl opacity-75">
					<i class="pi pi-arrow-right-arrow-left"></i> Transacciones
				</h1>
				<span class="text-sm opacity-75 mb-4">Maneja tus transacciones</span>
			</div>

			<Button label="Nueva transaccion" size="small"
				v-if="permissions.findIndex(permission => permission == 'create transaction') >= 0"
				class="mb-auto shadow-md text-sm"
				@click="visibleTipo=true" />
		</div>

		<div class="bg-indigo-50 p-4 flex justify-between gap-4 flex-wrap mt-4 text-sm">
			<div class="flex gap-2">
				<Select v-model="oficinaSeleccionada" optionLabel="name" filter :options="oficinas" placeholder="Oficina"
					v-if="['Superadmin', 'Administrador', 'Soporte'].includes(role)"
					@change="async () => {
						await changeOficina();
						changeFiltros();
					}
					" />
				<Select :disabled="oficinaSeleccionada == 0" v-model="agenteSeleccionado" optionLabel="name" filter
					:options="agentes" placeholder="Agente" @change="changeFiltros()" />
			</div>

			<div class="flex flex-wrap gap-2">

				<Select 
					:options="[
						{ code : 0, name : 'Bs.'},
						{ code : 1, name : 'USD'}
					]"
					option-label="name"
					option-value="code"
					v-model="inDollars"
					@change="changeFiltros()"
					/>
				
				<MultiSelect 
					v-model = "selectedMonths"
					:options="[
						{ name: 'Enero', code: 1 },
						{ name: 'Febrero', code: 2 },
						{ name: 'Marzo', code: 3 },
						{ name: 'Abril', code: 4 },
						{ name: 'Mayo', code: 5 },
						{ name: 'Junio', code: 6 },
						{ name: 'Julio', code: 7 },
						{ name: 'Agosto', code: 8 },
						{ name: 'Septiembre', code: 9 },
						{ name: 'Octubre', code: 10 },
						{ name: 'Noviembre', code: 11 },
						{ name: 'Diciembre', code: 12 }
					]"
					optionLabel="name"
					optionValue="code"
					max-selected-labels="2"
					@change="changeFiltros()"
					/>

				<Select 
					v-model="selectedYear"
					:options="[
						{ name: '2023', code: 2023 },
						{ name: '2024', code: 2024 },
						{ name: '2025', code: 2025 }
					]"
					optionLabel="name"
					optionValue="code"
					@change="changeFiltros()"
				/>

				<Select v-model="tipoTransaccionSeleccionada" optionLabel="name" filter :options="tipoTransacciones"
					placeholder="Tipo de transaccion" @change="changeFiltros()" />

				<Select v-model="estadoTransaccionSeleccionada" optionLabel="name" filter :options="estadosTransacciones"
					placeholder="Estado de transaccion" @change="changeFiltros()" />

				<IconField>
					<InputIcon
					@click="
						stopFetching = false;
						getTransacciones();
						getPagos();
					"
					class="pi pi-search cursor-pointer" />
					<InputText v-model="inputBuscar" placeholder="Buscar" @input="
						stopFetching = false;
					" />
				</IconField>
			</div>
		</div>
		<Tabs value="0">
			<TabList>
				<Tab value="0">Transacciones</Tab>
				<Tab value="1">Pagos pendientes</Tab>
			</TabList>
			<TabPanels>
				<TabPanel value="0">

					<DataTable size="small" :value="transacciones" :reorderableColumns="true" class="text-sm"
					stripedRows resizableColumns columnResizeMode="expand" tableStyle="min-width: 50rem"
					scrollable scrollHeight="400px" :loading="isLoading" sortMode="multiple">
					<template #loading>
						<div class="h-[424.5px] flex justify-center items-center">
							<i class="pi pi-spin pi-spinner" style="font-size: 2rem"></i>
						</div>
					</template>
						<Column>
							<template #body="slotProps">
								<div class="flex justify-center">
									<Link :href="`/transactions/${slotProps.data.internal_id}/show`">
										<Button icon="pi pi-search" severity="info" size="small" raised rounded aria-label="Detail"
											v-if="permissions.includes('show transaction')" />
									</Link>
									 <!--
										<Button icon="pi pi-pencil" as="router-link" severity="warn" class="ml-2"
										:to="`/transactions/create?transaction_id=${slotProps.data.internal_id}`" size="small" raised rounded
										aria-label="Edit" />
										-->
									<!-- <Button icon="pi pi-file-pdf" as="router-link" severity="danger" class="ml-2"
										:to="`/transactions/pdf/${ slotProps.data.internal_id}`" size="small" raised
										rounded aria-label="Edit"
										v-if="permissions.findIndex(permission => permission == 'show transaction') >= 0" /> -->
								</div>
							</template>
						</Column>
						<Column sortable field="trr_id" header="TRR ID"></Column>
						<Column sortable field="transaction_status" header="Estado"></Column>
						<Column sortable field="transaction_type" header="Tipo de TRR"></Column>
						<Column sortable field="agent" header="Agente" width="200px">
							<template #body="slotProps">
								<div class="truncate" :title="slotProps.data.agent">
									{{ slotProps.data.agent }}
								</div>
							</template>
						</Column>
                        <Column sortable header="MLS" field="mls_id">
							<template #body="slotProps">
								{{ slotProps.data.is_external==0?slotProps.data.mls_id:'' }}
							</template>
						</Column>
						<Column sortable field="listing_transaction_type" header="Tipo de transaccion"></Column>
						<Column sortable field="area" header="Clase"></Column>
						<Column sortable field="price" header="Precio de captacion">
							<template #body="slotProps">
								<h1 class="text-end w-full pr-2">
									{{ `${inDollars == 1 ? 'USD' :'Bs.'} ${Intl.NumberFormat("en-US", {minimumFractionDigits : 2, maximumFractionDigits : 2}).format(slotProps.data.current_listing_price)}` }}
							</h1>

							</template>
						</Column>
						<Column sortable field="sold_date" header="Fecha de venta"></Column>
						<Column sortable field="current_listing_price" header="Precio de cierre">
							<template #body="slotProps">
								<h1 class="text-end w-full pr-2">
								{{ `${inDollars == 1 ? 'USD' :'Bs.'} ${Intl.NumberFormat("en-US", {minimumFractionDigits : 2, maximumFractionDigits : 2}).format(slotProps.data.current_listing_price)}` }}
							</h1>

							</template>
						</Column>
						<Column sortable field="pending_payments" header="Pagos restantes"></Column>
					</DataTable>
					<div v-show="isVisible" ref="referencia"
						class="h-0 opacity-0 text-white flex items-center justify-center mt-6"></div>

				</TabPanel>
				<TabPanel value="1">

					<DataTable size="small" :value="pagos" class="text-sm" stripedRows tableStyle="min-width: 50rem">

						<Column sortable field="trr_id" header="TRR ID"></Column>
						<Column sortable header="Estado de transacción">
							<template #body="slotProps">
								{{ getStatus(slotProps.data.transaction_status_id) }}
							</template>
						</Column>
						<Column sortable field="agent_name" header="Agente"></Column>
						<Column sortable field="side" header="Lado">
							<template #body="slotProps">
								{{ slotProps.data.transaction_type_id == 1 ? 'L' : 'S' }}

							</template>
						</Column>
                        <Column sortable field="MLS" header="MLS">
							<template #body="slotProps">
								{{ slotProps.data.is_external==0?slotProps.data.MLSID:'' }}
							</template>
						</Column>
						<Column sortable header="Tipo de transacción">
							<template #body="slotProps">
								{{ slotProps.data.listing_transaction_type == 1 ? 'Venta' :
								 slotProps.data.listing_transaction_type == 2 ? 'Alquiler' : 'Anticretico' }}

							</template>
						</Column>
						<Column sortable header="Dia de pago">
							<template #body="slotProps">
								{{ new Date(slotProps.data.expected_payment_date).toLocaleDateString('en-GB') }}
							</template>
						</Column>
						<Column sortable header="Valor esperado">
							<template #body="slotProps">
								{{ new Intl.NumberFormat('en-US').format(parseFloat(slotProps.data.amount_expected)) }} Bs.
							</template>
						</Column>
						<Column sortable header="Monto recibido">
							<template #body="slotProps">
								{{ slotProps.data.amount_received ?
								new Intl.NumberFormat('en-US').format(parseFloat(slotProps.data.amount_received)) :
								'-' }} Bs.
							</template>
						</Column>
						<Column sortable field="received_date" header="Fecha recibido"></Column>
						<Column sortable field="received" header="Recibido">
							<template #body="slotProps">
								<Checkbox @change="
									paymentReceived(slotProps.data, $event.target.checked);
									"
									:disabled="slotProps.data.received"
									v-model="slotProps.data.received" inputId="size_large" value="1" name="size" size="large" />
							</template>
						</Column>
						<template #footer>
							<div class="flex justify-end">
								<span class="font-semibold py-2 inline-block text-lg">
								Total por recibir: {{ Intl.NumberFormat('en-US').format(totalPayments.amount_expected) }} Bs..
							</span>
							</div>
						</template>
					</DataTable>
				</TabPanel>
			</TabPanels>
		</Tabs>


	</div>

	<Dialog v-model:visible="visible" modal header="Crear nueva transacción" :style="{ width: '50rem' }">
		<div class="mb-8 flex gap-2 flex-col">
			<label for="text-sm ">Agente</label>
			<Select v-model="agenteTransaccionSeleccionado" size="small" optionLabel="name" filter
				:options="agentesTransacciones" placeholder="Seleccionar" @value-change="getCaptaccionesTransacciones()">
			</Select>
		</div>

		<div class="mb-8 flex gap-2 flex-col">
			<label for="text-sm ">Captaciones</label>
			<Select :disabled="!agenteTransaccionSeleccionado" size="small" v-model="captacionTransaccionSeleccionada"
				optionLabel="name" filter :options="captacionesTransacciones" placeholder="Seleccionar">
			</Select>
		</div>

		<div class="flex justify-end gap-2">
			<Button type="button" label="Cancelar" size="small" severity="secondary" @click="visible = false"></Button>
			<Button type="button" label="Guardar" size="small" :disabled="!captacionTransaccionSeleccionada"
				@click="visible = false; crearTransaccion();"></Button>
		</div>
	</Dialog>

	<!--modal tipo captacion-->
	<Dialog v-model:visible="visibleTipo" modal header="Crear nueva transacción" :style="{ width: '25rem' }">
		<div class="mb-8 flex gap-4 flex-row">
			<div class="flex items-center gap-2">
				<RadioButton v-model="tipo" inputId="capt1" name="tipo" value="1" />
				<label for="capt1">Captación Interna</label>
			</div>
			<div class="flex items-center gap-2">
				<RadioButton v-model="tipo" inputId="cap2" name="tipo" value="2" />
				<label for="capt2">Captación Externa</label>
			</div>
		</div>
		<div class="flex justify-end gap-2">
			<Button type="button" label="Cancelar" size="small" severity="secondary" @click="visibleTipo = false"></Button>
			<Button type="button" label="Aceptar" size="small" :disabled="!tipo"
				@click="visibleTipo = false; selectTipo();"></Button>
		</div>
	</Dialog>
	<!--modal tipo captacion-->

	<!--modal captacion externa -->
	<Dialog v-model:visible="visibleExterno" modal header="Crear Captación Externa" :style="{ width: '30rem' }"
		:breakpoints="{ '1199px': '75vw', '575px': '90vw' }">

		<Form v-slot="$form" :captExterna :resolver="resolver"  @submit="onFormSubmit">
			<div class="grid grid-cols-12 gap-3">

				<div class="col-span-12">
					<p>Aqui es donde agregas tu reporte de cierre por la parte compradora/locataria</p>
					<label for="text-sm">Agente</label>
					<Select v-model="captExterna.agent_id" name="agent_id" class="w-full my-1" size="small" optionValue="id" optionLabel="name" filter
						:options="agentesTransacciones" placeholder="Seleccionar">
					</Select>
					<Message v-if="$form.agent_id?.invalid" severity="error" size="small" variant="simple">{{ $form.agent_id.error.message }}</Message>
				</div>

				<div class="col-span-12">
					<label for="text-sm">Referencia de la Captación</label>
					<InputText size="small" type="text" v-model="captExterna.reference" name="reference" class="w-full my-1" />
					<Message v-if="$form.reference?.invalid" severity="error" size="small" variant="simple">{{ $form.reference.error.message }}</Message>
				</div>
				<div class="mt-2 col-span-12">
					<hr>
				</div>
				<div class="col-span-6">
					<label for="text-sm" >Dirección</label>
					<InputText size="small" name="first_address" type="text" v-model="captExterna.first_address" class="w-full" />
					<Message v-if="$form.first_address?.invalid" severity="error" size="small" variant="simple">{{ $form.first_address.error.message }}</Message>
				</div>
				<div class="col-span-3">
					<label for="text-sm ">Nº</label>
					<InputText size="small" type="text" v-model="captExterna.number" class="w-full" />
				</div>
				<div class="col-span-3">
					<label for="text-sm ">Apt.</label>
					<InputText size="small" type="text" v-model="captExterna.unit_department" class="w-full" />
				</div>

				<div class="col-span-6">
					<label for="text-sm ">Departamento</label>
					<Select v-model="captExterna.state_id" class="w-full" size="small" name="state_id" optionValue="id" optionLabel="name" filter
						:options="departamentos" placeholder="Seleccionar" @value-change="getProvincias()">
					</Select>
					<Message v-if="$form.state_id?.invalid" severity="error" size="small" variant="simple">{{ $form.state_id.error.message }}</Message>
				</div>

				<!--
				<div class="col-span-6">
					<label for="text-sm ">Región</label>
					<Select v-model="captExterna.region_id" class="w-full" size="small" optionLabel="name" filter
					:options="agentesTransacciones" placeholder="Seleccionar" @value-change="getCaptaccionesTransacciones()">
					</Select>
				</div>
				-->
				<div class="col-span-6">
					<label for="text-sm ">Provincia</label>
					<Select v-model="captExterna.province_id" class="w-full" optionValue="id" size="small" optionLabel="name" filter
						:options="provincias" placeholder="Seleccionar"  @value-change="getCiudades()">
					</Select>
				</div>

				<div class="col-span-6">
					<label for="text-sm ">Ciudad</label>
					<Select v-model="captExterna.city_id" class="w-full" size="small" optionValue="id" optionLabel="name" filter
						:options="ciudades" placeholder="Seleccionar" @value-change="getZonas()">
					</Select>
				</div>
				<div class="col-span-6">
					<label for="text-sm ">Zona</label>
					<Select v-model="captExterna.zone_id" class="w-full" size="small" optionValue="id" optionLabel="name" filter
						:options="zonas" placeholder="Seleccionar">
					</Select>
				</div>
				<div class="col-span-6">
					<label for="text-sm">Código</label>
					<InputText size="small" type="text" v-model="captExterna.zip_code" class="w-full" />
				</div>

				<div class="mt-2 col-span-12">
					<hr>
				</div>
				<div class="col-span-6">
					<label for="text-sm">Precio de la Captación</label>
					<InputNumber v-model="captExterna.amount" size="small" name="amount" fluid min="0"  inputId="locale-us" locale="en-US" :minFractionDigits="2">
					</InputNumber>
					<Message v-if="$form.amount?.invalid" severity="error" size="small" variant="simple">{{ $form.amount.error.message }}</Message>
				</div>
				<!--
					<div class="col-span-6">
						<label for="text-sm">Moneda</label>
						<Select v-model="captExterna.mo" class="w-full" size="small" optionLabel="name" filter
						:options="agentesTransacciones" placeholder="Seleccionar">
					</Select>
				</div>
				-->


				<div class="col-span-6">
					<label for="text-sm">Fecha inicial de la Captación</label>
					<DatePicker @update:modelValue="setDateI" size="small" :manualInput="false" v-model="captExterna.date_of_listing"
						dateFormat="dd/mm/yy" showIcon fluid iconDisplay="input" name="date_of_listing" inputId="icondisplay" />
					<Message v-if="$form.date_of_listing?.invalid" severity="error" size="small" variant="simple">{{ $form.date_of_listing.error.message }}</Message>
				</div>
				<div class="col-span-6">
					<label for="text-sm">Tipo de Transacción </label>
					<Select v-model="captExterna.transaction_type_id" class="w-full" size="small" optionValue="id" optionLabel="name" filter
						:options="tipos_transacciones" placeholder="Seleccionar" name="transaction_type_id">
					</Select>
					<Message v-if="$form.transaction_type_id?.invalid" severity="error" size="small" variant="simple">{{ $form.transaction_type_id.error.message }}</Message>
				</div>


				<div class="flex gap-6 flex-row col-span-12 justify-start">
					<div class="flex  items-center gap-3">
						<RadioButton v-model="captExterna.area_id" inputId="cap2" name="tipo" value="2" />
						<label for="capt2">Residencial</label>
					</div>
					<div class="flex items-center gap-3">
						<RadioButton v-model="captExterna.area_id" inputId="capt1" name="tipo" value="1" />
						<label for="capt1">Comercial</label>
					</div>
				</div>

				<div class="col-span-6">
					<label for="text-sm">Tipo de propiedad </label>
					<Select v-model="captExterna.subtype_property_id" class="w-full" size="small" optionValue="id" optionLabel="name" filter
						:options="subTipos" placeholder="Seleccionar" name="subtype_property_id" >
					</Select>
					<Message v-if="$form.subtype_property_id?.invalid" severity="error" size="small" variant="simple">{{ $form.subtype_property_id.error.message }}</Message>
				</div>

				<div class="col-span-6">
					<label for="text-sm"> Tipo de Contrato</label>
					<Select v-model="captExterna.contract_type_id" name="contract_type_id" class="w-full" size="small" optionValue="id" optionLabel="name" filter
						:options="contratos" placeholder="Seleccionar">
					</Select>
					<Message v-if="$form.contract_type_id?.invalid" severity="error" size="small" variant="simple">{{ $form.contract_type_id.error.message }}</Message>
				</div>

				<div class="mt-2 col-span-12">
					<hr>
				</div>
				<div class="col-span-3">
					<label for="text-sm">Numero Total de ambientes </label>
					<InputNumber v-model="captExterna.total_number_rooms" size="small" inputId="withoutgrouping"
						:useGrouping="false" fluid />
				</div>
				<div class="col-span-3">
					<label for="text-sm">Num. de dormitorios</label>
					<InputNumber v-model="captExterna.number_bedrooms" size="small" inputId="withoutgrouping" :useGrouping="false"
						fluid />
				</div>
				<div class="col-span-3">
					<label for="text-sm">Num. de baños</label>
					<InputNumber v-model="captExterna.number_bathrooms" size="small" inputId="withoutgrouping"
						:useGrouping="false" fluid />
				</div>
				<div class="col-span-3">
					<label for="text-sm">Total metros cuadrados</label>
					<InputNumber v-model="captExterna.total_area" size="small" inputId="withoutgrouping" :useGrouping="false"
						fluid />
				</div>
				<div class="col-span-3">
					<label for="text-sm">Volumen Cúbico</label>
					<InputNumber v-model="captExterna.cubic_volume" size="small" inputId="withoutgrouping" :useGrouping="false"
						fluid />
				</div>
				<div class="col-span-3">
					<label for="text-sm">Tamaño del lote</label>
					<InputNumber v-model="captExterna.land_m2" size="small" inputId="withoutgrouping" :useGrouping="false"
						fluid />
				</div>
				<div class="col-span-12">
					<label for="text-sm">Descripción</label>
					<Textarea class="w-full" v-model="captExterna.description_website" rows="5" cols="30" />
				</div>
			</div>

			<div class="flex justify-end gap-2 mt-5">
				<Button type="button" label="Cancelar" size="small" severity="secondary"
					@click="visibleExterno = false"></Button>
				<Button type="submit" label="Aceptar" size="small" ></Button>
			</div>
		</Form>
	</Dialog>
</AppLayout>
	<!--modal captacion externa -->
</template>

<script setup>
import { useRoute } from 'vue-router';
import { router } from '@inertiajs/vue3';
import { onMounted, ref, computed } from "vue";
import apiClient from "@/src/constansts/axiosClient";
import { useFiltros } from "@/Composables/dashboard/filtros";
import { debounce } from 'lodash';
import { yupResolver } from "@primevue/forms/resolvers/yup";
import * as yup from "yup";
import moment from 'moment';
import { Form } from '@primevue/forms';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

import { usePage } from '@inertiajs/vue3';

const page = usePage();
const route = useRoute();

let agenteSeleccionado = ref(0);
let oficinaSeleccionada = ref(0);
let estadoTransaccionSeleccionada = ref(0);
let tipoTransaccionSeleccionada = ref(0);
let transacciones = ref([]);
let inputBuscar = ref(null);
let visible = ref(false);
let visibleTipo = ref(false);
let visibleExterno = ref(false);
let tipo = ref('1');
let isVisible = ref(true);
let stopFetching = ref(false);
let pagos = ref([]);
let selectedMonths = ref([]);
let selectedYear = ref(new Date().getFullYear());
let inDollars = ref(0);

const role = page.props.role;
const user = page.props.user;
const permissions = page.props.permissions;

let isLoading = ref(false);

const totalPayments = computed(() => {
	return {
		amount_expected: pagos.value.reduce((acc, pago) => acc + parseFloat(pago.amount_expected), 0),
		amount_received: pagos.value.reduce((acc, pago) => acc + parseFloat(pago.amount_received), 0),
		avarage_amount_expected: pagos.value.reduce((acc, pago) => acc + parseFloat(pago.amount_expected), 0) / pagos.value.length,
		avarage_amount_received: pagos.value.reduce((acc, pago) => acc + parseFloat(pago.amount_received), 0) / pagos.value.length,
	}
});

const getStatus = (statusId) => {
	const statuses = {
		2 : 'Enviado',
		3 : 'Incompleto',
		4 : 'Pendiente',
		5 : 'Aceptado'
	};
	return statuses[statusId];
};

function changeFiltros() {
	transacciones.value = [];
	stopFetching.value = false;
	getTransacciones();
	getPagos();
}

document.addEventListener('keydown', (event) => {
	if (event.key === 'Enter') {
		stopFetching.value = false;
		getTransacciones();
		getPagos();
	}
});

async function crearTransaccion() {
	const data = {
		agent_id: agenteTransaccionSeleccionado.value.id,
		listing_id: captacionTransaccionSeleccionada.value.code,
	}
	const response = await apiClient.post('/transactions/store', data);
	router.visit(`/transactions/create?transaction_id=${response.data.internal_id}`);
}

async function crearTransaccionExterno() {

	const response = await apiClient.post('/listings/external', captExterna.value);

	const data = {
		agent_id: response.data.data.agent_id,
		listing_id: response.data.data.id,
	}
	visibleExterno.value=false;
	const response2 = await apiClient.post('/transactions/store', data);
	router.visit(`/transactions/create?transaction_id=${response2.data.internal_id}`);

}

const onFormSubmit = ({ valid, values }) => {
    if (valid) {
       crearTransaccionExterno()

    }
};

const abrirTransaccion = async (event) => {
	const uuid = event.data.internal_id;

	const query = {
		transaction_id: uuid,
	}

	const url = router.resolve({
		path: '/transactions/create',
		query: query
	}).href;

	window.open(url, '_blank');
}

async function getTransacciones() {

	isLoading.value = true;

	if(role == 'Agente') {
		agenteSeleccionado.value = {code : user.agent.id};
	}

	if(role == 'Broker') {
		oficinaSeleccionada.value = {code : user.agent.office.id} ;
	}

	const data = {
		transaction_status_id: estadoTransaccionSeleccionada.value
			? [estadoTransaccionSeleccionada.value.code]
			: [],
		transaction_type_id: tipoTransaccionSeleccionada.value
			? [tipoTransaccionSeleccionada.value.code]
			: [],
		agent_id: agenteSeleccionado.value
			? [agenteSeleccionado.value.code]
			: [],
		office_id: oficinaSeleccionada.value
			? [oficinaSeleccionada.value.code]
			: [],
		transacciones_cargadas:
			transacciones.value && !inputBuscar.value
				? transacciones.value.map(
					(transaccion) => transaccion.transaction_id
				)
				: [],
		trr_id: inputBuscar.value ? [inputBuscar.value] : [],

		months : selectedMonths.value,
		year : selectedYear.value,
		inDollars : inDollars.value
	};

	if (!stopFetching.value) {
		const response = await apiClient.post(
			"/transactions/get-transactiones",
			data,
			{
				timeout: 30000,
			}
		);
		isLoading.value = false;

		if (response.status == 200) {
			if (response.data.length == 0) {
				stopFetching.value = true;
			}
			const responseTrasacciones = response.data.map(
				(item) =>
				(item = {
					trr_id: item.trr_id,

					transaction_status: estadosTransacciones.value.find(
						(estado) => estado.code == item.transaction_status_id
					).name,

					transaction_type: tipoTransacciones.value.find(
						(tipo) => tipo.code == (item.both == 0 ? item.transaction_type_id : 3)
					).name,

					agent: item.agent,

					mls_id: item.MLSID,
					is_external: item.is_external,

					listing_transaction_type: item.listing_transaction_type,

					area: item.area_id == 1 ? "Res" : "Com",

					current_listing_price: item.current_listing_price,

					sold_date: item.sold_date ? moment(item.sold_date).format('DD/MM/YYYY') : '',

					price: item.current_listing_price,

					payments_todo: 0,

					transaction_id: item.transaction_id,

					internal_id: item.internal_id,

					pending_payments : item.pending_payments,
				})
			);
			if (inputBuscar.value) {
				transacciones.value = [];
			}
			responseTrasacciones.forEach((transaccion) => {
				transacciones.value.push(transaccion);
			});
		}
	}

}

async function getPagos() {
	const data = {
		params: {
			'not_received': true,
			'office_id': oficinaSeleccionada.value && oficinaSeleccionada.value.code != 0 ?
			oficinaSeleccionada.value.code : '',
			'agent_id': agenteSeleccionado.value && agenteSeleccionado.value.code != 0 ?
			agenteSeleccionado.value.code : '',
			'trr_report_id' : inputBuscar.value ? inputBuscar.value : '',
			'months' : selectedMonths.value,
			'year' : selectedYear.value
		}
	}

	const response = await apiClient.get('/payments/get-payments-details', data);
	if (response.status == 200) {
		pagos.value = response.data.map(pago => ({
			...pago,
			received: !!pago.date_received,
		}));
	}
}

async function changeOficina() {
	stopFetching.value = false;
	agentesSeleccionados.value = "";
	if (oficinaSeleccionada.value) {
		await getAgentes();
	}
}

async function getAgentes() {
	stopFetching.value = false;
	const data = {
		office_id: oficinaSeleccionada.value,
	};
	const response = await apiClient.post(
		"/dashboard/get-agentes-por-ubicacion",
		data
	);
	agentes.value = response.data.map((agente) => ({
		code: agente.id,
		name: agente.name,
	}));
	agentes.value.sort((a, b) => a.name.localeCompare(b.name));
}

let agentesTransacciones = ref([]);
let agenteTransaccionSeleccionado = ref(null);
let departamentos = ref([])
let provincias = ref([])
let ciudades = ref([])
let zonas = ref([])
let tipos_transacciones = ref([])
let subTipos = ref([])
let contratos = ref([])
const captExterna = ref({
	agent_id: null,
	first_address: null,
	second_address: null,
	number: null,
	state_id: null,
	zone_id: null,
	city_id: null,
	province_id: null,
	region_id: 120,
	reference: null,
	status_listing_id: 1,
	date_of_listing: new Date(),
	area_id: "2",
	number_bathrooms: null,
	total_number_rooms: null,
	number_bedrooms: null,
	zip_code: null,
	unit_department: null,
	contract_type_id: null,
	cubic_volume: null,
	price_type_id:null,
	transaction_type_id: null,
	subtype_property_id: null,
	description_website: null,
	land_m2: null,
	total_area: null
});
const resolver = ref(
	yupResolver(
		yup.object().shape({
			agent_id: yup.number().required("Campo requerido"),
			state_id: yup.string().required("Campo requerido"),
			first_address: yup.string().required("Campo requerido"),
			province_id: yup.string().required("Campo requerido"),
			amount: yup.number().required("Campo requerido"),
			transaction_type_id: yup.number().required("Campo requerido"),
			contract_type_id: yup.number().required("Campo requerido"),
			subtype_property_id: yup.number().required("Campo requerido")
		})
	)
);
const selectTipo = () => {
	getAgentesTransacciones()
	if (tipo.value == 1) {
		visible.value = true
	} else {
		getDepartamentos()
		getTransactionType()
		getSubtipos()
		getContratos()
		visibleExterno.value = true
	}
}

async function paymentReceived (payment, isChecked) {

	const response = await apiClient.post('/payments/update-payment', {
		payment_id: payment.id,
		received: isChecked,
	});

	if(payment.received == 1) {
		payment.received_date = new Date().toLocaleDateString('en-GB');
		payment.amount_received = payment.amount_expected;
	}else{
		payment.received_date = null;
		payment.amount_received = 0;
	}
}

async function getDepartamentos() {
	const data = {
		// office_id :
	};
	const response = await apiClient.get("/options/get-departamentos");
	if (response.status == 200) {
		departamentos.value = response.data;
	}
}
async function getTransactionType() {

	const response = await apiClient.get("/options/get-transacciones");
	if (response.status == 200) {
		tipos_transacciones.value = response.data;
	}
}

async function getSubtipos() {

	const response = await apiClient.get("/options/get-subtipo");
	if (response.status == 200) {
	subTipos.value = response.data;
	}
}

async function getContratos() {

	const response = await apiClient.get("/options/get-contrato");
	if (response.status == 200) {
	contratos.value = response.data;
	}
}

async function getProvincias() {
	provincias.value=[]
	const data = {
	'state_id': [captExterna.value.state_id]
	};
		const response = await apiClient.post('/options/get-provincias', data);
		provincias.value=response.data;
	}

	async function getCiudades() {
	ciudades.value=[]
	const data = {
	'province_id': [captExterna.value.province_id]
	};
		const response = await apiClient.post('/options/get-ciudades', data);
		ciudades.value=response.data;
	}

	async function getZonas() {
	zonas.value=[]
	const data = {
	'city_id': [captExterna.value.city_id]
	};
		const response = await apiClient.post('/options/get-zonas', data);
		zonas.value=response.data;
	}


async function getAgentesTransacciones() {

	const data = {
		oficina_id : oficinaSeleccionada.value.code
	};
	const response = await apiClient.post("/dashboard/get-agentes", data);
	if (response.status == 200) {
		agentesTransacciones.value = response.data;
	}
}

let captacionesTransacciones = ref([]);
let captacionTransaccionSeleccionada = ref(null);

async function getCaptaccionesTransacciones() {

	const response = await apiClient.get(
		`/agents/listings/${agenteTransaccionSeleccionado.value.id}`
	);
	if (response.status == 200) {
		captacionesTransacciones.value = Object.values(response.data).map(
			(captacion) => {
				return {
					code: captacion.id,
					name: captacion.MLSID,
				};
			}
		);
	}
}


let tipoTransacciones = ref([
	{ code: "1", name: "Listing" },
	{ code: "2", name: "Selling" },
	{ code: "3", name: "Both" },
]);

let referencia = ref(null);

// function setupIntersectionObserver() {
// 	const element = referencia.value;
// 	if (!element) {
// 		return;
// 	}

// 	const observer = new IntersectionObserver(
// 		async (entries) => {
// 			entries.forEach((entry) => {
// 				if (entry.isIntersecting && !inputBuscar.value) {
// 					debouncedGetTransacciones();
// 					isVisible.value = !isVisible.value;
// 					setTimeout(() => {
// 						isVisible.value = !isVisible.value;
// 					}, 2000);
// 				}
// 			});
// 		},
// 		{
// 			threshold: 0.1,
// 		}
// 	);

// 	observer.observe(element);

// 	return () => observer.disconnect();
// }


let estadosTransacciones = ref([]);
async function cargarDatosFiltros() {
	const responseTransactionStatuses = await apiClient.get(
		"/transactions/get-transaction-statuses"
	);

	estadosTransacciones.value = responseTransactionStatuses.data.map(
		(status) => {
			return {
				code: status.id,
				name: status.name,
			};
		}
	);
}

const {
	oficinas,
	agentes,
	agentesSeleccionados,
	getOficinas,
	cargarFiltrosOficinasAgentes,
} = useFiltros();
function setDateI(date) {
	captExterna.date_of_listing = moment(date).format('YYYY-MM-DD');
}

onMounted(async () => {
	selectedMonths.value = [ 
		new Date().getMonth() + 1	
	];
	await getOficinas();
	if(role == 'Broker') {
		console.log(oficinas.value);
		console.log(user.agent.office.id);
		oficinaSeleccionada.value = oficinas.value.filter(oficina => oficina.code == (user.agent.office.id));
		if(oficinaSeleccionada.value && oficinaSeleccionada.value.length > 0) {
			oficinaSeleccionada.value = oficinaSeleccionada.value[0];
			getAgentes();
		}else{
			oficinaSeleccionada.value = [];
		}
	}
	await cargarFiltrosOficinasAgentes();
	await cargarDatosFiltros();
	await getTransacciones();
	await getPagos()
	//setupIntersectionObserver();

	
});


</script>



