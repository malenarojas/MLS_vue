<template>
  <div class="p-4 bg-white rounded-lg shadow-md w-full">
    <!-- Área Principal de Especialidad -->
    <div class="mb-4">
      <h5 class="text-blue-600 font-bold text-base mb-2">Área Principal de Especialidad</h5>
      <div class="grid gap-3">
        <div v-for="area in areas" :key="area.id" class="flex items-start gap-2">
          <RadioButton
            v-model="selectedArea"
            :inputId="`area_${area.id}`"
            :value="area.id"
            @change="loadSpecialties(area.specialities)"
          />
          <div class="w-full">
            <label :for="`area_${area.id}`" class="font-semibold text-gray-800 text-xs block">{{ area.name }}</label>
            <p class="text-gray-500 text-xs">{{ area.description }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Especialidades y Subespecialidades en una fila -->
    <div class="mb-4 flex flex-wrap gap-3">
      <div class="w-1/4">
        <h5 class="font-bold text-gray-800 text-sm">Especialidades</h5>
        <Select
          v-model="selectedSpeciality"
          :options="specialities"
          optionLabel="name"
          optionValue="id"
          placeholder="Seleccione"
          class="w-full text-xs p-1 border border-blue-400 rounded-md"
          @change="handleSpecialitySelection"
        />
      </div>

      <div class="w-1/4" v-if="subSpecialities.length > 0">
        <h5 class="font-bold text-gray-800 text-sm">Subespecialidades</h5>
        <Select
          v-model="selectedSubSpeciality"
          :options="subSpecialities"
          optionLabel="name"
          optionValue="id"
          placeholder="Seleccione"
          class="w-full text-xs p-1 border border-blue-400 rounded-md"
          @change="addSpecialityFromSub"
        />
      </div>
    </div>

    <!-- Especialidades Seleccionadas en contenedor azul -->
    <div class="mb-4 bg-blue-100 p-2 rounded-lg">
      <h5 class="font-bold text-gray-800 text-sm">Especialidades Seleccionadas</h5>
      <div v-if="selectedSpecialities.length > 0" class="flex flex-wrap gap-1 mt-1">
        <span
          v-for="(speciality, index) in selectedSpecialities"
          :key="index"
          class="px-2 py-1 bg-blue-500 text-white rounded-lg flex items-center gap-1 text-xs"
        >
          <span>{{ speciality.name }}</span>
          <button @click="removeSpeciality(speciality)" class="text-white text-xs">✕</button>
        </span>
      </div>
      <div v-else class="text-gray-500 text-xs">No se han seleccionado especialidades aún.</div>
    </div>

    <!-- Países de Interés -->
    <div>
      <h5 class="font-bold text-gray-800 text-sm">Países de Interés</h5>
      <Select
        v-model="selectedCountry"
        :options="availableCountries"
        optionLabel="name"
        placeholder="Seleccione"
        class="w-1/4 text-xs p-1 border border-blue-400 rounded-md mt-1"
        @change="addCountry"
      >
        <!-- Personalización del Dropdown -->
        <template #option="slotProps">
          <div class="flex items-center gap-1">
            <img :src="slotProps.option.flag" :alt="slotProps.option.name" class="w-5 h-3 rounded-md" />
            <span class="text-xs">{{ slotProps.option.name }}</span>
          </div>
        </template>
      </Select>

      <!-- Países seleccionados en contenedor azul -->
      <div class="bg-blue-100 p-2 rounded-lg mt-2">
        <div class="flex flex-wrap gap-1">
          <span v-for="(country, index) in selectedCountries" :key="index"
            class="px-2 py-1 bg-blue-500 text-white rounded-lg flex items-center gap-1 text-xs">
            <img :src="country.flag" :alt="country.name" class="w-5 h-3 rounded-md" />
            <span>{{ country.name }}</span>
            <button @click="removeCountry(country)" class="text-white text-xs">✕</button>
          </span>
        </div>
      </div>
    </div>
  </div>
</template>



<script setup>
//import { useAgentStore } from "@/stores/Agents";
import RadioButton from "primevue/radiobutton";
import Select from "primevue/select";
import { computed, ref } from "vue";
const props = defineProps({
  agentData: {
    type: Object,
    required: true,
  },
  options:{
    type: Object,
    required: true,
  },
});

// Store
//const agentStore = useAgentStore();

// Lista de países
const countries = ref([
  { name: "Bolivia", flag: "https://flagcdn.com/w40/bo.png" },
  { name: "Argentina", flag: "https://flagcdn.com/w40/ar.png" },
  { name: "Perú", flag: "https://flagcdn.com/w40/pe.png" },
  { name: "Brasil", flag: "https://flagcdn.com/w40/br.png" },
  { name: "México", flag: "https://flagcdn.com/w40/mx.png" },
]);

const selectedCountry = ref(null);
const selectedCountries = ref([]);
const availableCountries = computed(() => {
  return countries.value.filter(
    (country) =>
      !selectedCountries.value.some((selected) => selected.name === country.name)
  );
});

// Datos iniciales
const areas = computed(() => props.options.formOptions.areas || []);
const specialities = ref([]);
const subSpecialities = ref([]);

// Selecciones
const selectedArea = ref(null);
const selectedSpeciality = ref(null);
const selectedSubSpeciality = ref(null);

// Lista de especialidades seleccionadas
const selectedSpecialities = ref([]);


// Cargar especialidades al seleccionar un área (muestra siempre las padres de forma estática)
const loadSpecialties = (areaSpecialities) => {
  selectedSpecialities.value = [];
  specialities.value = areaSpecialities.map((speciality) => ({
    id: speciality.pivot.id,
    name: speciality.name,
    children: speciality.children || [],
  }));

  console.log(
    "Especialidades cargadas con IDs de pivot:",
    specialities.value.map((s) => s.id)
  );

  subSpecialities.value = []; // Reiniciar subespecialidades
  selectedSpeciality.value = null;
  selectedSubSpeciality.value = null;
};
const emit = defineEmits(['update-specialities']); // Asegúrate de definir los eventos permitidos

// Manejar selección de especialidad
const handleSpecialitySelection = () => {
  const selected = specialities.value.find(
    (speciality) => speciality.id === selectedSpeciality.value
  );

  if (selected) {
    // Si tiene hijos, cargar subespecialidades
    if (selected.children && selected.children.length > 0) {
      subSpecialities.value = selected.children;
    } else {
      subSpecialities.value = []; // No hay hijos, vaciar subespecialidades
    }

    if (!selectedSpecialities.value.some((s) => s.id === selected.id)) {
      selectedSpecialities.value.push({ id: selected.id, name: selected.name });
      emit(
        "updateAreaSpecialitiesUser",
        selectedSpecialities.value.map((s) => ({
          area_speciality_id: s.id,
          user_id: null,
        }))
      );
      console.log("Especialidad seleccionada:", selected.id	);
    }
  }
};

// Añadir subespecialidad seleccionada
const addSpecialityFromSub = () => {
  const selected = subSpecialities.value.find(
    (subSpeciality) => subSpeciality.id === selectedSubSpeciality.value
  );

  if (selected && !selectedSpecialities.value.some((s) => s.id === selected.id)) {
    selectedSpecialities.value.push({ id: selected.id, name: selected.name });
    emit(
      "updateAreaSpecialitiesUser",
      selectedSpecialities.value.map((s) => ({
        area_speciality_id: s.id,
        user_id: null,
      }))
    );
    console.log("Subespecialidad añadida:", selected.id);
  }
};
 // Manejar países seleccionados
 const addCountry = () => {
	if (selectedCountry.value) {
	  const exists = selectedCountries.value.some(
		(country) => country.name === selectedCountry.value.name
	  );

	  if (!exists) {
		selectedCountries.value.push(selectedCountry.value);
	  }

	  selectedCountry.value = null;
	}
  };

// Eliminar especialidad seleccionada
const removeSpeciality = (specialityToRemove) => {
  selectedSpecialities.value = selectedSpecialities.value.filter(
    (speciality) => speciality.id !== specialityToRemove.id
  );
  emit(
    "updateAreaSpecialitiesUser",
    selectedSpecialities.value.map((s) => ({
      area_speciality_id: s.id,
      user_id: null,
    }))
  );
  console.log("Especialidad eliminada:", specialityToRemove.id);
};
const removeCountry = (countryToRemove) => {
	selectedCountries.value = selectedCountries.value.filter(
	  (country) => country.name !== countryToRemove.name
	);
  };
</script>

<style scoped>


</style>