

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import fondo from '@/assets/img/fondo1.jpg';
import { ref, computed } from 'vue';

const { module, page } = defineProps({
    module: Object,
    page: Object,
    child_pages: Array,
    parent_page: Object,
    default_page_id: Number,
});
console.log("modulo traido", module);
console.log("pagina traida", page);
const breadcrumbs = computed(() => {
  const trail = [];
  let current = page?.parent_recursive;

  while (current) {
    trail.unshift({
      id: current.id,
      title: current.title,
    });
    current = current.parent_recursive;
  }

  return trail;
});



</script>
<template>
  <AppLayout :title="module.name">
    <div class="bg-gradient-to-br from-white to-blue-50 min-h-screen p-6 rounded-xl shadow-lg">
      <!-- Encabezado del módulo -->
      <div class="flex flex-col items-center mb-1">
        <div v-if="page?.image" class="relative w-full max-w-5xl mb-10 rounded-xl overflow-hidden shadow-lg">
        <!-- Imagen de fondo -->
        <img
            :src=" fondo||page.image"
            alt="Imagen de la página"
            class="w-full h-44 object-cover brightness-75"
        />

        <!-- Título encima de la imagen -->
        <div class="absolute inset-0 flex items-center justify-center">
            <h1 class="text-4xl font-extrabold text-white uppercase tracking-wider drop-shadow-lg text-center px-4">
            {{ module.name }}
            </h1>
        </div>


        </div>
        <div v-else class="relative w-full max-w-5xl mb-10 rounded-xl overflow-hidden shadow-lg">
            <img
            :src=" fondo"
            alt="Imagen de la página"
            class="w-full h-44 object-cover brightness-75"
        />

        <!-- Título encima de la imagen -->
        <div class="absolute inset-0 flex items-center justify-center">
            <h1 class="text-4xl font-extrabold text-white uppercase tracking-wider drop-shadow-lg text-center px-4">
            {{ module.name }}
            </h1>
        </div>
        </div>
        <nav class="w-full max-w-5xl mx-auto -mt-2 mb-8 px-4">
         <!-- Breadcrumb estilizado -->
         <ol class="flex items-center space-x-2 text-base text-gray-700 font-medium">
        <li>
            <a
            @click.prevent="$inertia.visit(route('intranets.modules.index'))"
            class="text-blue-900 hover:underline text-xl"
            >
            Inicio
            </a>
        </li>
        <li class="text-gray-400 font-light text-xl">›</li>

        <template v-for="(crumb, index) in breadcrumbs" :key="crumb.id">
            <li>
            <a
                class="text-blue-800 hover:underline cursor-pointer text-xl"
                @click.prevent="$inertia.visit(route('intranets.modules.show', module.id) + `?page=${crumb.id}`)"
            >
                {{ crumb.title }}
            </a>
            </li>
            <li class="text-gray-400 font-light text-xl">›</li>
        </template>

        <li class="text-red-600 font-semibold text-xl">
            {{ page.title }}
        </li>
        </ol>

        </nav>
      </div>
      <!-- Otras Páginas como sección estilizada (dentro del mismo flujo de secciones)
    <div v-if="child_pages.length" class="space-y-3">
    <div class="bg-white rounded-xl p-4 shadow-md border border-blue-100 mb-4">
        <h3 class="text-xl font-bold text-blue-700 border-b border-blue-200 pb-2 mb-4">
        Otras Páginas
        </h3>
        <ul class="space-y-3">
        <li
            v-for="child in child_pages"
            :key="child.id"
            class="p-4 border border-gray-200 rounded-lg shadow-sm bg-gray-50 flex items-center justify-between"
        >
            <span class="text-gray-800 font-medium text-base">
            {{ child.title }}
            </span>
            <a
            class="text-sm text-blue-600 hover:underline"
            :href="route('intranets.modules.show', module.id) + `?page=${child.id}`"
            >
            Ver página
            </a>
        </li>
        </ul>
    </div>
    </div>-->

      <!-- Secciones -->
      <div v-if="page?.sections?.length" class="space-y-3">
        <div
          v-for="section in page.sections"
          :key="section.id"
          class="bg-white rounded-xl p-6 shadow-md border border-blue-100"
        >
          <h3 class="text-xl font-bold text-blue-700 border-b border-blue-200 pb-2 mb-4">
            {{ section.title }}
          </h3>
          <!-- Elementos -->
            <ul v-if="section.elements.length" class="space-y-4">
            <li
                v-for="el in section.elements"
                :key="el.id"
                class="p-4 border border-gray-200 rounded-lg shadow-sm bg-gray-50 flex flex-col sm:flex-row sm:items-center sm:justify-between"
            >
                <span class="text-gray-800 font-medium text-base">
                {{ el.content?.name }}
                </span>

                <a
                    v-if="el.content?.url && el.type === 'link'"
                    :href="el.content.url"
                    target="_blank"
                    class="text-sm text-blue-600 hover:underline mt-2 sm:mt-0"
                    >
                    Ver contenido
                    </a>

                    <a
                    v-else-if="el.content?.page_id && el.type === 'page'"
                    :href="route('intranets.modules.show', module.id) + `?page=${el.content.page_id}`"
                    class="text-sm text-blue-600 hover:underline mt-2 sm:mt-0"
                    >
                    Ver página
                    </a>

            </li>
            </ul>


          <div v-else class="text-gray-400 italic text-sm">
            Esta sección no tiene elementos.
          </div>
        </div>
      </div>

      <div v-else class="text-center text-red-500 font-medium">
        No hay secciones disponibles para esta página.
      </div>

      <!-- Botón para regresar -->
      <div class="mt-12 text-center">
        <button
          class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-md transition-all duration-300"
          @click="$inertia.visit(route('intranets.modules.index'))"
        >
          ← Volver a Módulos
        </button>
      </div>
    </div>
  </AppLayout>
</template>
