<template>
	<div class="step-four-container">
		<div class="bg-gray-100 border-b border-gray-300 p-3 mb-6">
			<h2 class="text-xm font-semibold text-gray-800">
				Paso 1: Ingrese los atributos de la Captacion
			</h2>
		</div>

	  <div class="grid grid-cols-5 gap-4">
		<!-- Tipo de Propiedad -->
		<div>
		  <label for="tipoPropiedad" class="block text-sm font-medium text-gray-700">Tipo de Propiedad</label>
		  <Dropdown
			id="tipoPropiedad"
			v-model="filters.property_type_id"
			:options="propertyTypes"
			optionLabel="name"
			optionValue="id"
			placeholder="Seleccione un tipo"
			class="w-full mt-1 text-xs"
			filter
		  />
		</div>

		<!-- Estado en el Mercado -->
		<div>
		  <label for="estadoMercado" class="block text-sm font-medium text-gray-700">Estado en el Mercado</label>
		  <Dropdown
			id="estadoMercado"
			v-model="filters.market_status_id"
			:options="marketStatuses"
			optionLabel="name_market_status"
			optionValue="id"
			placeholder="Seleccione un estado"
			class="w-full mt-1 text-xs"
			filter
		  />
		</div>

		<!-- Categoría -->
		<div>
		  <label for="categoria" class="block text-sm font-medium text-gray-700">Categoría</label>
		  <Dropdown
			id="categoria"
			v-model="filters.category_id"
			:options="categories"
			optionLabel="name_properties_categories"
			optionValue="id"
			placeholder="Seleccione una categoría"
			class="w-full mt-1 text-xs"
			filter
		  />
		</div>
	  </div>
	  <div class="grid grid-cols-6 gap-4 mt-6">
  <!-- Habitaciones -->
  <div>
    <label class="block text-sm font-medium text-gray-700">Habitaciones</label>
    <div class="flex space-x-2">
      <InputText
        v-model="filters.min_rooms"
        placeholder="Mín"
        class="w-full mt-1 text-sm"
				 type="number"
      />
      <InputText
        v-model="filters.max_rooms"
        placeholder="Máx"
        class="w-full mt-1 text-xs"
				 type="number"
      />
    </div>
  </div>

  <!-- Dormitorios -->
  <div>
    <label class="block text-sm font-medium text-gray-700">Dormitorios</label>
    <div class="flex space-x-2">
      <InputText
        v-model="filters.min_bedrooms"
        placeholder="Mín"
        class="w-full mt-1 text-xs"
				 type="number"
      />
      <InputText
        v-model="filters.max_bedrooms"
        placeholder="Máx"
        class="w-full mt-1 text-xs"
				 type="number"
      />
    </div>
  </div>

  <!-- Baños -->
  <div>
    <label class="block text-sm font-medium text-gray-700">Baños</label>
    <div class="flex space-x-2">
      <InputText
        v-model="filters.min_bathrooms"
        placeholder="Mín"
        class="w-full mt-1 text-xs"
				 type="number"
      />
      <InputText
        v-model="filters.max_bathrooms"
        placeholder="Máx"
        class="w-full mt-1 text-xs"
				 type="number"
      />
    </div>
  </div>

  <!-- Cuartos de baño -->
  <div>
    <label class="block text-sm font-medium text-gray-700">Cuartos de baño</label>
    <div class="flex space-x-2">
      <InputText
        v-model="filters.min_toiletroom"
        placeholder="Mín"
        class="w-full mt-1 text-xs"
				 type="number"
      />
      <InputText
        v-model="filters.max_toiletroom"
        placeholder="Máx"
        class="w-full mt-1 text-xs"
				 type="number"
      />
    </div>
  </div>

  <!-- Total SqM -->
  <div>
    <label class="block text-sm font-medium text-gray-700">Total SqM</label>
    <div class="flex space-x-2">
      <InputText
        v-model="filters.min_total_sqm"
        placeholder="Mín"
        class="w-full mt-1 text-xs"
				 type="number"
      />
      <InputText
        v-model="filters.max_total_sqm"
        placeholder="Máx"
        class="w-full mt-1 text-xs"
				 type="number"
      />
    </div>
  </div>

  <!-- Estacionamiento -->
  <div>
    <label class="block text-sm font-medium text-gray-700">Estacionamiento</label>
    <div class="flex space-x-2">
      <InputText
        v-model="filters.min_parking"
        placeholder="Mín"
        class="w-full mt-1 text-xs"
				 type="number"
      />
      <InputText
        v-model="filters.max_parking"
        placeholder="Máx"
        class="w-full mt-1 text-xs"
				 type="number"
      />
    </div>
  </div>

  <!-- Año Construcción -->
  <div>
    <label class="block text-sm font-medium text-gray-700">Año Construcción</label>
    <div class="flex space-x-2">
      <InputText
        v-model="filters.min_year_built"
        placeholder="Mín"
        class="w-full mt-1 text-xs"
				 type="number"
      />
      <InputText
        v-model="filters.max_year_built"
        placeholder="Máx"
        class="w-full mt-1 text-xs"
				 type="number"
      />
    </div>
  </div>

  <!-- Área Construida -->
  <div>
    <label class="block text-sm font-medium text-gray-700">Área Construida</label>
    <div class="flex space-x-2">
      <InputText
        v-model="filters.min_built_area"
        placeholder="Mín"
        class="w-full mt-1 text-xs"
				 type="number"
      />
      <InputText
        v-model="filters.max_built_area"
        placeholder="Máx"
        class="w-full mt-1 text-xs"
				 type="number"
      />
    </div>
  </div>

  <!-- Terreno -->
  <div>
    <label class="block text-sm font-medium text-gray-700">Terreno</label>
    <div class="flex space-x-2">
      <InputText
        v-model="filters.min_land_area"
        placeholder="Mín"
        class="w-full mt-1 text-xs"
				 type="number"
      />
      <InputText
        v-model="filters.max_land_area"
        placeholder="Máx"
        class="w-full mt-1 text-xs"
				 type="number"
      />
    </div>
  </div>

  <!-- # Plantas -->
  <div>
    <label class="blockm text-sm font-medium text-gray-700"># Plantas</label>
    <div class="flex space-x-2">
      <InputText
        v-model="filters.min_floors"
        placeholder="Mín"
        class="w-full mt-1 text-xs"
				 type="number"
      />
      <InputText
        v-model="filters.max_floors"
        placeholder="Máx"
        class="w-full mt-1 text-xs"
				 type="number"
      />
    </div>
  </div>

  <!-- Piso -->
  <div>
    <label class="block text-sm font-medium text-gray-700">Piso</label>
    <Dropdown
      v-model="filters.floor"
      :options="['No seleccionado', 'Planta Baja', '1', '2', '3', '4+']"
      placeholder="Seleccione"
      class="w-full mt-1 text-xs"
    />
  </div>
