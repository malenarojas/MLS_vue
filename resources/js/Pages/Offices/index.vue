<template>
    <Toast />
    <AppLayout title="Oficinas">
      <div class="bg-gradient-to-br from-blue-50 to-blue-100 min-h-screen p-4 w-full">
        <!-- Encabezado completo en una sola fila -->
        <div class="flex flex-wrap lg:flex-nowrap items-center justify-between gap-4 mb-8 bg-white shadow-xl rounded-lg p-6">
        <!-- Logo y Título -->
        <div class="flex items-center space-x-4">
            <img src="@/assets/img/logo.png" alt="Logo" class="h-12 w-12">
            <h1 class="text-3xl font-bold text-blue-800">Oficinas</h1>
        </div>

        <!-- Filtros y botón -->
        <div class="flex flex-wrap lg:flex-nowrap items-center gap-4">
            <!-- Estado -->
            <div class="flex items-center">
            <label for="status" class="text-blue-800 font-medium mr-2">Estado:</label>
            <Select
                id="status"
                v-model="selectedStatus"
                :options="[
                { id: 'all', name: 'Todos' },
                { id: 1, name: 'Activa' },
                { id: 0, name: 'Inactiva' }
                ]"

                optionLabel="name"
                optionValue="id"
                placeholder="Seleccione"
                class="w-48 border-2 border-blue-200 rounded-lg"
                @change="applyFilters"
            />
            </div>

            <!-- Buscador -->
            <div class="flex items-center">
            <label for="search" class="text-blue-800 font-medium mr-2">Buscar:</label>
            <div class="flex items-center bg-white border-2 border-blue-200 rounded-lg shadow-sm focus-within:ring-2 focus-within:ring-blue-500 transition-all">
                <input
                type="text"
                id="search"
                placeholder="Buscar oficina..."
                class="py-2 px-4 w-48 focus:outline-none"
                v-model="searchInput"
                @input="debouncedApplyFilters"
                />
                <button class="px-4 text-blue-500 hover:text-blue-700 transition-colors">
                <i class="pi pi-search"></i>
                </button>
            </div>
            </div>

            <!-- Botón Crear -->
            <button
            class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold py-2 px-6 rounded-lg shadow-lg transition-all transform hover:scale-105"
            @click="showCreateModal = true"
            >
            Crear Nueva Oficina
            </button>
        </div>
        </div>

        <!-- Tabla -->
        <div class="bg-white shadow-xl rounded-lg overflow-x-auto">
          <DataTable
            :value="offices"
            :rows="10"
            paginator
            :rowsPerPageOptions="[10, 20, 50]"
            responsiveLayout="scroll"
            class="w-full"
          >
          <!-- Imagen -->

            <!-- Identificación -->
            <Column field="id" header="Identificación" sortable>
              <template #body="{ data }">
                <span class="font-medium text-blue-600">{{ data.office_id }}</span>
              </template>
            </Column>

            <!-- Nombre Oficina -->
            <Column field="name" header="Nombre Oficina" sortable>
              <template #body="{ data }">
                <span class="font-medium text-gray-800 hover:text-blue-600 cursor-pointer transition-colors">
                  {{ data.name }}
                </span>
              </template>
            </Column>

            <!-- Estado -->
            <Column field="active_office" header="Estado" sortable>
            <template #body="{ data }">
                <div class="flex items-center">
                <div
                    class="h-2.5 w-2.5 rounded-full me-2"
                    :class="{
                    'bg-green-500': data.active_office === 1,
                    'bg-red-500': data.active_office === 0
                    }"
                ></div>
                <span :class="{
                    'text-green-600': data.active_office === 1,
                    'text-red-600': data.active_office === 0
                }">
                    {{ data.active_office === 1 ? 'Activa' : 'Inactiva' }}
                </span>
                </div>
            </template>
            </Column>

            <!-- Provincia -->
            <Column field="province" header="Provincia" sortable>
              <template #body="{ data }">
                <span class="text-gray-700">{{ data.province?.name || 'Sin provincia'  }}</span>
              </template>
            </Column>

            <!-- Ciudad -->
            <Column field="city" header="Ciudad" sortable>
              <template #body="{ data }">
                <span class="text-gray-700">{{ data.city?.name || 'Sin ciudad' }}</span>
              </template>
            </Column>

            <!-- Acciones -->
            <Column header="Acciones">
              <template #body="{ data }">
                <div class="flex items-center space-x-2">
                  <!-- Botón de editar -->
                  <!-- Botón de editar oficina -->
                    <a :href="`/offices/edit?id=${data.id}`" class="text-blue-500 hover:text-blue-700 transition-colors">
                    <i class="pi pi-pencil"></i>
                    </a>

                </div>
              </template>
            </Column>
          </DataTable>
        </div>
      </div>
      <!-- Modal para crear nueva oficina -->
      <Dialog
        v-model:visible="showCreateModal"
        modal
        header="Crear nueva oficina"
        :style="{ width: '500px' }"
        :breakpoints="{ '960px': '75vw', '640px': '90vw' }"
      >
        <div class="p-4">
          <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-2">Nombre Oficina</label>
            <InputText
              v-model="form.name"
              class="w-full border-2 border-gray-200 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="Ej: RE/MAX At Work"
              :class="{ 'p-invalid': form.errors.name }"
            />
            <small v-if="form.errors.name" class="p-error">{{ form.errors.name }}</small>
          </div>
        </div>

        <template #footer>
          <div class="flex justify-end space-x-4">
            <Button
              label="Cancelar"
              icon="pi pi-times"
              @click="closeCreateModal"
              class="p-button-text"
              :disabled="form.processing"
            />
            <Button
              label="Guardar"
              icon="pi pi-check"
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
 import { useToast } from 'primevue/usetoast';
  import { ref, computed, watch } from 'vue';
  import { debounce } from 'lodash';
  import AppLayout from '@/Layouts/AppLayout.vue';
  import DataTable from 'primevue/datatable';
  import Column from 'primevue/column';
  import Select from 'primevue/select';
  import Dialog from 'primevue/dialog';
  import { useForm } from '@inertiajs/vue3';
  import { router } from '@inertiajs/vue3';

   const props = defineProps({
    offices: Array,
    filters: Object,
    });
   console.log("officinasde la base de datos", props.offices);

  const toast = useToast();
  const offices = ref(props.offices || []);

