<template>
<AppLayout title="Agents">
  <div class="bg-gradient-to-br from-blue-50 to-blue-100 min-h-screen p-4 w-full">
    <!-- Encabezado -->
    <div class="flex justify-between items-center mb-8">
      <div class="flex items-center space-x-4">
        <img src="@/assets/img/logo.png" alt="Logo" class="h-12 w-12">
        <h1 class="text-3xl font-bold text-blue-800">Agentes</h1>
      </div>
       <div class="flex space-x-2">

            <Select
            v-model="exportOption"
            :options="exportOptions"
            optionLabel="label"
            optionValue="value"
            placeholder="Seleccione una opción"
            class="w-60"
            />

        <!-- Botón para exportar -->
        <button
            class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold py-2 px-6 rounded-lg shadow-lg transition-all transform hover:scale-105"
            @click="handleExport"
        >
            <i class="pi pi-file-excel text-white text-lg mr-2"></i>
        </button>


      <button
        class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold py-2 px-6 rounded-lg shadow-lg transition-all transform hover:scale-105"
        @click="navigateToCreate"
      >
        Crear Nuevo
      </button>
    </div>
  </div>

    <div class="bg-white shadow-xl rounded-lg p-6 mb-8">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div>
          <label for="region" class="block text-blue-800 font-medium mb-2">Región:</label>
          <Select
            id="region"
            v-model="selectedRegion"
            :options="options.regions"
            optionLabel="name"
            optionValue="id"
            placeholder="Seleccione una Región"
            class="w-full border-2 border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
            @change="applyFilters"
          />
        </div>

        <div>
          <label for="office" class="block text-blue-800 font-medium mb-2">Oficina:</label>
          <Select
            id="office"
            v-model="selectedOffice"
            :options="filteredOffices"
            optionLabel="name"
            optionValue="office_id"
			filter
            placeholder="Seleccione una Oficina"
            class="w-full border-2 border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
            @change="applyFilters"
          />
        </div>

        <div>
          <label for="status" class="block text-blue-800 font-medium mb-2">Estado:</label>
          <Dropdown
            id="status"
            v-model="selectedStatus"
            :options="props.options.agent_status"
            optionLabel="name"
            optionValue="id"
            placeholder="Seleccione un Estado"
            class="w-full border-2 border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
            @change="applyFilters"
          />
        </div>

        <div>
          <label for="search" class="block text-blue-800 font-medium mb-2">Buscar:</label>
          <div class="flex items-center bg-white border-2 border-blue-200 rounded-lg shadow-sm focus-within:ring-2 focus-within:ring-blue-500 transition-all">
						<input
							type="text"
							id="search"
							placeholder="Buscar agente..."
							class="py-2 px-4 rounded-l-lg w-full focus:outline-none"
							v-model="searchInput"
							@input="debouncedApplyFilters"
						/>
            <button class="px-4 text-blue-500 hover:text-blue-700 transition-colors">
              <i class="pi pi-search"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
    <div class="bg-white shadow-xl rounded-lg overflow-x-auto">
      <DataTable
      :value="props.agents.data"
        paginator
        :rows="perPage"
        :rowsPerPageOptions="[10, 20, 30, 40, 50,60,70,80,90,100]"
        v-model:rows="perPage"
        @update:rows="changePerPage"
        class="w-full"
      >
        <Column field="user.name_to_show" header="Nombre Completo">
          <template #body="{ data }">
            <div class="flex items-center space-x-3">
              <img
                class="w-10 h-10 rounded-full border-2 border-blue-200"
                :src="data?.image_url ||  defaultImage"
                alt="Foto de perfil"
              />
              <div>
                <span
                  class="text-blue-600 hover:text-blue-800 hover:underline cursor-pointer font-medium transition-colors"
                  @click="viewAgentDetails(data.id)"
                >
                  {{ data.user?.name_to_show || 'Sin Nombre' }}
                </span>
                <div class="text-gray-600 text-sm">
                  {{ data.user?.email || 'Sin Correo' }}
                </div>
              </div>
            </div>
          </template>
        </Column>
        <!-- Calidad -->
        <Column header="Calidad">
          <template #body="{ data }">
             <div class="flex justify-center items-center">
              <div
                class="relative inline-flex items-center justify-center w-10 h-10 rounded-full border-4"
                :class="{
                  'border-green-500': calculateQuality(data) >= 80,
                  'border-yellow-300': calculateQuality(data) >= 50 && calculateQuality(data) < 80,
                  'border-red-500': calculateQuality(data) < 50,
                }"
              >
                <span class="text-sm font-medium text-gray-700">
                  {{ calculateQuality(data) }}%
                </span>
              </div>
            </div>
          </template>
        </Column>
			 <Column header="Estado">
				<template #body="{ data }">
					<div class="flex items-center">
						<div
							class="h-2.5 w-2.5 rounded-full me-2"
							:class="{
								'bg-green-500': data.agent_status_id === 1,  // Activo
								'bg-red-500': data.agent_status_id === 2,    // Inactivo
								'bg-blue-500': data.agent_status_id === 3,   // Aprobado
								'bg-yellow-500': data.agent_status_id === 4, // Pendiente
								'bg-gray-500': data.agent_status_id === 5    // Rechazado
							}"
						></div>
						<span :class="{
							'text-green-600': data.agent_status_id === 1,
							'text-red-600': data.agent_status_id === 2,
							'text-blue-600': data.agent_status_id === 3,
							'text-yellow-600': data.agent_status_id === 4,
							'text-gray-600': data.agent_status_id === 5
						}">
							{{ getStatusText(data.agent_status_id) }}
						</span>
					</div>
				</template>
			</Column>
        <!-- Comisión -->
        <Column header="Comisión">
          <template #body="{ data }">
            <span class="text-blue-600 font-medium">
              {{ data.commission_percentage || 0 }}%
            </span>
          </template>
        </Column>

        <!-- Antigüedad -->
        <Column header="Antigüedad en Remax">
          <template #body="{ data }">
            <span class="text-gray-700">
              {{ calculateTenure(data.user?.remax_start_date) }}
            </span>
          </template>
        </Column>

        <!-- Acciones -->
        <Column header="Acciones">
  <template #body="{ data }">
    <div class="flex items-center space-x-2">
      <!-- Botón de editar -->
      <a
        :href="`/agents/edit?agent_id=${data.id}`"
        class="text-blue-500 hover:text-blue-700 transition-colors"
      >
        <i class="pi pi-pencil"></i>
      </a>

      <!-- Botón de eliminar -->
      <!-- Botón de eliminar -->
        <div class="relative" v-if="roleName === 'Administrador'">
        <button
            class="text-red-500 hover:text-red-700 transition-colors"
            @click="deleteAgent(data.id)"
            @mouseover="showTooltip = true"
            @mouseleave="showTooltip = false"
        >
            <i class="pi pi-trash text-lg"></i>
        </button>
        </div>
    </div>
  </template>
