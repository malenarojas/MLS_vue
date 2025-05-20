<template>
  <div class="step-three-container">
		<div class="bg-gray-100 border-b border-gray-300 p-3 mb-6">
			<h2 class="text-sm font-semibold text-gray-800">
				Paso 3: Detalles de la Ubicación
			</h2>
		</div>
    <!-- Contenedor en grid para 2 filas y 5 columnas -->
    <div class="grid grid-cols-5 gap-4">
      <!-- País -->
      <div>
        <label for="pais" class="block text-sm font-medium text-gray-700">País</label>
        <Dropdown
          id="pais"
          v-model="filters.region_id"
          :options="countries"
          optionLabel="name"
          optionValue="id"
          placeholder="Seleccione un país"
          @change="updateCountry"
          filter
          class="w-full mt-1 text-sm"
          :disabled="true"

        />
      </div>
      <!-- Departamento -->
      <div>
        <label for="estado" class="block text-sm font-medium text-gray-700">Departamento</label>
        <Dropdown
          id="state"
          v-model="filters.state_id"
          :options="states"
          optionLabel="name"
          optionValue="id"
          placeholder="Seleccione un Departamento"
          @change="updateLocation"
          filter
          class="w-full mt-1 text-sm"
        />
      </div>

      <!-- Provincia -->
      <div >
        <label for="provincia" class="block text-sm font-medium text-gray-700">Provincia</label>
        <Dropdown
          id="provincia"
          v-model="filters.province_id"
          :options="props.provinces"
          optionLabel="name"
          optionValue="id"
          placeholder="Seleccione una provincia"
          @change="updateLocation"
          filter
          class="w-full mt-1 text-sm"
        />
      </div>

      <!-- Ciudad -->
      <div >
        <label for="ciudad" class="block text-sm font-medium text-gray-700">Ciudad</label>
        <Dropdown
          id="ciudad"
          v-model="filters.city_id"
          :options="props.cities"
          optionLabel="name"
          optionValue="id"
          placeholder="Seleccione una ciudad"
          @change="updateLocation"
          filter
          class="w-full mt-1 text-sm"
        />
      </div>

      <!-- Zona -->
      <div>
        <label for="zona" class="block text-sm font-medium text-gray-700">Zona</label>
        <Dropdown
          id="zona"
          v-model="filters.zone_id"
          :options="props.zones"
          optionLabel="name"
          optionValue="id"
          placeholder="Seleccione una zona"
          @change="updateLocation"
          filter
          class="w-full mt-1 text-sm"
        />
      </div>
      <!-- Número en Calle -->
      <div>
        <label for="street-number" class="block text-sm font-medium text-gray-700">Número en Calle</label>
        <InputText
          id="street-number"
          v-model="filters.street_number"
          placeholder="Ingrese el número de calle"
          class="w-full mt-1 text-sm"
        />
      </div>

      <!-- Dirección -->
      <div>
        <label for="direccion" class="block text-sm font-medium text-gray-700">Dirección</label>
        <InputText
          id="direccion"
          v-model="filters.address"
          placeholder="Ingrese la dirección"
          class="w-full mt-1 text-sm"
        />
      </div>

      <!-- Distrito -->
      <div>
        <label for="distrito" class="block text-sm font-medium text-gray-700 ">Distrito</label>
        <InputText
          id="distrito"
          v-model="filters.district"
          placeholder="Ingrese el distrito"
          class="w-full mt-1 text-sm"
        />
      </div>

      <!-- Código Postal -->
      <div>
        <label for="codigo-postal" class="block text-sm font-medium text-gray-700">Código Postal</label>
        <InputText
          id="codigo-postal"
          v-model="filters.postal_code"
          placeholder="Ingrese el código postal"
          class="w-full mt-1 text-sm"
        />
      </div>
    </div>
    <div v-if="showMap" class="map-section">
		  <Map @update-listings="updateListingsFromMap" />
		</div>
  </div>
</template>

<script setup>
//import { useMarketAnalisisStore } from "@/stores/marketAnalisis";
import { computed, onMounted, ref, watch } from "vue";

import { router } from "@inertiajs/vue3";
// Tu componente de Mapa
import Map from "@/Pages/MarketAnalysis/map.vue";

