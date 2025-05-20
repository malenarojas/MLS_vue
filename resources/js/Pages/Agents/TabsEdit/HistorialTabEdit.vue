<template>
  <div class="audit-logs">
    <h2 class="text-2xl font-bold mb-4">Audit Logs</h2>

    <div v-if="isLoading" class="text-gray-600">Cargando...</div>
    <div v-else-if="error" class="text-red-500">{{ error }}</div>

    <div v-else class="overflow-x-auto max-h-96 border rounded-lg shadow-lg">
      <table class="table-auto w-full border-collapse bg-white">
        <!-- Cabecera fija -->
        <thead class="bg-blue-100 sticky top-0 z-10">
          <tr>
            <th class="px-4 py-2 border text-left text-sm font-semibold">Campo Editado</th>
            <th class="px-4 py-2 border text-left text-sm font-semibold">Valor Anterior</th>
            <th class="px-4 py-2 border text-left text-sm font-semibold">Valor Nuevo</th>
            <th class="px-4 py-2 border text-left text-sm font-semibold">Usuario</th>
            <th class="px-4 py-2 border text-left text-sm font-semibold">Fecha</th>
          </tr>
        </thead>

        <!-- Cuerpo de la tabla -->
        <tbody>
          <tr v-for="log in sortedLogs" :key="log.id" class="hover:bg-gray-50">
            <td class="px-4 py-2 border text-sm truncate-cell" :title="log.field_name">
              {{ log.field_name }}
            </td>
            <td class="px-4 py-2 border text-sm truncate-cell" :title="log.old_value">
              {{ log.old_value || '-' }}
            </td>
            <td class="px-4 py-2 border text-sm truncate-cell" :title="log.new_value">
              {{ log.new_value || '-' }}
            </td>
            <td class="px-4 py-2 border text-sm truncate-cell" :title="log.user_name || 'Desconocido'">
              {{ log.user_name || 'Desconocido' }}
            </td>
            <td class="px-4 py-2 border text-sm">
              {{ formatDate(log.created_at) }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref, computed } from 'vue';
import { fetchAuditLogsByAgent } from "@/src/services/AgentService"; // Ajusta la ruta según tu proyecto
//import { useRoute } from "vue-router";

// **Props con defineProps**
const props = defineProps({
  agentDataupdate: {
    type: Object,
    required: true,
  },
  options:{
    type: Object,
    required: true,
  },
  user:{
    type: Object,
    required: true,
  },
  role: {
    type: Object,
    required: true,
  },
  logs: {
    type: Object,
    required: true,
  },
});

//const route = useRoute();
//const agentId = route.query.agent_id;

const logs = ref([]);
const isLoading = ref(false);
const error = ref(null);

onMounted(() => {
  isLoading.value = true;
  try {
    logs.value = props.logs || []; // 👈 Aquí asignamos directamente los logs
    console.log('Estos son los logs:', logs.value);
  } catch (e) {
    error.value = "No se pudieron cargar los logs de auditoría.";
    console.error(e);
  } finally {
    isLoading.value = false;
  }
});


// Ordenar los logs por fecha (de más reciente a más antiguo)
const sortedLogs = computed(() => {
  return [...logs.value].sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
});

// Función para formatear la fecha de manera más legible
function formatDate(date) {
  if (!date) return "-";
  return new Intl.DateTimeFormat("es-BO", {
    year: "numeric",
    month: "2-digit",
    day: "2-digit",
    hour: "2-digit",
    minute: "2-digit",
    second: "2-digit",
    timeZone: "America/La_Paz",
  }).format(new Date(date));
}
</script>

<style scoped>
/* Hacer la tabla scrollable con cabecera fija */
.overflow-x-auto {
  overflow-x: auto;
  max-height: 400px;
}

/* Estilos adicionales para las celdas truncadas */
.truncate-cell {
  max-width: 200px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.audit-logs {
  max-width: 100%;
  margin: auto;
}

.table-auto {
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  overflow: hidden;
}

th {
  background-color: #f1f5f9;
  color: #374151;
}

td {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.truncate-cell {
  max-width: 200px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

tbody tr {
  transition: background-color 0.2s;
}

tbody tr:hover {
  background-color: #f9fafb;
}

thead th {
  position: sticky;
  top: 0;
  background-color: #f8fafc;
  z-index: 1;
}

.overflow-x-auto {
  overflow-x: auto;
  margin-top: 1rem;
}

.shadow-md {
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
</style>