</Column>

      </DataTable>
    </div>

    <AgentDetailModal
        v-if="showModal && selectedAgent"
        :agent="selectedAgent"
        :showModal="showModal"
        @update:showModal="closeModal"
        />
  </div>

</AppLayout>
</template>

<script setup>
import apiClient from "@/src/constansts/axiosClient";
import defaultImage from '@/assets/img/default.gif';
import AgentDetailModal from '@/Components/show.vue';
import AppLayout from "@/Layouts/AppLayout.vue";
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { useToast } from "primevue";
import debounce from 'lodash/debounce';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { useIsLoadingStore } from "@/Stores/isLoadingCharts";
const loadingStore = useIsLoadingStore();
const toast = useToast();
const props = defineProps({
  agents: Array,
  options: Object,
  user: Object,
  role: String,
  filters: Object
});
const filteredAgents = ref(props.agents || []);

watch(() => props.agents.data, (newAgents) => {
  console.log('🟢 Datos de agentes actualizados:', newAgents);
}, { immediate: true });

const perPage = ref(props.agents.meta?.per_page || 10);
const options = ref(props.options || {});
const roleName = ref(props.role|| {});
console.log("este es el rol del usuario",roleName.value);

const userOfficeId = ref(null);

const getUserRoleFromProps = () => {
  if (typeof roleName.value === 'string') {
    console.log("🎯 Rol recibido:", roleName.value);
    const normalizedRole = roleName.value.trim().toLowerCase();

    if (normalizedRole === 'administrador') {
    } else if (normalizedRole === 'broker') {
    } else {
    }
  } else {
    console.warn("Rol no es un string válido:", roleName.value);
  }
};


