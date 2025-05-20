<template>

	<div class="p-4">
		<h1 class="font-bold text-2xl opacity-75 mb-4"><i class="pi pi-globe"></i> Promedios</h1>
		<div class="flex gap-2 text-xs">

			<MultiSelect v-model="mesesSeleccionados" optionLabel="name" filter :options="meses" placeholder="Mes"
				:maxSelectedLabels="3" @change="getDatosPromedios()" />

			<MultiSelect v-model="anioSeleccionado" optionLabel="name" filter :options="anio" placeholder="Año"
				:maxSelectedLabels="3" @change="getDatosPromedios()" />
			
			<MultiSelect v-model="tipoTransaccionSeleccionada" optionLabel="name" filter :options="tiposTransaccion" placeholder="Tipo de transaccion"
				:maxSelectedLabels="3" @change="getDatosPromedios()" />

		</div>
		<div class="bg-indigo-50 p-4 flex justify-between gap-4 flex-wrap mt-4 text-sm">
			<div class="flex gap-2">
				<MultiSelect v-model="oficinasSeleccionadas" optionLabel="name" filter :options="oficinas"
					placeholder="Oficina" :maxSelectedLabels="3" @change="changeOficina(); getDatosPromedios();" />
				<MultiSelect :disabled="oficinasSeleccionadas == 0" v-model="agentesSeleccionados" optionLabel="name"
					filter :options="agentes" placeholder="Agente" :maxSelectedLabels="3"
					@change="getDatosPromedios()" />
			</div>

			<div class="gap-2 flex">

				<MultiSelect v-model="regionSeleccionada" optionLabel="name" filter :options="regiones"
					placeholder="Bolivia" :maxSelectedLabels="2" />

				<MultiSelect v-model="departamentoSeleccionado" optionLabel="name" filter @change="changeDepartamento(); getDatosPromedios();"
					:options="departamentos" placeholder="Departamento" :maxSelectedLabels="2" />

				<MultiSelect v-model="provinciaSeleccionada" optionLabel="name" filter @change="changeProvincia(); getDatosPromedios();"
					:options="provincias" placeholder="Provincia" :maxSelectedLabels="2" />

				<MultiSelect v-model="ciudadSeleccionada" optionLabel="name" filter @change="changeCiudad(); getDatosPromedios();"
					:options="ciudades" placeholder="Ciudad" :maxSelectedLabels="2" />

				<MultiSelect v-model="zonaSeleccionada" optionLabel="name" filter @change="getDatosPromedios();"
					:options="zonas" placeholder="Zona" :maxSelectedLabels="2" />
			</div>
		</div>

		<div class="mt-4">
			
				<span class="text-sm opacity-75">Precio promedio de captaciones</span>
				<div class="flex gap-2 items-center">
					<h3 class="mb-2 text-3xl font-bold opacity-85">{{ precioPromedio }}</h3>
					<h3
					:class="['mb-2 text-xl font-bold opacity-85', comparacionPrecioPasado[1] == '-' ? 'text-red-500' : 'text-green-500']"
					v-if="comparacionPrecioPasado"
					>{{ comparacionPrecioPasado }}</h3>

				</div>

				<span class="text-sm opacity-75">Tiempo promedio en el mercado</span>
				<div class="flex gap-2 items-center">
					<h3 class="mb-2 text-3xl font-bold opacity-85">{{ tiempoPromedio }}</h3>
					<h3 
					:class="['mb-2 text-xl font-bold opacity-85', comparacionTiempoPasado[1] == '-' ? 'text-red-500' : 'text-green-500']"
					v-if="comparacionTiempoPasado"
					>{{ comparacionTiempoPasado}}</h3>

				</div>
			
		</div>
	</div>

</template>

<script setup>
import { useRoute } from 'vue-router';
import { onMounted, ref } from "vue";
import apiClient from '@/src/constansts/axiosClient';
import VueApexCharts from "vue3-apexcharts";

const route = useRoute();

let tiempoPromedio =ref(0);
let precioPromedio =ref(0);

let comparacionTiempoPasado =ref(0);
let comparacionPrecioPasado =ref(0);

