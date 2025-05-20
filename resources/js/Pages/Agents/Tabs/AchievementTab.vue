<template>
	<div v-if="agentData" >
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
			  indeterminate binary
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
			/>
		  </template>
		</Column>
	  </DataTable>

	</div>
</template>

<script setup>
import { achievements } from "@/src/interfaces/Achievements";
import Calendar from "primevue/calendar";
import Checkbox from "primevue/checkbox";
import Column from "primevue/column";
import DataTable from "primevue/datatable";
import { ref } from "vue";

// Nuevo array para manejar el estado de los logros
const achievementUsers = ref(
  achievements.map((achievement) => ({
	...achievement,
	enable_achievement: 0, // Por defecto deshabilitado
	achievement_date: null, // Sin fecha por defecto
  }))
);

// Función para manejar el cambio del checkbox
const handleEnableChange = (data) => {
  if (!data.enable_achievement) {
	// Si el checkbox se desactiva, limpiar la fecha
	data.achievement_date = null;
  }
  emitAchievements(); // Emitir cambios al array
};

// Función para preparar el array de logros a enviar
const emitAchievements = () => {
  const payload = achievementUsers.value.map((achievement) => ({
    id: achievement.achievement_id,
    enable_achievement: achievement.enable_achievement,
    achievement_date: achievement.achievement_date,
  }));

  emit("updateAchievements", payload); // Emitir los logros actualizados
};


const emit = defineEmits(["updateAchievements"]);

// Props
defineProps({
  agentData: {
	type: Object,
	required: true,
  },
});
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
