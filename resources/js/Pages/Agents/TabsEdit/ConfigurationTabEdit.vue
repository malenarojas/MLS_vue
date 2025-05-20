<template>
    <div class="p-4 bg-white shadow rounded">
      <h2 class="text-xl font-bold mb-4 text-blue-800">Permisos</h2>

      <div
        v-for="(permissions, group) in groupedPermissions"
        :key="group"
        class="mb-6"
      >
        <h3 class="text-lg font-semibold mb-2 text-gray-700 capitalize">
          {{ formatGroupName(group) }}
        </h3>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
          <div
            v-for="permission in permissions"
            :key="permission.id"
            class="flex items-center gap-2"
          >
          <Checkbox
            v-model="selectedPermissions"
            :inputId="`perm-${permission.id}`"
            :value="permission.name"
            />

            <label
              :for="`perm-${permission.id}`"
              class="text-sm text-gray-800"
            >
              {{ permission.description }}
            </label>
          </div>
        </div>
      </div>
    </div>
  </template>

<script setup>
import Checkbox from 'primevue/checkbox';
import { computed, ref, watch } from 'vue';

const props = defineProps({
  agentDataupdate: {
    type: Object,
    required: true,
  },
  validationErrors: {
    type: Object,
    required: true,
  },
  options: {
    type: Object,
    required: true,
  },
  user: {
    type: Object,
    required: true,
  },
});

// 🔹 Todos los permisos disponibles
const allPermissions = props.options.all_permissions || [];
const selectedPermissions = ref([]);

watch(
  () => props.agentDataupdate.user?.permissions,
  (newPermissions) => {
    if (Array.isArray(newPermissions)) {
      // Convertimos a array de nombres de permisos (por si vienen como objetos)
      const names = newPermissions.map((perm) =>
        typeof perm === 'string' ? perm : perm.name
      );

      selectedPermissions.value = names;
      props.agentDataupdate.permissions = names; // 🔁 Guardamos también en agentDataupdate.permissions

      console.log("✅ Permisos cargados desde user:", selectedPermissions.value);
    } else {
      selectedPermissions.value = [];
      props.agentDataupdate.permissions = [];
    }
  },
  { immediate: true }
);
watch(selectedPermissions, (newSelected) => {
  props.agentDataupdate.permissions = [...newSelected];
  console.log("📥 Permisos actualizados en agentDataupdate.permissions:", newSelected);
});

watch(selectedPermissions, (newSelected) => {
  props.agentDataupdate.permissions = [...newSelected];

  const tienePermisoTerminacion = newSelected.includes('termination users');

  if (tienePermisoTerminacion) {
    if (!props.agentDataupdate.date_termination) {
      props.agentDataupdate.date_termination = new Date().toISOString().split('T')[0];
      console.log("🗓️ Fecha asignada por permiso:", props.agentDataupdate.date_termination);
    }
  } else {
    props.agentDataupdate.date_termination = null;
    console.log("❌ Permiso removido, se borró termination_date");
  }

  console.log("📥 Permisos actualizados en agentDataupdate.permissions:", newSelected);
});

const groupedPermissions = computed(() => {
  return allPermissions.reduce((acc, permission) => {
    const group = permission.group || 'otros';
    if (!acc[group]) acc[group] = [];
    acc[group].push(permission);
    return acc;
  }, {});
});

const formatGroupName = (group) => {
  return group.replace(/_/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase());
};

</script>
