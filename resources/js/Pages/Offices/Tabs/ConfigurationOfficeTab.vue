<template>
    <div class="p-4 bg-white shadow rounded">
      <h2 class="text-xl font-bold mb-4 text-blue-800">Permisos de la Oficina</h2>

      <!-- Iterar los grupos de permisos -->
      <div
        v-for="(permissions, group) in groupedPermissions"
        :key="group"
        class="mb-6"
      >
        <!-- Nombre del grupo formateado -->
        <h3 class="text-lg font-semibold mb-2 text-gray-700 capitalize">
          {{ formatGroupName(group) }}
        </h3>

        <!-- Lista de permisos en este grupo -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
          <div
            v-for="permission in permissions"
            :key="permission.id"
            class="flex items-center gap-2"
          >
            <Checkbox
              v-model="selectedPermissions"
              :value="permission.name"
              :inputId="`perm-${permission.id}`"
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
  import { computed, ref, watch } from 'vue';
  import Checkbox from 'primevue/checkbox';

  /*
    Comentario en español:
    - Este tab se basa en un "form" que contiene info de la oficina,
      incluyendo "officePermissions" o algo similar.
    - "all_permissions" es la lista completa de permisos disponibles para una oficina.
    - "selectedPermissions" es un array con los nombres de los permisos marcados.
  */

  const props = defineProps({
    form: {
      type: Object,
      required: true
    },
    // Opcional: si tienes un objeto "options" que trae la lista de todos los permisos
    options: {
      type: Object,
      required: true
    }
  });

  // Extraer la lista de todos los permisos disponibles
  const allPermissions = props.options?.all_permissions ?? null;

  // Arreglo reactivo que contiene los nombres de los permisos seleccionados
  const selectedPermissions = ref([]);

  // Cuando cambie form.officePermissions, sincronzamos con selectedPermissions
  watch(
    () => props.form.officePermissions,
    (newVal) => {
      if (Array.isArray(newVal)) {
        // Asegurarnos de solo tener nombres de permiso (por si vienen objetos)
        const names = newVal.map((perm) =>
          typeof perm === 'string' ? perm : perm.name
        );
        selectedPermissions.value = names;
        console.log("✅ Cargando permisos de la oficina:", names);
      } else {
        selectedPermissions.value = [];
      }
    },
    { immediate: true }
  );

  // Cada vez que cambie selectedPermissions, actualizamos form.officePermissions
  watch(selectedPermissions, (newVal) => {
    props.form.officePermissions = [...newVal];
    console.log("📥 Permisos seleccionados:", newVal);
  });

  // Agrupar permisos según permission.group (si existe), para mostrarlos más organizados
  /*const groupedPermissions = computed(() => {
    return allPermissions.reduce((acc, permission) => {
      const group = permission.group || 'otros';
      if (!acc[group]) acc[group] = [];
      acc[group].push(permission);
      return acc;
    }, {});
  });*/

  // Formatear el nombre del grupo (quitar underscores, poner mayúsculas, etc.)
  function formatGroupName(group) {
    return group
      .replace(/_/g, ' ')
      .replace(/\b\w/g, (letter) => letter.toUpperCase());
  }
  </script>

  <style scoped>
  /* Ajusta estilos a tu gusto */
  </style>
