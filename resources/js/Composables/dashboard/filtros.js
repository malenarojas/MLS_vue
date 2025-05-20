import { ref } from 'vue';
import apiClient from "@/src/constansts/axiosClient";// Asegúrate de que esta ruta sea correcta

export function useFiltros() {
	// Estado para las regiones, departamentos, etc.
	const regiones = ref([{ name: 'Bolivia', cod: 120 }]);
	const departamentos = ref([]);
	const provincias = ref([]);
	const ciudades = ref([]);
	const zonas = ref([]);

	const regionSeleccionada = ref(0);
	const departamentoSeleccionado = ref(0);
	const provinciaSeleccionada = ref(0);
	const ciudadSeleccionada = ref(0);
	const zonaSeleccionada = ref(0);
	const tipoTransaccionSeleccionada = ref(0);

	const meses = ref([
		{ name: "Enero", code: "1" },
		{ name: "Febrero", code: "2" },
		{ name: "Marzo", code: "3" },
		{ name: "Abril", code: "4" },
		{ name: "Mayo", code: "5" },
		{ name: "Junio", code: "6" },
		{ name: "Julio", code: "7" },
		{ name: "Agosto", code: "8" },
		{ name: "Septiembre", code: "9" },
		{ name: "Octubre", code: "10" },
		{ name: "Noviembre", code: "11" },
		{ name: "Diciembre", code: "12" },
	]);

	const tiposTransaccion = ref ([
		{ name: "Venta", code : 1},
		{ name: "Alquiler", code : 2},
		{ name: "Anticretico", code : 3},
	]);

	const mesesSeleccionados = ref([]);
	const anio = ref([
		{ 'name': '2020', "code": '2020' },
		{ 'name': '2021', "code": '2021' },
		{ 'name': '2022', "code": '2022' },
		{ 'name': '2023', "code": '2023' },
		{ 'name': '2024', "code": '2024' },
	]);
	const anioSeleccionado = ref([]);

	// Filtros relacionados con oficinas y agentes
	const oficinas = ref([]);
	const oficinasTransaccion = ref([]);
	const oficinasSeleccionadas = ref(0);
	const agentes = ref([]);
	const agentesSeleccionados = ref([]);

	// Funciones para obtener los datos
	async function getDepartamentos() {
		const response = await apiClient.post('/dashboard/get-departamentos', {
			'region_id': regionSeleccionada.value ?? 120
		});
		if (response.status === 200) {
			departamentos.value = response.data.map(departamento => ({
				code: departamento.id,
				name: departamento.name,
			}));
		}
	}

	async function getProvincias() {
		const response = await apiClient.post('/dashboard/get-provincias', {
			'state_id': departamentoSeleccionado.value ? departamentoSeleccionado.value.map(departamento => departamento.code) : []
		});
		if (response.status === 200) {
			provincias.value = response.data.map(provincia => ({
				code: provincia.id,
				name: provincia.name,
			}));
		}
	}

	async function getCiudades() {
		const response = await apiClient.post('/dashboard/get-ciudades', {
			'province_id': provinciaSeleccionada.value ? provinciaSeleccionada.value.map(provincia => provincia.code) : []
		});
		if (response.status === 200) {
			ciudades.value = response.data.map(ciudad => ({
				code: ciudad.id,
				name: ciudad.name,
			}));
		}
	}

	async function getZonas() {
		console.log(ciudadSeleccionada.value);
		const response = await apiClient.post('/dashboard/get-zonas', {
			'city_id': ciudadSeleccionada.value ? ciudadSeleccionada.value.map(ciudad => ciudad.code) : []
		});
		console.log(response);
		if (response.status === 200) {
			zonas.value = response.data.map(zona => ({
				code: zona.id,
				name: zona.name,
			}));
		}
	}

	async function getOficinas() {
		const data = {
			'province_ids': provinciaSeleccionada.value ? provinciaSeleccionada.value.map(provincia => provincia.code) : [],
			'city_ids': ciudadSeleccionada.value ? ciudadSeleccionada.value.map(ciudad => ciudad.code) : [],
		};
		const response = await apiClient.post('/dashboard/get-oficinas-por-ubicacion', data);
		oficinas.value = response.data.map(oficina => ({
			code: oficina.id,
			name: oficina.name
		}));
	}

	async function getOficinasTransaccion() {
		const data = {
			'province_ids': provinciaSeleccionada.value ? provinciaSeleccionada.value.map(provincia => provincia.code) : [],
			'city_ids': ciudadSeleccionada.value ? ciudadSeleccionada.value.map(ciudad => ciudad.code) : [],
		};
		const response = await apiClient.post('/dashboard/get-oficinas-por-ubicacion', data);
		oficinasTransaccion.value = response.data.map(oficina => ({
			code: oficina.id,
			name: oficina.name,
			is_external:oficina.is_external,
			office_id:oficina.office_id
		}));
	}

	async function getAgentes() {
		const data = {
			'province_ids': provinciaSeleccionada.value ? provinciaSeleccionada.value.map(provincia => provincia.code) : [],
			'city_ids': ciudadSeleccionada.value ? ciudadSeleccionada.value.map(ciudad => ciudad.code) : [],
			'office_id': oficinasSeleccionadas.value ? oficinasSeleccionadas.value.map(oficina => oficina.code) : [],
		};
		const response = await apiClient.post('/dashboard/get-agentes-por-ubicacion', data);
		agentes.value = response.data.map(agente => ({
			code: agente.id,
			name: agente.name
		}));
	}

	function cargarFiltrosFechas(route) {
		if (route.query.mes) {
			mesesSeleccionados.value = [meses.value.find(mes => parseInt(mes.code) === parseInt(route.query.mes))];
		} else {
			mesesSeleccionados.value = [];
		}
		anioSeleccionado.value = [anio.value.find(anio => anio.code == route.query.anio)];
	}

	async function cargarFiltrosOficinasAgentes(route = null) {
		oficinasSeleccionadas.value = route?.query?.oficina ? [oficinas.value.find(oficina => oficina.code == route.query.oficina)] : [];
		if (route && route.query.oficina) {
			await getAgentes();
		}
		if (route && route.query.agente) {
			agentesSeleccionados.value = [agentes.value.find(agente => agente.code == route.query.agente)];
		}
	}

	//Changes

	async function changeDepartamento() {
		await getProvincias();
	}

	async function changeProvincia() {

		await getCiudades();
		await getOficinas();

	}

	async function changeCiudad() {
		getZonas();
		await getOficinas();
	}


	async function changeOficina () {
		agentesSeleccionados.value = '';
		if(oficinasSeleccionadas.value){
			getAgentes();
		}
	}


	// Exportar la lógica del filtro
	return {
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
		tipoTransaccionSeleccionada,
		tiposTransaccion,
		getDepartamentos,
		getProvincias,
		getCiudades,
		getZonas,
		getOficinas,
		getAgentes,
		cargarFiltrosFechas,
		cargarFiltrosOficinasAgentes,
		changeDepartamento,
		changeProvincia,
		changeCiudad,
		changeOficina,
		getOficinasTransaccion,
		oficinasTransaccion
	};
}
