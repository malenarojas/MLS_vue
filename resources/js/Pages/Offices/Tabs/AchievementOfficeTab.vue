<template>
    <div v-if="form">
      <DataTable :value="achievementOffices" responsiveLayout="scroll">
        <!-- Columna de Logro -->
        <!-- Columna de Imagen del Logro -->
        <Column header="Imagen">
        <template #body="{ data }">
            <img
            v-if="data.image_url"
            :src="data.image_url.replace(/,$/, '')"
            alt="Imagen del logro"
            class="w-20 h-20 object-contain rounded shadow bg-white p-1"
            />
        </template>
        </Column>

        <Column field="name_achievements" header="Logro">
          <template #body="{ data }">
            {{ data.name_achievements }}
          </template>
        </Column>

        <!-- Columna de Habilitar logro -->
        <Column header="Habilitar">
          <template #body="{ data }">
            <Checkbox
              v-model="data.enable_achievement"
              binary
              :true-value="1"
              :false-value="0"
              @change="handleEnableChange(data)"
            />
          </template>
        </Column>

        <!-- Columna de Descripción -->
        <Column field="achievement_description" header="Descripción">
          <template #body="{ data }">
            {{ data.achievement_description }}
          </template>
        </Column>

        <!-- Columna de Fecha -->
        <Column header="Fecha del Logro">
          <template #body="{ data }">
            <Calendar
              v-model="data.achievement_date"
              :disabled="!data.enable_achievement"
              dateFormat="yy-mm-dd"
              showIcon
              @input="handleEnableChange(data)"
            />
          </template>
        </Column>
      </DataTable>
    </div>
  </template>

  <script setup>

  import { ref, watch } from 'vue';

  // Imports de PrimeVue
  import DataTable from 'primevue/datatable';
  import Column from 'primevue/column';
  import Calendar from 'primevue/calendar';
  import Checkbox from 'primevue/checkbox';
  const props = defineProps({
    form: {
      type: Object,
      required: true,
    },
    achievement: {
      type: Object,
      required: true,
    },
  });
 console.log("logros de achievement", props.achievement);
  // Definición de eventos
  const emit = defineEmits(["updateOfficeAchievements"]);

  // Donde guardamos los logros que mostramos en la tabla
  const achievementOffices = ref([props.achievement]);

  // Observamos si en `form` vienen logros de oficina (por ejemplo, `form.achievementOffice`)
  watch(
    () => props.form.achievementoffices,
    (newVal) => {
      if (Array.isArray(newVal) && newVal.length > 0) {
        // Ajusta la lógica para mapear al formato interno que necesites
        console.log("Logros de oficina recibidos:", newVal);
        achievementOffices.value = newVal.map((item) => {
        const defaultAchievement = (props.achievement || []).find(
            a => a.id === item.achievement_id
        ) || {};

        return {
            id: item.id,
            achievement_id: item.achievement_id,
            name_achievements: defaultAchievement.name_achievements || "Sin Nombre",
            achievement_description: defaultAchievement.achievement_description || "Sin Descripción",
            enable_achievement: item.enable_achievement ?? 0,
            achievement_date: item.achievement_date ?? null,
            image_url: defaultAchievement.image_url || null,
        };
        });
      } else {
        console.warn("No se encontraron logros en form.achievementOffice");
        achievementOffices.value = (props.achievement || []).map((item) => ({
            id: null,
            achievement_id: item.id ?? null, // el id del logro base
            name_achievements: item.name_achievements || "Sin Nombre",
            achievement_description: item.achievement_description || "Sin Descripción",
            enable_achievement: 0, // nuevo, así que por defecto deshabilitado
            achievement_date: null,
            image_url: item.image_url || null,// aún sin fecha
        }));
      }
    },
    { immediate: true }
  );

  function emitOfficeAchievements() {
    if (!Array.isArray(achievementOffices.value)) {
      console.error("achievementOffices no es un array válido:", achievementOffices.value);
      return;
    }
    const payload = achievementOffices.value.map((achievement) => ({
      id: achievement.id ?? null,
      achievement_id: achievement.achievement_id ?? null,
      enable_achievement: achievement.enable_achievement ?? 0,
      achievement_date: achievement.achievement_date ?? null,
      name_achievements: achievement.name_achievements || "Sin Nombre",
      achievement_description: achievement.achievement_description || "Sin Descripción",
      image_url: achievement.image_url || null,// aún sin fecha

    }));
    console.log("Payload de logros de oficina emitido:", payload);
    emit("updateOfficeAchievements", payload);
  }

  function handleEnableChange(data) {
    if (!data.enable_achievement) {
      data.achievement_date = null; // Limpiamos fecha si se desactiva
    }
    emitOfficeAchievements();
  }
  </script>

  <style scoped>
  /* Estilos opcionales */
  </style>
