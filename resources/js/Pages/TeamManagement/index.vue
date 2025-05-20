<template>
    <AppLayout title="Team Management">
      <div class="bg-gradient-to-br from-blue-50 to-blue-100 min-h-screen p-4 w-full">
        <!-- Header y Controles en una sola fila -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
          <div class="flex items-center space-x-4 w-full">
            <!-- Logo y Título -->
            <img src="@/assets/img/logo.png" alt="RE/MAX Logo" class="h-12 w-12">
            <h1 class="text-3xl font-bold text-blue-800"> Team Management </h1>

            <!-- Controles en línea con labels al lado -->
            <div class="flex items-center ml-auto space-x-4">
              <!-- Selector de Oficina con label al lado -->
              <div class="flex items-center">
                <label for="office" class="text-blue-800 font-medium mr-2 whitespace-nowrap">Oficina:</label>
                <Select
                  id="office"
                  v-model="selectedOffice"
                  :options="props.offices"
                  optionLabel="name"
                  optionValue="office_id"
                  filter
                  placeholder="Seleccione"
                  class="min-w-[180px] border-2 border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                  @change="applyFilters"
                />
              </div>
              <!-- Buscador con label al lado -->
              <div class="flex items-center">
            <label for="search" class="text-blue-800 font-medium mr-2 whitespace-nowrap">Buscar team:</label>
            <div class="flex items-center bg-white border-2 border-blue-200 rounded-lg shadow-sm focus-within:ring-2 focus-within:ring-blue-500 transition-all w-[160px]">
                <input
                type="text"
                id="search"
                placeholder="..."
                class="py-2 px-2 w-[100px] focus:outline-none"
                v-model="searchInput"
                @input="debouncedApplyFilters"
                />
                <button class="px-2 text-blue-500 hover:text-blue-700 transition-colors">
                <i class="pi pi-search"></i>
                </button>
            </div>
            </div>

              <!-- Botón Crear Nuevo con grosor normal -->
              <!-- Reemplazá el <Link> por este botón si querés que sea modal en lugar de navegar -->
            <button
            class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition-colors whitespace-nowrap"
            @click="openCreateModal"
            >
            Crear Nuevo Team
            </button>

            </div>
          </div>
        </div>
        <!-- Tabla de Equipos -->
        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
        <!-- Encabezado -->
        <DataTable
            :value="props.teams.data"
            paginator
            :rows="10"
            :totalRecords="props.teams.total"
            lazy
            :first="(props.teams.current_page - 1) * 10"
            stripedRows
            responsiveLayout="scroll"
            class="shadow-xl rounded-lg overflow-hidden text-sm"
            >
            <!-- Nombre del equipo -->
            <Column header="name">
            <template #body="slotProps">
                {{ slotProps.data.name }}
            </template>
            </Column>


            <!-- Oficina -->
            <Column header="Oficina">
            <template #body="slotProps">
                {{ slotProps.data.office?.name ?? 'Sin oficina' }}
            </template>
            </Column>

            <!-- Líder -->
            <Column header="Líder Equipo">
            <template #body="slotProps">
                <div class="flex items-center gap-2">
                <img
                    :src="slotProps.data.leader?.[0]?.image_url || '/images/avatar-placeholder.png'"
                    class="h-8 w-8 rounded-full object-cover"
                    alt="Líder"
                />
                <span>{{ slotProps.data.leader?.[0]?.user?.name_to_show ?? 'Sin líder' }}</span>
                </div>
            </template>
            </Column>

            <!-- Miembros -->
            <Column field="member_count" header="Miembros" class="text-center" sortable>
            <template #body="slotProps">
                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">
                {{ slotProps.data.members_count }}
                </span>
            </template>
            </Column>

            <!-- Listados -->
            <Column field="listing_count" header="Listados" class="text-center" sortable />

            <!-- Shortlink -->
            <Column header="Shortlink">
            <template #body="slotProps">
                <a
                v-if="slotProps.data.shortlink"
                :href="'/' + slotProps.data.shortlink"
                class="text-blue-500 hover:underline"
                >
                /{{ slotProps.data.shortlink }}
                </a>
                <span v-else class="text-gray-400 italic">Sin shortlink</span>
            </template>
            </Column>
            <Column header="Acciones" style="min-width: 120px;">
            <template #body="slotProps">

                    <a
                    :href="`/teammanagement/edit?team_id=${slotProps.data.id}`"
                    class="text-blue-500 hover:text-blue-700 transition-colors"
                    title="Editar equipo"
                    >
                    <i class="pi pi-pencil"></i>
                    </a>
            </template>
            </Column>
        </DataTable>
        </div>
      </div>
      <Dialog
        v-model:visible="showCreateModal"
        modal
        header="Crear Nuevo Team"
        :style="{ width: '500px' }"
        :breakpoints="{ '960px': '75vw', '640px': '90vw' }"
        >
  <div class="p-4 space-y-4">
    <!-- Nombre del equipo -->
    <div>
      <label class="block text-gray-700 font-medium mb-1">Nombre Equipo</label>
      <InputText
        v-model="form.name"
        class="w-full border-2 border-gray-200 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        placeholder="Ej: Team Elite"
        :class="{ 'p-invalid': form.errors.name }"
      />
      <small v-if="form.errors.name" class="p-error">{{ form.errors.name }}</small>
    </div>

    <!-- Oficina -->
    <div>
      <label class="block text-gray-700 font-medium mb-1">Oficina</label>
      <Select
        v-model="form.office_id"
        :options="props.offices"
        optionLabel="name"
        optionValue="office_id"
        placeholder="Seleccione"
        class="w-full border-2 border-gray-200 rounded-lg"
        :class="{ 'p-invalid': form.errors.office_id }"
      />
      <small v-if="form.errors.office_id" class="p-error">{{ form.errors.office_id }}</small>
    </div>

    <!-- Líder del equipo -->
    <div>
      <label class="block text-gray-700 font-medium mb-1">Líder Equipo</label>
      <Select
        v-model="form.agent_id"
        :options="agents"
        optionLabel="name"
        optionValue="id"
        placeholder="No seleccionado"
        class="w-full border-2 border-gray-200 rounded-lg"
        :class="{ 'p-invalid': form.errors.leader_id }"
      />
      <small v-if="form.errors.leader_id" class="p-error">{{ form.errors.leader_id }}</small>
    </div>
  </div>

  <template #footer>
    <div class="flex justify-end space-x-4">
      <Button
        label="Cancelado"
        icon="pi pi-times"
        @click="closeCreateModal"
        class="p-button-text"
        :disabled="form.processing"
      />
      <Button
        label="Guardar"
        icon="pi pi-save"
        @click="submitForm"
        class="bg-blue-500 hover:bg-blue-600"
        :loading="form.processing"
      />
    </div>
  </template>
