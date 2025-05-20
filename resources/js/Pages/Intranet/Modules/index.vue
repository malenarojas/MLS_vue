<script setup>
const props = defineProps({ modules: Array,  user: Object,
    role: String});
import AppLayout from "@/Layouts/AppLayout.vue";
import { router } from "@inertiajs/vue3";
import defaultagents from '@/assets/img/TÍTULOS INTRANET-01.jpg';
import defaultbrocker from '@/assets/img/TÍTULOS INTRANET-02.jpg';
import { ref } from 'vue';
import { useToast } from 'primevue/usetoast';

console.log("modules traidos ", props.modules);
console.log("role traidos ", props.role);
console.log("user traidos ", props.user);
const roleName = ref(props.role|| {});
const toast = useToast();

function openModule(moduleId) {
    if (!moduleId) {
    // Si no hay moduleId, lanzamos un toast sin redirigir
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'Este módulo no tiene página asignada.',
      life: 4000,
    });
    return; // Salimos de la función
  }
  router.visit(route('intranets.modules.show', moduleId), {
    onError: (errors) => {
      toast.add({
        severity: 'error',
        summary: 'Error',
        detail: 'Este módulo no tiene página asignada.',
        life: 4000,
      });
    }
  });
}

</script>

<template>
    <AppLayout title="Intranet Brókers">
      <div class="bg-gradient-to-br from-white to-blue-50 min-h-screen px-6 py-4 rounded-xl shadow-inner">

        <!-- Encabezado dinámico por rol -->
        <div class="flex flex-col items-center mb-10 w-full">
          <img
            v-if="roleName === 'Broker'"
            :src="defaultbrocker"
            alt="Título Bróker"
            class="w-full max-w-5xl object-contain mb-6"
          />
          <img
            v-else-if="roleName === 'Agente' || roleName === 'Administrador'"
            :src="defaultagents"
            alt="Título Agente/Admin"
            class="w-full max-w-5xl object-contain mb-6"
          />

          <h1 class="text-4xl font-extrabold text-blue-800 tracking-wider uppercase text-center">
            Módulos
          </h1>
        </div>
        <!-- Grid de módulos -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="module in modules"
            :key="module.id"
            class="bg-white rounded-xl shadow-md p-5 cursor-pointer hover:shadow-xl hover:-translate-y-1 transition-all duration-300"
            @click="openModule(module.id)"
          >
            <img
              :src="module.image_url"
              alt="Imagen del módulo"
              class="w-full h-40 object-contain mb-4"
            />
            <h2 class="text-lg font-semibold text-blue-700 text-center tracking-wide">
              {{ module.name }}
            </h2>
          </div>
        </div>
      </div>
    </AppLayout>
  </template>

