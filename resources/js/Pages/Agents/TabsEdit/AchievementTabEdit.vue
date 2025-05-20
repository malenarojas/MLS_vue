<template>
	<div v-if="agentDataupdate">
	  <DataTable :value="achievementUsers" responsiveLayout="scroll">
		<!-- Columna de Logro -->
		<Column field="name_achievements" header="Logro">
		  <template #body="{ data }">
			{{ data.name_achievements }}
		  </template>
		</Column>

		<!-- Columna de Habilitar logro -->
		<Column header="Habilitar logro">
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
        @change="handleEnableChange(data)"
			/>
		  </template>
		</Column>
	  </DataTable>
	</div>
</template>
<script setup>
import { achievements } from "@/src/interfaces/Achievements"; // ✅ Importa los logros desde la interfaz
import Calendar from "primevue/calendar";
import Checkbox from "primevue/checkbox";
import Column from "primevue/column";
import DataTable from "primevue/datatable";
import { ref, watch } from "vue";

// Props
const props = defineProps({
  agentDataupdate: {
	type: Object,
	required: true,
  },
  options: {
	type: Object,
	required: true,
  },
});
//console.log("logros de los agntes", props.options.Achievements)

// Emitir evento al padre
const emit = defineEmits(["updateAchievements"]);

// Inicializar `achievementUsers` con los logros importados
const achievementUsers = ref([...achievements]);

// Watch para actualizar los logros cuando cambie agentDataupdate
watch(
  () => props.agentDataupdate?.achievementuser,
  (newAchievements) => {
    if (Array.isArray(newAchievements) && newAchievements.length > 0) {
      console.log("estos son los que traiga de base datos",newAchievements);
      achievementUsers.value = newAchievements.map((achievement) => {
        // Buscar en la lista de achievements por defecto usando el id
        const defaultAchievement = achievements.find(a => a.achievement_id === achievement.achievement_id) || {};
        return {
          id: achievement.id,
          achievement_id: achievement.achievement_id,
          name_achievements: defaultAchievement.name_achievements || "Sin Nombre",
          achievement_description: defaultAchievement.achievement_description || "Sin Descripción",
          enable_achievement: achievement.enable_achievement || 0,
          achievement_date: achievement.achievement_date || '',
        };
      });

      console.log("✅ Logros cargados con nombres y descripciones:", achievementUsers.value);
    } else {
      // Si no hay logros en `agentDataupdate`, usa los importados desde `achievements`
      console.warn("agentDataupdate.achievement_user no es un array válido, usando valores importados.");
      achievementUsers.value = [...achievements];
    }
    console.log("achievement user  cargadas :")
  },
  { immediate: true } // ✅ Se ejecuta al cargar el componente
);

const emitAchievements = () => {
  if (!Array.isArray(achievementUsers.value) || achievementUsers.value.length === 0) {
    console.error("Error: achievementUsers no es un array válido o está vacío", achievementUsers.value);
    return;
  }

  const payload = achievementUsers.value.map((achievement) => ({
    id: achievement.id,
    achievement_id: achievement.achievement_id,
    enable_achievement: achievement.enable_achievement,
    achievement_date: achievement.achievement_date
  }));

  console.log("🚀 Payload emitido:", payload);
  emit("updateAchievements", payload);
};


const handleEnableChange = (data) => {
  if (!data.enable_achievement) {
    data.achievement_date = null;
  }
  emitAchievements(); // Emitir cambios en cada modificación
};


</script>


<style scoped>
.actions {
  margin-top: 1rem;
  text-align: right;
}

.actions button {
  padding: 0.5rem 1rem;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

table {
  border-collapse: collapse;
  width: 100%;
}

th,
td {
  border: 1px solid #ddd;
  padding: 8px;
}

th {
  background-color: #f4f4f4;
}
</style>