const getUserOfficeFromProps = () => {
  const user = props.user;

  if (user?.agent) {
    userOfficeId.value = user.agent.office_id || null;
    console.log("🏢 Oficina del usuario desde props:", userOfficeId.value);

    const normalizedRole = (roleName.value || '').trim().toLowerCase();
    if (normalizedRole === 'broker') {
      selectedOffice.value = userOfficeId.value;
      console.log("🔒 Oficina fijada por ser broker:", selectedOffice.value);
    }
  } else {
    console.warn("No se encontró la oficina del usuario en los props.");
  }
};
const selectedRegion = ref(null);
const selectedOffice = ref(null);
const searchInput = ref(null);
const selectedStatus = ref(1);
const activeAgentId = ref(null);
onMounted(async () => {
	getUserRoleFromProps();
    getUserOfficeFromProps();
    await applyFilters();
    selectedOffice
});

const filteredOffices = computed(() => {
  const offices = props.options.offices || [];

  if (!Array.isArray(offices)) {
    console.error(" `offices` no es un array:", offices);
    return [];
  }
  const normalizedRole = (roleName.value || '').toLowerCase();

  console.log("📋 Filtrando oficinas con:", {
    allOffices: offices,
    userOfficeId: userOfficeId.value,
    roleName: normalizedRole,
  });

  if ( normalizedRole === 'broker') {
    console.log("oficcinas encontradas",offices);
    console.log("esta es la oficina del broker",userOfficeId.value);
    return offices.filter(office => office.office_id === userOfficeId.value);

  }

  if (normalizedRole === 'administrador') {
    console.log("oficcinas encontradas",offices);
    loadFiltersFromLocalStorage();
    return offices;
  }
});

const calculateTenure = (startDate) => {
  if (!startDate) return 'Sin fecha';
  const start = new Date(startDate);
  const today = new Date();

  const years = today.getFullYear() - start.getFullYear();
  const months = today.getMonth() - start.getMonth();
  const days = today.getDate() - start.getDate();

  let adjustedYears = years;
  let adjustedMonths = months;
  let adjustedDays = days;

  if (adjustedDays < 0) {
    adjustedDays += new Date(today.getFullYear(), today.getMonth(), 0).getDate();
    adjustedMonths--;
  }
  if (adjustedMonths < 0) {
    adjustedMonths += 12;
    adjustedYears--;
  }

  return `${adjustedYears} años ${adjustedMonths} meses ${adjustedDays} días`;
};
const saveFiltersToLocalStorage = () => {
  const filters = {
    region_id: selectedRegion.value,
    office_id: selectedOffice.value,
    agent_status_id: selectedStatus.value,
    search: searchInput.value,
  };
  localStorage.setItem("agentFilters", JSON.stringify(filters));
};

const loadFiltersFromLocalStorage = () => {
  const storedFilters = localStorage.getItem("agentFilters");
  if (storedFilters) {
    const filters = JSON.parse(storedFilters);
    selectedRegion.value = filters.region_id || null;
    selectedOffice.value = filters.office_id || null;
    selectedStatus.value = filters.agent_status_id || 1;
    searchInput.value = filters.search || "";
  }
};