</div>
     <!-- Título con botón desplegable -->
		 <h2 class="form-title text-xm  flex justify-between items-center">
      Características
      <button
        class="p-2 bg-blue-500 text-white rounded hover:bg-blue-600 focus:outline-none"
        @click="toggleFeatures"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5"
          :class="isFeaturesVisible ? 'rotate-180' : ''"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M5.293 9.293a1 1 0 011.414 0L10 12.586l3.293-3.293a1 1 0 011.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
            clip-rule="evenodd"
          />
        </svg>
      </button>
    </h2>
    <hr class="my-4 border-gray-300" />

    <!-- Contenido desplegable -->
    <div v-show="isFeaturesVisible" class="transition-all duration-300">
      <!-- Checkbox para las características -->
      <div
        v-for="featureGroup in featureGroups"
        :key="featureGroup.id"
        class="mt-6"
      >
        <h3 class="text-lg font-semibold">{{ featureGroup.name }}</h3>
        <div class="grid grid-cols-3 gap-4">
          <div
            v-for="child in featureGroup.children"
            :key="child.id"
            class="flex items-center"
          >
            <Checkbox
              v-model="filters.selectedFeatures"
              :value="child.id"
              :label="child.name"
            />
            <label class="ml-2 text-xm text-gray-700">{{ child.name }}</label>
          </div>
        </div>
      </div>s
    </div>
	</div>
  </template>

<script setup>
import Checkbox from "primevue/checkbox";
import Dropdown from "primevue/dropdown";
import InputText from "primevue/inputtext";
import { computed, ref, watch } from "vue";

const props = defineProps({
  filtros: Object, // ✅ Este es el nombre real del prop que te llega del padre
  options: Object,
});

const emit = defineEmits(["anterior", "buscar", "updateFilters"]);

const filters = props.filtros; // ✅ Usás el alias correcto

// Estado para mostrar/ocultar características
const isFeaturesVisible = ref(false);
const toggleFeatures = () => {
  isFeaturesVisible.value = !isFeaturesVisible.value;
};

// Opciones para dropdowns y checkboxes
const propertyTypes = computed(() => props.options?.subtype_property || []);
const marketStatuses = computed(() => props.options?.market_status || []);
const categories = computed(() => props.options?.category_property || []);
const featureGroups = computed(() => props.options?.feactures || []);

// Watch para emitir cambios si se necesitan
watch(
  filters,
  () => {
    emit("updateFilters", { ...filters });
    console.log("🌀 Filtros del paso 4 emitidos:", filters);
  },
  { deep: true }
);
</script>


  <style>

.step-four-container {
  max-width: 100%;
  padding: 20px;
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
  flex-wrap: wrap; /* Permite ajuste en pantallas pequeñas */
  align-items: flex-start; /* Alinea los elementos en la parte superior */
  gap: 20px; /* Espaciado entre los grupos */
  width: 100%;
}

/* Grupos de formulario */
.form-group {
  flex: 1; /* Cada grupo ocupa espacio proporcional */
  min-width: 200px; /* Ancho mínimo */
  max-width: 300px; /* Evita que crezcan demasiado */
}

/* Botones de acción */
.button-group {
  display: flex;
  justify-content: space-between; /* Distribuye los botones uniformemente */
  gap: 10px;
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
/* Aplica estilo a los placeholders de los inputs */
input::placeholder {
  font-size: 12px; /* Tamaño más pequeño para los placeholders */
  color: #a0aec0; /* Color más claro opcional */
}

.text-xs::placeholder {
  font-size: 12px; /* Aplica este tamaño también para inputs con clase text-xs */
}
</style>
