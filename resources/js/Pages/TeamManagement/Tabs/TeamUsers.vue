<template>
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
      <h2 class="text-2xl font-bold mb-6 text-blue-800 border-b border-blue-100 pb-3">Administrar Equipo</h2>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Sección Miembros -->
        <div>
          <h3 class="text-lg font-semibold text-blue-700 mb-4">Miembros del Equipo</h3>

          <div class="flex gap-2 mb-4">
            <Select
              v-model="selectedAdmin"
              :options="mappedAgents"
              optionLabel="name_to_show"
              optionValue="id"
              placeholder="Seleccionar miembro"
              class="flex-grow border border-gray-300 rounded-lg"
            />
            <Button
              label="Agregar"
              icon="pi pi-plus"
              class="bg-blue-500 hover:bg-blue-600 border-blue-500"
              @click="addMember"
            />
          </div>

          <ul class="space-y-2">
            <li
              v-for="(member, index) in teamMembers"
              :key="index"
              class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100"
            >
              <div class="flex items-center gap-3">
                <img
                  :src="member.image_url || '/images/avatar-default.png'"
                  alt="Foto"
                  class="w-8 h-8 rounded-full border border-gray-300 object-cover"
                />
                <span class="text-gray-800">
                  {{ member.user?.name_to_show || 'Sin nombre' }}
                  <span v-if="member.status === 'Transferred'" class="text-xs text-gray-500 ml-2">(Transferred)</span>
                </span>
              </div>
              <button
                @click="removeMember(index)"
                class="text-red-500 hover:text-red-700 p-1"
              >
                <i class="pi pi-times"></i>
              </button>
            </li>
          </ul>
        </div>

        <!-- Sección Líder -->
        <div>
          <h3 class="text-lg font-semibold text-blue-700 mb-4">Líder del Equipo</h3>

          <div class="flex items-center gap-4 mb-4">
            <img
              :src="form.leader?.image_url || '/images/avatar-default.png'"
              alt="Foto líder"
              class="w-10 h-10 rounded-full border-2 border-yellow-400 object-cover"
            />
            <span class="text-gray-800 font-medium">
              {{ form.leader?.name_to_show || 'Sin líder asignado' }}
            </span>
            <Button
              label="Cambiar"
              icon="pi pi-user-edit"
              class="ml-auto bg-blue-500 hover:bg-blue-600 text-white text-sm"
              @click="openLeaderModal"
            />
          </div>

          <div v-if="leaderMember" class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
            <div class="flex items-center gap-2">
              <i class="pi pi-star text-yellow-500"></i>
              <span>{{ leaderMember.user?.name_to_show }}</span>
            </div>
            <button
              @click="removeMember(teamMembers.indexOf(leaderMember))"
              class="text-red-500 hover:text-red-700 p-1"
            >
              <i class="pi pi-times"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Modal para líder -->
      <Dialog
        v-model:visible="dialogVisible"
        header="Seleccionar Líder"
        modal
        :style="{ width: '500px' }"
      >
        <Dropdown
          v-model="selectedLeaderId"
          :options="teamMembersForDropdown"
          optionLabel="name_to_show"
          optionValue="id"
          placeholder="Buscar líder..."
          class="w-full border border-gray-300 rounded-lg mb-4"
          filter
        />

        <template #footer>
          <Button
            label="Cancelar"
            class="p-button-text text-gray-600 hover:text-gray-800"
            @click="dialogVisible = false"
          />
          <Button
            label="Asignar"
            class="bg-blue-500 hover:bg-blue-600 border-blue-500"
            @click="assignLeader"
          />
        </template>
      </Dialog>
    </div>
  </template>
  <script setup>
  import { ref , computed, watch, toRaw} from 'vue';
  import Select from 'primevue/select';
  import Button from 'primevue/button';

  // ⚠️ Props que deberías recibir desde el padre
  const props = defineProps({
    agents: Array,
    form: Array,
    initialAdmins: Array,
    initialMembers: Array,
  });
  const dialogVisible = ref(false)
  const selectedLeaderId = ref(null)

  const selectedAdmin = ref(null);
  const newLeader = ref(null);

const teamMembers = ref((props.form.members ?? []).map(agent => JSON.parse(JSON.stringify(toRaw(agent)))));
console.log("asi lo estoy convirtiendo ", teamMembers.value);
watch(
  () => [teamMembers.value, props.form.leader_id],
  () => {
    console.log("📦 Estado Final de equipo:");
    console.log("🧩 Miembros:", teamMembers.value);
    console.log("⭐ Líder ID:", props.form.leader_id);
    console.log("👤 Líder Objeto:", props.form.leader);
  },
  { deep: true }
);
watch(
  teamMembers,
  (nuevoValor) => {
    props.form.members = nuevoValor.map(member => ({
      ...member,
      pivot: {
        is_leader: member.pivot?.is_leader || false
      }
    }));
  },
  { deep: true }
);


console.log("miembros del equipo ", teamMembers.value);
console.log("agente en el tab ya ", props.agents);
const mappedAgents = computed(() =>
  props.agents.map((agent) => ({
    ...agent,
    name_to_show: agent.user?.name_to_show || 'Sin nombre',
  }))
);
const teamMembersForDropdown = computed(() => {
  return teamMembers.value.map(member => ({
    id: member.id,
    name_to_show: member.user?.name_to_show || 'Sin nombre',
  }));
});

const leaderMember = computed(() =>
  teamMembers.value.find((member) => member.pivot?.is_leader === true)
);


const filteredAgents = computed(() => {
  return teamMembers.value.map(member => ({
    id: member.id,
    name_to_show: member.user?.name_to_show || 'Sin nombre',
  }));
});

// Función para abrir el modal y preseleccionar el líder actual
function openLeaderModal() {
  selectedLeaderId.value = props.form.leader_id  // preseleccionar el líder actual en el dropdown
  dialogVisible.value = true              // mostrar el diálogo
}

function assignLeader() {
  const newLeader = teamMembers.value.find(agent => agent.id === selectedLeaderId.value);

  if (newLeader) {
    // 🔁 1. Limpiar el liderazgo actual
    teamMembers.value.forEach(member => {
      if (member.pivot) {
        member.pivot.is_leader = false;
      }
    });

    // 🆕 2. Asignar nuevo líder
    if (!newLeader.pivot) newLeader.pivot = {}; // Asegurarse que exista
    newLeader.pivot.is_leader = true;

    // 📝 3. Actualizar los datos del form también (opcional)
    props.form.leader_id = newLeader.id;
    props.form.leader = {
      ...newLeader,
      name_to_show: newLeader.user?.name_to_show || 'Sin nombre'
    };
  }

  dialogVisible.value = false;
}



  const addMember = () => {
  const found = mappedAgents.value.find(agent => agent.id === selectedAdmin.value);
  if (found && !teamMembers.value.find(a => a.id === found.id)) {
    teamMembers.value.push(found);
    selectedAdmin.value = null;

    // 👇 Mostrás el array actualizado en consola
    console.log("🧩 Miembros actuales del equipo:", teamMembers.value);
  }
};


  const removeMember = (index) => {
    teamMembers.value.splice(index, 1);
  };
  </script>

  <style scoped>
  /* Estilos adicionales si necesitás */
  </style>