</Dialog>

    </AppLayout>
  </template>

  <script setup>
  import { ref,watch } from 'vue';
  import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';
import Select from 'primevue/select';
import { debounce } from 'lodash';
import { useForm } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
const showCreateModal = ref(false);
import { useToast } from 'primevue/usetoast';
const toast = useToast();
const props = defineProps({
  offices: Array,
  teams: Array,
  //agents: Array,
  selectedOffice: [Number, String, null]
});
console.log("teams traidos ", props.teams);

console.log("officionas traidas ", props.offices);
const agents = ref([]);
const teams = ref([props.teams.data || []]);

const form = useForm({
  name: '',
  office_id: null,
  agent_id: null,
});

watch(() => form.office_id, async (newOfficeId) => {
  if (newOfficeId) {
    try {
      const response = await axios.get(route('teammanagement.agents'), {
        params: { office_id: newOfficeId }
      });

      console.log('🎯 Agentes cargados:', response.data.agents);
      agents.value = response.data.agents;
    } catch (error) {
      console.error('Error al cargar agentes:', error);
    }
  }
});
watch(() => props.agents, (newAgents) => {
  agents.value = newAgents;
});


watch(() => form.leader_id, (newLeaderId) => {
  console.log('🟢 Cambio detectado en leader_id (agente líder):', newLeaderId);
});

// Abrir el modal
const openCreateModal = () => {
  form.reset();
  showCreateModal.value = true;
};

// Cerrar el modal
const closeCreateModal = () => {
  showCreateModal.value = false;
  form.reset();
  form.clearErrors();
};

const selectedOffice = ref(null);
const searchInput = ref('');
const offices = ref(props.offices || []);

const debouncedApplyFilters = debounce(() => {
  applyFilters();
}, 500);

const applyFilters = () => {
  // Lógica para aplicar filtros
  console.log('Filtros aplicados:', {
    office: selectedOffice.value,
    search: searchInput.value
  });
};
  // Sample data - in a real app this would come from props or API

  const submitForm = () => {
  form.post(route('teammanagement.store'), {
    preserveScroll: true,
    onSuccess: () => {
      closeCreateModal(); // Cierra el modal
      toast.add({
        severity: 'success',
        summary: 'Éxito',
        detail: 'Equipo creado correctamente',
        life: 3000
      });
    },
    onError: () => {
        const firstError = Object.values(form.errors)[0] || 'Ocurrió un error al guardar el equipo.';
      toast.add({
        severity: 'error',
        summary: 'Error al guardar',
        detail: firstError,
        life: 4000
      });
    }
  });
};

  </script>

  <style scoped>
  /* Custom styles if needed */
  </style>