async function getDatosPromedios () {
	console.log(mesesSeleccionados.value);
	const data = {

		mesesSeleccionados: mesesSeleccionados.value && (mesesSeleccionados.value.length > 0) 
		? mesesSeleccionados.value.map(mes => mes.code) :
		[1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
		anioSeleccionado: anioSeleccionado.value ? anioSeleccionado.value.map(item => item.code) : ['2024'],

		oficina_id : oficinasSeleccionadas.value ? oficinasSeleccionadas.value.map(item => item.code) : [],
		agente_id : agentesSeleccionados.value ? agentesSeleccionados.value.map(item => item.code) : [],

		states_id : departamentoSeleccionado.value ? departamentoSeleccionado.value.map(item => item.code) : [],
		provincies_id : provinciaSeleccionada.value ? provinciaSeleccionada.value.map(item => item.code) : [],
		cities_id : ciudadSeleccionada.value ? ciudadSeleccionada.value.map(item => item.code) : [],
		zones_id : zonaSeleccionada.value ? zonaSeleccionada.value.map(item => item.code) : [],
		
		transaction_types_id : tipoTransaccionSeleccionada.value ? tipoTransaccionSeleccionada.value.map(tipoTransaccion => tipoTransaccion.code) : [],

	};

	comparacionTiempoPasado.value = 0;
	comparacionPrecioPasado.value = 0;

	console.log(data);

	const response = await apiClient.post('/dashboard/get-promedios', data, {
		timeout : 30000
	});
	console.log(response.data);
	if(response.status == 200) {
		tiempoPromedio.value = convertirDias(response.data['tiempoPromedio']);
		precioPromedio.value = '$ ' + Intl.NumberFormat(('en-US')).format(parseFloat(response.data['precioPromedio']));
	}

	if (anioSeleccionado.value.length == 1) {
		data.anioSeleccionado[0] = data.anioSeleccionado[0] - 1;

		const responsePasado = await apiClient.post('/dashboard/get-promedios', data, {
			timeout: 30000
		});
		console.log(responsePasado.data);
		if (response.status == 200) {
			const diferenciaTiempo = response.data['tiempoPromedio'] - responsePasado.data['tiempoPromedio'];
			const diferenciaTiempoProcentual = (diferenciaTiempo * 100 / response.data['tiempoPromedio']).toFixed(2);

			const diferenciaPrecio = response.data['precioPromedio'] - responsePasado.data['precioPromedio'];
			const diferenciaPrecioProcentual = (diferenciaPrecio * 100 / response.data['precioPromedio']).toFixed(2);

			comparacionTiempoPasado.value = diferenciaTiempoProcentual > 0 ? 
				`(+${diferenciaTiempoProcentual} % )` :
				`(${diferenciaTiempoProcentual} % )`;
			comparacionPrecioPasado.value = diferenciaPrecioProcentual > 0 ?
				`(+${diferenciaPrecioProcentual} % )` : 
				`(${diferenciaPrecioProcentual} % )` ;
		}
	}
}

function convertirDias(dias) {
    const anios = Math.floor(dias / 365); 
    dias = dias % 365; 
    const meses = Math.floor(dias / 30);
    dias = parseInt(dias % 30);

	if(anios) {
		return `${anios} Años, ${meses} Meses, ${dias} Días`;
	}else{
		return `${meses} Meses, ${dias} Días`;
	}
}

/*
	CODIGO DE FILTROS COPIAR APARTIR DE AQUI :)
	BY: Ing. Papitas
*/
import { useFiltros } from '@/composables/dashboard/filtros';

    const {
      regiones,
      departamentos,
      provincias,
      ciudades,
      zonas,
      regionSeleccionada,
      departamentoSeleccionado,
      provinciaSeleccionada,
      ciudadSeleccionada,
      zonaSeleccionada,
      meses,
      mesesSeleccionados,
      anio,
      anioSeleccionado,
      oficinas,
      oficinasSeleccionadas,
      agentes,
      agentesSeleccionados,
	  tiposTransaccion,
	  tipoTransaccionSeleccionada,
      getDepartamentos,
      getOficinas,
      cargarFiltrosFechas,
      cargarFiltrosOficinasAgentes,
	  changeDepartamento,
	  changeProvincia,
	  changeCiudad,
	  changeOficina,
    } = useFiltros();

onMounted( async () => {
	cargarFiltrosFechas(route);
	getDepartamentos();
	await getOficinas();
	await cargarFiltrosOficinasAgentes(route);
	getDatosPromedios();
});


definePageMeta({
  middleware: 'auth',
});
</script>