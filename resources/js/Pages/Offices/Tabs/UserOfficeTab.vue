<template>
    <div>
      <h2 class="text-xl font-semibold mb-4">Agentes de la oficina</h2>

      <!-- Filtros de ejemplo -->
      <div class="flex items-center gap-4 mb-4">
        <Dropdown
          v-model="selectedStatus"
          :options="statusOptions"
          optionLabel="label"
          placeholder="Estado"
          class="w-36"
        />
        <Dropdown
          v-model="selectedRole"
          :options="roleOptions"
          optionLabel="label"
          placeholder="Roles"
          class="w-44"
        />
        <div class="flex items-center gap-2">
          <InputText v-model="searchTerm" placeholder="Buscar..." />
          <Button icon="pi pi-search" class="p-button-primary" @click="onFilter" />
        </div>
      </div>

      <!-- DataTable con los agentes -->
      <DataTable
        :value="filteredAgents"
        :rows="10"
        paginator
        responsiveLayout="scroll"
        class="shadow border rounded"
        >
  <!-- Acciones -->
  <Column header="Acciones" :body="actionsTemplate" class="w-32" />

  <Column header="Nombre">
  <template #body="{ data }">
    <span class="font-semibold text-gray-800">
      {{ data.user?.name_to_show || `${data.user?.first_name} ${data.user?.last_name}` }}
    </span>
  </template>
</Column>

<Column header="Correo">
  <template #body="{ data }">
    <span>{{ data.user?.email || '-' }}</span>
  </template>
</Column>

<Column header="Teléfono">
  <template #body="{ data }">
    <span>{{ data.user?.phone_number || '-' }}</span>
  </template>
</Column>

<Column header="Activo">
  <template #body="{ data }">
    <span
      :class="{
        'text-green-600 font-semibold': data.agent_status_id == 1,
        'text-red-500 font-semibold': data.agent_status_id == 2
      }"
    >
      {{ data.agent_status_id == 1 ? 'Activo' : 'Inactivo' }}
    </span>
  </template>
</Column>


</DataTable>

    </div>
  </template>

  <script setup>
  // Imports
  import { defineProps, ref, computed } from 'vue'
  import Dropdown from 'primevue/dropdown'
  import InputText from 'primevue/inputtext'
  import Button from 'primevue/button'
  import DataTable from 'primevue/datatable'
  import Column from 'primevue/column'

  // 1. Recibimos todo el 'form' (que incluye form.agents)
  //    o solo los agentes. Aquí hacemos un ejemplo con 'form'.
  const props = defineProps({
    form: {
      type: Object,
      required: true
    }
  })
console.log("props del from para usarios ", props.form.agents);  // Filtros
  const selectedStatus = ref(null)
  const selectedRole = ref(null)
  const searchTerm = ref('')

  const filteredAgents = computed(() => {
  if (!props.form.agents) return []

  return props.form.agents.filter(agent => {
    const user = agent.user || {}

    const matchesSearch =
      !searchTerm.value ||
      user.name_to_show?.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
      user.first_name?.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
      user.last_name?.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
      user.email?.toLowerCase().includes(searchTerm.value.toLowerCase())

    const matchesStatus =
      !selectedStatus.value || agent.status === selectedStatus.value

    const matchesRole =
      !selectedRole.value || agent.roles?.includes(selectedRole.value)

    return matchesSearch && matchesStatus && matchesRole
  })
})

function actionsTemplate({ data }) {
  return h(
    'div',
    { class: 'flex items-center gap-2' },
    [
      h('button', {
        class: 'text-blue-600 hover:text-blue-800 font-medium',
        onClick: () => console.log('Editar', data.id)
      }, 'Editar'),

      h('button', {
        class: 'text-red-600 hover:text-red-800 font-medium',
        onClick: () => console.log('Eliminar', data.id)
      }, 'Eliminar')
    ]
  )
}

function nameTemplate({ data }) {
  const name = data.user?.name_to_show || `${data.user?.first_name || ''} ${data.user?.last_name || ''}`
  return h('span', { class: 'text-gray-800 font-semibold' }, name)
}

function emailTemplate({ data }) {
  return h('span', { class: 'text-gray-600' }, data.user?.email || '-')
}

function phoneTemplate({ data }) {
  return h('span', { class: 'text-gray-600' }, data.user?.phone_number || '-')
}

function activeTemplate({ data }) {
  const estado = data.agent_status_id
  const label = estado == 1 ? 'Activo' : 'Inactivo'
  const color = estado == 1 ? 'text-green-600' : 'text-red-500'

  return h('span', { class: `font-semibold ${color}` }, label)
}




  // Botón "Buscar"
  function onFilter() {
    // Podés poner lógica adicional si querés
    console.log('Filtrando con:', searchTerm.value, selectedRole.value, selectedStatus.value)
  }
  </script>

  <style scoped>
  /* Estilos a gusto */
  </style>
