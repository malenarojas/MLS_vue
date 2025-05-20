<template>
    <div class="office-audit-logs">
      <h2 class="text-2xl font-bold mb-4">Office Audit Logs</h2>

      <div v-if="isLoading" class="text-gray-600">Cargando...</div>
      <div v-else-if="error" class="text-red-500">{{ error }}</div>

      <!-- Contenedor principal de la tabla -->
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
            <tr
              v-for="log in sortedLogs"
              :key="log.id"
              class="hover:bg-gray-50"
            >
              <td
                class="px-4 py-2 border text-sm truncate-cell"
                :title="log.field_name"
              >
                {{ log.field_name }}
              </td>
              <td
                class="px-4 py-2 border text-sm truncate-cell"
                :title="log.old_value"
              >
                {{ log.old_value || '-' }}
              </td>
              <td
                class="px-4 py-2 border text-sm truncate-cell"
                :title="log.new_value"
              >
                {{ log.new_value || '-' }}
              </td>
              <td
                class="px-4 py-2 border text-sm truncate-cell"
                :title="log.user_name || 'Desconocido'"
              >
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
  /*
    Comentario en español:
    - `officeData`: datos de la oficina (si lo necesitas).
    - `logs`: array de objetos de auditoría, donde cada objeto tiene campos:
      {
        id,
        field_name,
        old_value,
        new_value,
        user_name,
        created_at,
        ... etc.
      }
  */
  import { ref, onMounted, computed } from 'vue';

  const props = defineProps({
    officeData: {
      type: Object,
      required: true,
    },
    logs: {
      type: Array,
      default: () => [],
    },
  });

  const isLoading = ref(false);
  const error = ref(null);
  const internalLogs = ref([]);

  // Al montar el componente, asignamos los logs al ref local
  onMounted(() => {
    isLoading.value = true;
    try {
      internalLogs.value = props.logs || [];
      console.log('Office Audit Logs:', internalLogs.value);
    } catch (e) {
      error.value = "No se pudieron cargar los logs de auditoría para la oficina.";
      console.error(e);
    } finally {
      isLoading.value = false;
    }
  });

  // Ordenamos los logs por fecha (más reciente primero)
  const sortedLogs = computed(() => {
    return [...internalLogs.value].sort(
      (a, b) => new Date(b.created_at) - new Date(a.created_at)
    );
  });

  // Formatear fecha a un estilo legible
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
  /* Ajustes de scroll + cabecera fija */
  .overflow-x-auto {
    overflow-x: auto;
    max-height: 400px;
  }

  /* Celdas truncadas (muestran '...' si es muy largo) */
  .truncate-cell {
    max-width: 200px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .office-audit-logs {
    max-width: 100%;
    margin: auto;
  }

  .table-auto {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    overflow: hidden;
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
  </style>