function getStatusText(statusId) {
  switch (statusId) {
    case 1:
      return "Activo";
    case 2:
      return "Inactivo";
    case 3:
      return "Aprobado";
    case 4:
      return "Pendiente";
    case 5:
      return "Rechazado";
    default:
      return "Desconocido";
  }
}
const debouncedApplyFilters = debounce(async () => {
  saveFiltersToLocalStorage(); // Guardar filtros antes de filtrar
  await applyFilters();
}, 300)

const applyFilters = () => {
  console.log('🟡 Aplicando filtros a agentes', {
    agent_status_id: selectedStatus.value,
    region_id: selectedRegion.value,
    office_id: selectedOffice.value,
    search: searchInput.value,
  });

  router.get(route('agents.index'), {
    agent_status_id: selectedStatus.value,
    region_id: selectedRegion.value,
    office_id: selectedOffice.value,
    search: searchInput.value,
    per_page: perPage.value
  }, {
    preserveScroll: true,
    preserveState: true,
    replace: true,
  });
};


// Función para cambiar items por página
const changePerPage = () => {
  applyFilters(); // Vuelve a la primera página con el nuevo tamaño
};
const calculateQuality = (agent) => {
  const userFields = [
    agent.user?.first_name,
    agent.user?.middle_name,
    agent.user?.last_name,
    agent.user?.name_to_show,
    agent.user?.ci,
    agent.user?.gender,
    agent.user?.phone_number,
    agent.user?.email,
    agent.user?.url,
    agent.user?.birthdate,
    agent.user?.remax_start_date,
    agent.user?.username,
    agent.user?.user_type_id,
    agent.user?.remax_title_id,
    agent.user?.remax_title_to_show_id,
    agent.user?.team_status_id,
    agent.user?.customer_preference_id,
  ];

  const agentFields = [
    agent.date_joined,
    agent.date_termination,
    agent.agent_status,
    agent.studies,
    agent.additional_education,
    agent.image_name,
    agent.previous_occupation,
    agent.license_type,
    agent.license_number,
    agent.expiration_date_license,
    agent.marketing_slogan,
    agent.meta_tag_description,
    agent.bullet_point_one,
    agent.bullet_point_two,
    agent.bullet_point_three,
  ];

  const fieldsToCheck = [...userFields, ...agentFields];
  const completedFields = fieldsToCheck.filter(
    (field) => field && field !== ""
  ).length;

  const totalFields = fieldsToCheck.length;
  return Math.round((completedFields / totalFields) * 100);
};


// Limpiar debounce cuando el componente se desmonta
onBeforeUnmount(() => {
  debouncedApplyFilters.cancel();
});

// **Actualizar `localStorage` cuando cambien los filtros**
watch([selectedRegion, selectedOffice, selectedStatus, searchInput], () => {
  saveFiltersToLocalStorage();
});


const showModal = ref(false);
const selectedAgent = ref(null);
const selectedAgentId = ref(null);

const viewAgentDetails = async (id) => {
  try {
    const response = await axios.get(`/agents/${id}`);
    selectedAgent.value = response.data;
    showModal.value = true;
    activeAgentId.value = id;
  } catch (error) {
    console.error("❌ Error al cargar detalles del agente:", error);
  }
};


const closeModal = () => {
	activeAgentId.value = null; // Resetea el modal activo
	selectedAgent.value = null; // Limpia el agente seleccionado

};


async function navigateToCreate() {
  try {
    router.visit('/agents/create', {
      method: 'get',
      preserveState: true,
      replace: true,
    });
  } catch (error) {
    console.error("Error al navegar a la creación de agente:", error);
  }
}

/*
*/
const exportOption = ref();

