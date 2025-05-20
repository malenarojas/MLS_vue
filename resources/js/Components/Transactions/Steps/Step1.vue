<template>

	<div class="rounded bg-indigo-50 p-4 shadow-md">
		<div class="flex justify-between  gap-x-12 gap-y-8">
			<div class="flex gap-4">
				<img :src="store.getStepData(transactionId, 1).image_url" class="h-32"
						alt="" />

				<div class="flex flex-col gap-1 justify-center">
					<h1 class="text-lg">{{ store.getStepData(transactionId, 1).listing_name ?? '-'  }}</h1>
					<h2 class="opacity-75">Owner: {{ store.getStepData(transactionId, 1).owner }}</h2>
				</div>
			</div>

			<div class="flex flex-col gap-1 justify-center text-sm opacity-85">
				<span>ID captación: {{ store.getStepData(transactionId, 1).listing_id }}</span>
				<span>{{ store.getStepData(transactionId, 1).listing_transaction_type }}</span>
				<span>{{ store.getStepData(transactionId, 1).type_property }}</span>
			</div>
		</div>

		<h3 class="opacity-85 font-semibold mt-4 text-lg">Detalles de la transacción</h3>

		<div class="mt-4 bg-white rounded p-4 border">
			<h4 class="opacity-75 font-semibold  border-b-2 pb-2 ">Precio</h4>

			<div class="grid md:grid-cols-3 mt-4 gap-x-6 gap-y-6 text-sm">

				<div class="flex justify-between ">
					<span class="opacity-65">Precio de captacion:</span>
					<span class="opacity-85 font-bold">{{ Intl.NumberFormat('en-US').format(store.getStepData(transactionId, 1).current_listing_price) }} Bs.</span>
				</div>
				<div class="flex justify-between ">
					<span class="opacity-65">Fecha de la captacion:</span>
					<span class="opacity-85 font-bold">{{ store.getStepData(transactionId, 1).date_of_listing }} </span>
				</div>
				<div class="flex justify-between ">
					<span class="opacity-65">Dias en el mercado:</span>
					<span class="">{{ parseInt(store.getStepData(transactionId, 1).days_on_market) }} días. </span>
				</div>
				<div class="flex justify-between items-center">

					<div>
						<span class="opacity-65">Precio de venta: </span><span class="text-red-500">*</span>
					</div>

					<div class="flex gap-2 items-center">
						<InputNumber inputId="minmaxfraction" size="small" :minFractionDigits="0" :maxFractionDigits="2"
							fluid
							v-model="store.getStepData(transactionId, 1).sold_price"  />
						<span class="opacity-85 font-bold">Bs.</span>
					</div>
				</div>

				<div class="flex justify-between items-center">
					<div>
						<span class="opacity-65">Fecha de cierre: </span><span class="text-red-500">*</span>
					</div>
					<DatePicker v-model="store.getStepData(transactionId, 1).sold_date" @value-change="
						store.getStepData(transactionId, 1).sold_date = moment(store.getStepData(transactionId, 1).sold_date).format('DD/MM/YYYY');
					" showIcon fluid size="small" />
				</div>

				<div class="flex justify-between items-center">
					<span class="opacity-65">Fecha de posesión:</span>

					<DatePicker @value-change="
						store.getStepData(transactionId, 1).possession_date = moment(store.getStepData(transactionId, 1).possession_date).format('DD/MM/YYYY');
					" v-model="store.getStepData(transactionId, 1).possession_date" showIcon fluid  size="small" />
				</div>

			</div>
		</div>

		<div class="mt-2 flex gap-4">
			<div>
				<span class="text-sm opacity-75">A quien representa? </span><span class="text-red-500">*</span>
			</div>
			<div class="flex flex-wrap gap-4 mt-1">

				<div class="flex items-center gap-2">
					<RadioButton size="small" 
						:disabled="store.getStepData(transactionId, 1).restricted_side_selection" 
						v-model="store.getStepData(transactionId, 1).side" inputId="representacionAmbos"
						name="representacionAmbos" 
						value="both" />
					<label for="representacionAmbos" class="text-sm opacity-75">Ambos (Vendedor/Comprador)</label>
				</div>
				<div class="flex items-center gap-2" v-if="store.getStepData(transactionId, 1).transaction_type_id == 1">
					<RadioButton size="small" 
						:disabled="store.getStepData(transactionId, 1).restricted_side_selection" 
						v-model="store.getStepData(transactionId, 1).side"						 
						inputId="representacionVendedor"
						name="representacionVendedor" 
						value="listing" />

					<label for="representacionVendedor" class="text-sm opacity-75">Vendedor</label>
				</div>
				<div class="flex items-center gap-2" v-if="store.getStepData(transactionId, 1).transaction_type_id == 2">

					<RadioButton size="small" 
						:disabled="store.getStepData(transactionId, 1).restricted_side_selection" 
						v-model="store.getStepData(transactionId, 1).side" 
						inputId="representacionComprador"
						name="representacionComprador" 
						value="selling" />
						
					<label for="representacionComprador" class="text-sm opacity-75">Comprador</label>
				</div>
			</div>

		</div>
	</div>
</template>

<script setup>

import { useTransactionStepStore } from '@/Stores/useTransactionStore';
import { usePage } from '@inertiajs/vue3';
import moment from 'moment';

const transactionId = usePage().props.transaction_id

const store = useTransactionStepStore();



	

</script>