watch(() => props.offices, (newOffices) => {
  console.log('🟢 Nuevos datos recibidos desde backend:', newOffices)
  offices.value = newOffices;
});

  console.log('🟢 Oficinas recibidas para mostrar en la tabla:', offices.value);



const selectedStatus = ref(props.filters?.active_office ?? 'all');
const searchInput = ref(props.filters?.search ?? '');
watch(selectedStatus, () => {
  applyFilters();
});

// Debounced para el input de búsqueda
const debouncedApplyFilters = debounce(() => {
  applyFilters();
}, 500);
watch(searchInput, () => {
  debouncedApplyFilters();
});

  // Formatear fecha
  const formatDate = (dateString) => {
    // Implementa tu lógica de formateo de fecha aquí
    return dateString;
  };
  const editOffice = (id) => {
    // Editar oficina
  };

  const toggleStatus = (id) => {
    // Cambiar estado activo/inactivo
  };

  const applyFilters = () => {
    console.log('🟡 Aplicando filtros', {
    active_office: selectedStatus.value,
    search: searchInput.value,
  });
    router.get(route('offices.index'), {
  active_office: selectedStatus.value,
  search: searchInput.value,
}, {
  preserveScroll: true,
  preserveState: true,
  replace: true,
});

};
  // Control de visibilidad del modal
// Control del modal
const showCreateModal = ref(false);

// Formulario con Inertia
const form = useForm({
  name: ''
});

const openCreateModal = () => {
  form.reset();
  showCreateModal.value = true;
};

const closeCreateModal = () => {
  showCreateModal.value = false;
  form.reset();
  form.clearErrors();
};

const submitForm = () => {
  form.post(route('offices.store'), {
    preserveScroll: true,
    onSuccess: () => {
      closeCreateModal();
      // Opcional: Mostrar notificación de éxito
      toast.add({ severity: 'success', summary: 'Éxito', detail: 'Oficina creada correctamente', life: 3000 });
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
  /* Estilos adicionales si son necesarios */
  </style>
