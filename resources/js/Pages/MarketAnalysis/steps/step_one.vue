<template>
  <div class="step-one-container">
    <div class="bg-gray-100 border-b border-gray-300 p-3 mb-4">
      <h2 class="text-sm font-semibold text-gray-800">
        Paso 4: Elija las preferencias de propiedad
      </h2>
    </div>

    <div class="grid grid-cols-3 gap-4 items-end">

      <!-- Estado de la Captación -->
      <div>
        <label class="text-sm block mb-1">Estado de la captación</label>
        <MultiSelect
          v-model="filters.status"
          :options="filteredStatuses"
          optionLabel="name"
          optionValue="id"
          filter
          placeholder="Selecciona estados"
          :maxSelectedLabels="3"
          class="w-full text-sm"
        />
      </div>

      <!-- Período de Tiempo -->
      <div>
        <label class="text-sm block mb-1">Período de Tiempo</label>
        <div class="flex items-center gap-2">
          <Datepicker
            v-model="filters.start_date"
            class="w-full min-w-[152px]"
            showIcon
            dateFormat="yy/mm/dd"
          />
          <span class="text-gray-600">a</span>
          <Datepicker
            v-model="filters.end_date"
            class="w-full min-w-[152px]"
            showIcon
            dateFormat="yy/mm/dd"
          />
        </div>
      </div>

      <!-- Precio de Captación -->
      <div>
        <label class="text-sm block mb-1">Precio Captación</label>
        <div class="flex gap-2">
          <InputText
            v-model="filters.minimum_price"
            placeholder="Mínimo"
            type="number"
            class="w-full min-w-[100px] text-sm px-2 py-1 border border-gray-300 rounded-md"
          />
          <InputText
            v-model="filters.maximum_price"
            placeholder="Máximo"
            type="number"
            class="w-full min-w-[100px] text-sm px-2 py-1 border border-gray-300 rounded-md"
          />
        </div>
      </div>

    </div>
  </div>
</template>
<script setup>
//import { useMarketAnalisisStore } from "@/stores/marketAnalisis";
import Datepicker from "primevue/datepicker";
import InputText from "primevue/inputtext";
import { onMounted, ref, watch } from "vue";

import { subMonths } from 'date-fns';

const props = defineProps({
  options: Object, // Laravel Inertia pasará estos datos
});

  // Referencia al store
//  const marketAnalysisStore = useMarketAnalisisStore();

const today = new Date();
const sixMonthsAgo = subMonths(today, 6);
// Filtros para el formulario
	const filters = ref({
		status: [],
		start_date: sixMonthsAgo,
		end_date: today, // Fecha actual en formato YYYY-MM-DD
		minimum_price: null,
		maximum_price: null,
	});


  // Lista de estados obtenida del store
  const statuses = ref([]);
  const filteredStatuses = ref([]);

  // 🔥 Cargar datos desde `props.options`
const loadOptions = () => {
  if (props.options?.status_listing) {
    statuses.value = props.options.status_listing;

    // Filtrar solo los estados requeridos (Activa, Oferta/Reserva, Venta Aceptada/Vendida)
    filteredStatuses.value = statuses.value.filter((status) =>
      [2, 8, 7, 4, 6, 3].includes(status.id)
    );
  } else {
    console.error("No se encontraron estados en `status_listing`.");
  }
};

// Ejecutar la carga de opciones al montar
 loadOptions();
  const emit = defineEmits(["updateFilters"]);
 // Observa cambios en los filtros y emite el evento automáticamente
	watch(filters, () => {
	emit("updateFilters", { ...filters.value });
	}, { deep: true });


  // Cargar las opciones al montar el componente
  onMounted(() => {
	//fetchOptions();
    loadOptions();
    if (!filters.value.start_date) {
    filters.value.start_date = subMonths(new Date(), 6);
  }

  if (!filters.value.end_date) {
    filters.value.end_date = new Date();
  }

  });

</script>

<style scoped>
.step-one-container {
  max-width: 100%;
  padding: 20px;
  border-radius: 8px;
  background-color: #ffffff;
}

.form-title {
  text-align: left;
  font-size: 18px;
  margin-bottom: 20px;
  font-weight: bold;
}


.form-row {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 20px;
}

.form-group {
  flex: 1;
  min-width: 200px;
  max-width: 300px;
  margin: 0;
}

.form-group.align-top {
  align-self: flex-start;
}


.date-group {
  display: flex;
  align-items: center;
  gap: 10px;
}

.date-picker {
  width: 120px;
}

/* Estilo para la sección de precios */
.price-group {
  display: flex;
  gap: 10px;
}

.price-input {
  flex: 1;
  max-width: 100px;
}
/* Aplica estilo a los placeholders de los inputs */
input::placeholder {
  font-size: 10px; /* Tamaño más pequeño para los placeholders */
  color: #a0aec0; /* Color más claro opcional */
}

.text-xs::placeholder {
  font-size: 10px; /* Aplica este tamaño también para inputs con clase text-xs */
}
</style>
