<script setup>
import { computed, onMounted } from "vue";
import { Link, router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import { route } from "ziggy-js";
import QualityControlFilter from "./Partial/QualityControlFilter.vue";
import { useListingEditStore } from "@/Stores/useListingEditStore";

const props = defineProps({
    listings: Object,
});
const editStore = useListingEditStore();

// Función para cambiar de página
const changePage = (url) => {
    if (url) {
        router.get(url, {}, { preserveState: true, preserveScroll: true });
    }
};

const listingsData = computed(() => props.listings.data);

onMounted(() => {
    editStore.setHistortTab();
});
</script>

<template>
    <AppLayout>
        <h1 class="text-xl font-bold mb-4">Control de calidad</h1>
        <QualityControlFilter />
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <!-- Contenedor Scrollable -->
            <div class="h-[500px] overflow-y-auto border border-gray-200">
                <table
                    class="table-auto w-full text-left border-collapse text-sm"
                >
                    <thead class="bg-gray-100 text-gray-700 text-center">
                        <tr>
                            <!-- <th class="border-b border-gray-300 px-4 py-2">
                                ID
                            </th> -->
                            <th class="border-b border-gray-300 px-4 py-2">
                                MLSID
                            </th>
                            <th class="border-b border-gray-300 px-4 py-2">
                                Creado en
                            </th>
                            <th class="border-b border-gray-300 px-4 py-2">
                                Agente
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600">
                        <tr
                            v-for="listing in listingsData"
                            :key="listing.id"
                            class="hover:bg-gray-50 even:bg-gray-50 text-center"
                        >
                            <!-- <td class="border-b border-gray-200 px-4 py-2">
                                <Link
                                    :href="route('listings.edit', listing.key)"
                                >
                                    {{ listing.id }}
                                </Link>
                            </td> -->
                            <td
                                class="border-b border-gray-200 px-4 py-2 text-blue-500 hover:text-blue-400 hover:underline cursor-pointer"
                            >
                                <Link
                                    :href="route('listings.edit', listing.key)"
                                >
                                    {{ listing.MLSID }}
                                </Link>
                            </td>
                            <td class="border-b border-gray-200 px-4 py-2">
                                {{
                                    new Date(
                                        listing.created_at
                                    ).toLocaleDateString()
                                }}
                            </td>
                            <td class="border-b border-gray-200 px-4 py-2">
                                {{
                                    listing.agents?.[0]?.user?.name_to_show ??
                                    "N/A"
                                }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="flex justify-center my-5 space-x-2">
                <button
                    v-if="props.listings.prev_page_url"
                    @click="changePage(props.listings.prev_page_url)"
                    class="px-4 py-2 text-sm font-medium border rounded-lg transition-all duration-300 bg-white text-gray-800 hover:bg-gray-100"
                >
                    Anterior
                </button>
                <span class="px-4 py-2 text-sm font-medium"
                    >Página {{ props.listings.current_page }} de
                    {{ props.listings.last_page }}</span
                >
                <button
                    v-if="props.listings.next_page_url"
                    @click="changePage(props.listings.next_page_url)"
                    class="px-4 py-2 text-sm font-medium border rounded-lg transition-all duration-300 bg-white text-gray-800 hover:bg-gray-100"
                >
                    Siguiente
                </button>
            </div>
        </div>
    </AppLayout>
</template>
