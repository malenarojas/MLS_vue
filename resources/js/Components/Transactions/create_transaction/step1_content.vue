<template>

	<div class="rounded bg-indigo-50 p-4 shadow-md">
		<div class="flex justify-between flex-wrap">

			<div class="flex gap-4">
				<img src="https://remax.azureedge.net/userimages/120/L_a273f732-e235-4eb9-881f-700a60cf574f.jpg"
					class="h-32" alt="">

				<div class="flex flex-col gap-1 justify-center">
					<h1 class="text-lg">{ Nombre y direccion de la captacion }</h1>
					<h2 class="opacity-75">Owner: { Nombre del Dueño }</h2>
				</div>
			</div>

			<div class="flex flex-col gap-1 justify-center text-sm opacity-85">
				<span>ID captación: { ID Captacion }</span>
				<span>{ Tipo de transaccion }</span>
				<span>{ Tipo/Subtipo propiedad }</span>
			</div>
		</div>

		<h3 class="opacity-85 font-semibold mt-4 text-lg">Detalles de la transacción</h3>

		<div class="mt-4 bg-white rounded p-4 border">
			<h4 class="opacity-75 font-semibold  border-b-2 pb-2 ">Precio</h4>

			<div class="grid md:grid-cols-3 mt-4 gap-x-6 gap-y-6 text-sm">

				<div class="flex justify-between ">
					<span class="opacity-65">Precio de captacion:</span>
					<span class="opacity-85 font-bold">{ Precio } </span>
				</div>
				<div class="flex justify-between ">
					<span class="opacity-65">Fecha de la captacion:</span>
					<span class="opacity-85 font-bold">{ Fecha } </span>
				</div>
				<div class="flex justify-between ">
					<span class="opacity-65">Dias en el mercado:</span>
					<span class="">{ Precio } </span>
				</div>
				<div class="flex justify-between items-center">

					<div>
						<span class="opacity-65">Precio de venta: </span><span class="text-red-500">*</span>
					</div>

					<div class="flex gap-2 items-center">
						<InputNumber inputId="minmaxfraction" size="small" :minFractionDigits="0" :maxFractionDigits="5"
							fluid />
						<span class="opacity-85 font-bold">USD</span>
					</div>
				</div>

				<div class="flex justify-between items-center">
					<div>
						<span class="opacity-65">Fecha de cierre: </span><span class="text-red-500">*</span>
					</div>
					<DatePicker v-model="fechaCierre" showIcon fluid size="small" />
				</div>

				<div class="flex justify-between items-center">
					<span class="opacity-65">Fecha de posesión:</span>
					<DatePicker showIcon fluid size="small" />
				</div>

			</div>
		</div>

		<div class="mt-2 flex gap-4">
			<div>
				<span class="text-sm opacity-75">A quien representa? </span><span class="text-red-500">*</span>
			</div>
			<div class="flex flex-wrap gap-4 mt-1">
				<div class="flex items-center gap-2">
					<RadioButton size="small" v-model="representacion" inputId="representacionAmbos"
						name="representacionAmbos" value="ambos" />
					<label for="representacionAmbos" class="text-sm opacity-75">Ambos (Vendedor/Comprador)</label>
				</div>
				<div class="flex items-center gap-2">
					<RadioButton size="small" v-model="representacion" inputId="representacionVendedor"
						name="representacionVendedor" value="vendedor" />
					<label for="representacionVendedor" class="text-sm opacity-75">Vendedor</label>
				</div>
			</div>

		</div>
	</div>
</template>

<script setup>
import { RadioButton } from 'primevue';

let fechaCierre = ref(new Date());
let representacion = ref(0);
</script>