const exportOptions = computed(() => {
    const normalizedRole = roleName.value.trim().toLowerCase();
    if (normalizedRole === 'broker') {
    return [
      { label: 'Exportar filtro actual', value: 'filtered' }
    ];
  } else {
    return [
      { label: 'Exportar filtro actual', value: 'filtered' },
      { label: 'Exportar todos los agentes', value: 'all' },
      { label: 'Exportar agentes activos', value: 'active' },
      { label: 'Exportar agentes inactivos', value: 'inactive' }
    ];
  }
});
function handleExport() {
  if (exportOption.value === 'filtered') {
    exportAgents(); // sigue llamando el export del filtro
  } else {
    exportAgentsByStatus(exportOption.value);
  }
}


// Funciones existentes
const exportAgents = () => {
  const agentIds = props.agents.data.map(agent => agent.id);

  if (agentIds.length === 0) {
    toast.add({
      severity: 'warn',
      summary: 'Sin agentes',
      detail: 'No hay agentes para exportar',
      life: 3000
    });
    return;
  }

  const query = new URLSearchParams({
    agent_ids: agentIds.join(',')
  }).toString();

  loadingStore.setLoading(true);

  apiClient.get(`/agents/export-excel?${query}`, {
    responseType: 'blob',
    timeout: 60000,
  }).then(response => {
    loadingStore.setLoading(false);
    const blob = new Blob([response.data], {
      type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    });

    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', 'agentes_exportados.xlsx');
    document.body.appendChild(link);
    link.click();
    link.remove();
  }).catch(error => {
    loadingStore.setLoading(false);
    console.error(' Error al exportar agentes:', error);
  });
};

const exportAgentsByStatus = (status) => {
  loadingStore.setLoading(true);

  const query = new URLSearchParams({
    status: status, // aquí mandamos el parámetro
  }).toString();

  const filename = {
    all: 'todos_los_agentes.xlsx',
    active: 'agentes_activos.xlsx',
    inactive: 'agentes_inactivos.xlsx',
  }[status] || 'agentes_exportados.xlsx';

  apiClient.get(`/agents/export-exceltoday?${query}`, {
    responseType: 'blob',
    timeout: 60000,
  }).then(response => {
    loadingStore.setLoading(false);
    const blob = new Blob([response.data], {
      type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    });

    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', 'agentes_exportados.xlsx');
    document.body.appendChild(link);
    link.click();
    link.remove();
  }).catch(error => {
    loadingStore.setLoading(false);
    console.error('Error al exportar agentes:', error);
  });
};

async function deleteAgent(agentId) {
  if (!agentId) {
    console.error('❌ ID de agente no válido para eliminar.');
    return;
  }

  if (!confirm('¿Estás seguro que quieres eliminar este agente?')) {
    return; // Canceló
  }

  try {
    loadingStore.setLoading(true);

    const response = await apiClient.delete(`/agents/${agentId}`);

    toast.add({
      severity: 'success',
      summary: 'Eliminado',
      detail: 'Agente eliminado correctamente',
      life: 3000
    });

    // Refrescar la tabla después de eliminar
    applyFilters();

  } catch (error) {
    console.error('❌ Error al eliminar agente:', error);

    // 🔥 Capturar el mensaje del error real
    let errorMessage = 'No se pudo eliminar el agente';
    if (error.response && error.response.data && error.response.data.message) {
      errorMessage = error.response.data.message;
    } else if (error.message) {
      errorMessage = error.message;
    }

    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: errorMessage, // 🔥 Mostramos el error real
      life: 5000
    });
  } finally {
    loadingStore.setLoading(false);
  }
}




/*const exportAgentstoday = () => {
  loadingStore.setLoading(true);

  apiClient.get(`/agents/export-exceltoday`, {
    responseType: 'blob',
    timeout: 60000,
  }).then(response => {
    loadingStore.setLoading(false);
    const blob = new Blob([response.data], {
      type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    });

    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', 'agentes_exportados.xlsx');
    document.body.appendChild(link);
    link.click();
    link.remove();
  }).catch(error => {
    loadingStore.setLoading(false);
    console.error('Error al exportar agentes:', error);
  });
};*/
</script>