const showMap = ref(false);

const props = defineProps({
  options: Object,
  provinces: Array,
  cities: Array,
  zones: Array,
});

const updateLocation = () => {
    console.log(
        `location actulizada`,props.provinces, props.cities, props.zones
    );
    router.visit(route("marketanalysis.index"), {
        method: "get",
        data: {
            state_id: filters.value.state_id,
            province_id: filters.value.province_id,
            city_id: filters.value.city_id,
        },
        only: ["provinces", "cities", "zones"],
        preserveState: true,
        preserveScroll: true,
        replace: true,
        preserveUrl: true,
    });
};

// Define los eventos que este componente puede emitir
const emit = defineEmits(['updateFilters', 'anterior', 'siguiente']);

//const marketAnalysisStore = useMarketAnalisisStore();

// Estados para los selectores
const filters = ref({
  country_id: null,
  region_id: null,
  state_id: null,
  province_id: null,
  city_id: null,
  zone_id: null,
  street_name: null,
  street_number: null,
  address: null,
  district: null,
  postal_code: null,
});

const countries = ref([{ id: 120, name: "Bolivia" }, { id: 2, name: "Argentina" }]);
const states = ref(props.options?.state || []);
const provinces = computed(() => props.location?.provinces || []);
const cities = computed(() => props.location?.cities || []);
const zones = computed(() => props.location?.zones || []);

const updateCountry = () => {
  filters.value.region_id = null;
  filters.value.province_id = null;
  filters.value.city_id = null;
  filters.value.zone_id = null;
};

const updateState = () => {
  filters.value.province_id = null;
  filters.value.city_id = null;
  filters.value.zone_id = null;
};

const updateProvince = () => {
  filters.value.city_id = null;
  filters.value.zone_id = null;
};

const updateCity = () => {
  filters.value.zone_id = null;
};

const updateZone = () => {
  console.log("Zona seleccionada:", filters.value.zone_id);
};
// Watch para emitir filtros actualizados
watch(
  filters,
  () => {
    emit("updateFilters", { ...filters.value });
    console.log("Filtros emitidos desde Paso 3:", filters.value);
  },
  { deep: true }
);
   // Función para manejar el evento desde el componente Map
	 const updateListingsFromMap = (newListings) => {
      listingsData.value = [...newListings];
    };
// Fetch opciones al montar
onMounted(() => {
  filters.value.region_id = 120; // ID de Bolivia
});


</script>


<style scoped>
/* Aplica estilo a los placeholders de los inputs */
input::placeholder {
  font-size: 12px; /* Tamaño más pequeño para los placeholders */
  color: #a0aec0; /* Color más claro opcional */
}

.text-xs::placeholder {
  font-size: 12px; /* Aplica este tamaño también para inputs con clase text-xs */
}
.step-three-container {
  max-width: 100%;
  padding: 20px;
  border: 1px solid #ddd;
  border-radius: 8px;
  background-color: #ffffff; /* Fondo blanco */
}

/* Título */
.form-title {
  text-align: left;
  font-size: 18px;
  margin-bottom: 20px;
  font-weight: bold;
}

/* Fila principal */
.form-row {
  display: flex;
  flex-wrap: nowrap; /* Asegura que todos los elementos estén en una sola fila */
  align-items: flex-start; /* Alinea los elementos en la parte superior */
  gap: 20px; /* Espaciado entre los grupos */
  width: 100%;
}

/* Estilo para cada grupo */
.form-group {
  flex: 1; /* Cada grupo ocupa espacio proporcional */
  min-width: 200px; /* Ancho mínimo para evitar desbordamientos */
  max-width: 300px; /* Evita que los grupos sean demasiado grandes */
}

/* Botones de acción */
.button-group {
  display: flex;
  justify-content: flex-end; /* Mueve los botones a la derecha */
  gap: 10px; /* Espaciado entre botones */
  margin-top: 20px; /* Espaciado superior */
}

.p-button-primary {
  background-color: #4caf50;
  color: white;
  padding: 10px 20px;
  border-radius: 5px;
}

.p-button-secondary {
  background-color: #cccccc;
  color: #333;
  padding: 10px 20px;
  border-radius: 5px;
}

</style>
