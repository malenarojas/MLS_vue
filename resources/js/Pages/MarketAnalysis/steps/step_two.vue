<template>
  <div class="step-two-container">
		<div class="bg-gray-100 border-b border-gray-300 p-3 mb-6">
			<h2 class="text-xm font-semibold text-gray-800">
			  Paso 2: Especifique más detalles de la propiedad
			</h2>
		</div>
    <div class="form-row">
      <!-- Segmento del Mercado -->
      <div class="form-group">
        <label class="text-sm">Segmento del Mercado</label>
        <div class="radio-group">
          <div v-for="area in filteredAreas" :key="area.id">
            <RadioButton
              v-model="filters.market_segment"
              :inputId="'area-' + area.id"
              name="market-segment"
              :value="area.id"
            />
            <label class="text-sm" :for="'area-' + area.id">{{ area.name }}</label>
          </div>
        </div>
      </div>

      <!-- Tipo de Transacción -->
      <div class="form-group">
        <label class="text-sm">Tipo de Transacción</label>
        <div class="radio-group">
          <div v-for="transaction in filteredTransactions" :key="transaction.id">
            <RadioButton
              v-model="filters.transaction_type"
              :inputId="'transaction-' + transaction.id"
              name="transaction-type"
              :value="transaction.id"
            />
            <label class="text-sm" :for="'transaction-' + transaction.id">{{ transaction.name }}</label>
          </div>
        </div>
      </div>

      <!-- Tipo de Contrato -->
      <div class="form-group">
        <label class="text-sm" for="contract-type" >Tipo de Contrato</label>
        <Dropdown
          id="contract-type"
          v-model="filters.contract_type"
          :options="filteredContracts"
          optionLabel="name"
					option-value="id"
          placeholder="Seleccione un Tipo de Contrato"
          class="w-full text-sm"
					filter
        />
      </div>

      <!-- Estado de la Propiedad -->
      <div class="form-group">
        <label class="text-sm"   for="property-status">Estado de la Propiedad</label>
        <Dropdown
          id="property-status"
          v-model="filters.property_status"
          :options="filteredStatuses"
          optionLabel="name_state_properties"
					option-value="id"
          placeholder="Seleccione un Estado de Propiedad"
          class="w-full text-sm"
					filter
        />
      </div>
    </div>
  </div>
</template>
<script setup>
import Dropdown from "primevue/dropdown";
import RadioButton from "primevue/radiobutton";
import { computed, onMounted } from "vue";

const props = defineProps({
  filters: Object,
  options: Object,
});

// Computed filtrados
const filteredAreas = computed(() => props.options?.areas?.filter(area => [1, 2].includes(area.id)) || []);
const filteredTransactions = computed(() => props.options?.listing_transaccion_type || []);
const filteredContracts = computed(() => props.options?.contract_type || []);
const filteredStatuses = computed(() => props.options?.state_property || []);

// Setear valores por defecto si están vacíos
onMounted(() => {
  if (!props.filters.market_segment) props.filters.market_segment = 2;
  if (!props.filters.transaction_type) props.filters.transaction_type = 1;
});
</script>

<style scoped>
.step-two-container {
  max-width: 100%;
  padding: 20px;
  border-radius: 8px;
  background-color: #ffffff;
}

/* Título */
.form-title {
  text-align: left;
  font-size: 18px;
  margin-bottom: 20px;
  font-weight: bold;
}

/* Contenedor principal */
.form-row {
  display: flex;
  flex-wrap: nowrap; /* Asegura que los elementos estén en la misma fila */
  align-items: flex-start; /* Alinea los elementos en la parte superior */
  gap: 20px; /* Espacio entre los grupos */
  width: 100%;
}

/* Grupos de formulario */
.form-group {
  flex: 1; /* Asegura que cada grupo ocupe el mismo espacio proporcional */
  min-width: 200px; /* Ancho mínimo para evitar desbordamiento */
  max-width: 300px; /* Evita que crezcan demasiado */
}

/* Radio group y Dropdown */
.radio-group, .dropdown-group {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

/* Contenedor del botón */
.button-container {
  display: flex;
  justify-content: flex-end; /* Alinea el botón a la derecha */
  margin-top: 20px; /* Espaciado superior */
  width: 100%; /* Asegura que ocupe todo el ancho */
}

/* Contenedor del botón */
.button-group {
  display: flex;
  justify-content: flex-end; /* Alinea el botón a la derecha */
  align-items: center; /* Centra verticalmente */
  margin-top: 20px; /* Espaciado superior */
  width: 100%; /* Asegura que ocupe todo el ancho de la fila */
}

/* Estilo del botón */
.p-button-primary {
  background-color: #4caf50;
  color: white;
  padding: 10px 20px;
  border-radius: 5px;
}

</style>

