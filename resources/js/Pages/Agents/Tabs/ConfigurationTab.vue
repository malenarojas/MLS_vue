<template>
    <div class="p-4 bg-white shadow rounded">
      <h2 class="text-xl font-bold mb-4 text-blue-800">Permisos</h2>

      <div v-for="(permissions, group) in groupedPermissions" :key="group" class="mb-6">
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
              v-model="agentData.permissions"
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
import { computed } from 'vue';


const props = defineProps({
  agentData: {
    type: Object,
    required: true,
  },
  validationErrors: {
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
});

const allPermissions = props.options.formOptions.all_permissions || [];

const groupedPermissions = computed(() => {
  return allPermissions.reduce((acc, permission) => {
    const group = permission.group || 'otros';
    if (!acc[group]) acc[group] = [];
    acc[group].push(permission);
    return acc;
  }, {});
});

const formatGroupName = (group) => {
  return group.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
};
</script>

