<script setup>
import { computed } from "vue";
import { usePage, router, Link } from "@inertiajs/vue3";
import { useListingFilterStore } from "@/Stores/useListingFilterStore";

const page = usePage();
const filtersStore = useListingFilterStore();

const listings = computed(
    () => page.props?.listings || { data: [], links: [] }
);

const changePage = (url) => {
    if (url) {
        const currentPage = url.split("=").pop();
        router.visit(url, {
            method: "get",
            data: {
                ...filtersStore.formFilters,
                page: currentPage,
            },
            preserveState: true,
            preserveScroll: true,
            preserveUrl: true,
            only: ["listings"],
        });
    }
};
</script>

<template>
    <div class="bg-white shadow-md rounded-lg">
        <!-- Contenedor Scrollable -->
        <div class="h-[45rem] overflow-y-auto border border-gray-200">
            <table class="table-auto w-full text-left border-collapse text-sm">
                <thead class="bg-gray-100 text-gray-700 text-center">
                    <tr>
                        <th class="border-b border-gray-300 px-4 py-2">
                            Detalle Cap.
                        </th>
                        <th class="border-b border-gray-300 px-4 py-2">Área</th>
                        <th class="border-b border-gray-300 px-4 py-2">
                            Transacción
                        </th>
                        <th class="border-b border-gray-300 px-4 py-2">
                            Tipo de Propiedad
                        </th>
                        <th class="border-b border-gray-300 px-4 py-2 max-w-20">
                            Dormitorios/Baños
                        </th>
                        <th class="border-b border-gray-300 px-4 py-2">
                            Tipo de Contrato
                        </th>
                        <th class="border-b border-gray-300 px-4 py-2">
                            Creada/Caduca
                        </th>
                        <th class="border-b border-gray-300 px-4 py-2">
                            Días en el Mercado
                        </th>
                        <th class="border-b border-gray-300 px-4 py-2">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="text-gray-600">
                    <tr
                        v-for="listing in listings.data"
                        :key="listing.id"
                        class="hover:bg-gray-50 even:bg-gray-50 text-center"
                    >
                        <td class="border-b border-gray-200">
                            <div
                                class="relative bg-cover bg-center h-24 border-b-2 border-gray-200"
                                :style="{
                                    backgroundImage: `url(${
                                        listing.default_imagen?.url ??
                                        '/listing-default-logo.gif'
                                    })`,
                                }"
                            >
                                <div
                                    class="absolute bottom-0 bg-gray-400 text-white text-[.8rem] px-1"
                                >
                                    <p>{{ listing.MLSID }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="border-b border-gray-200 px-4 py-2">
                            {{ listing.area_name }}
                        </td>
                        <td class="border-b border-gray-200 px-4 py-2">
                            {{ listing.transaction_type_name }}
                        </td>
                        <td class="border-b border-gray-200 px-4 py-2">
                            {{ listing.subtype_property_name }}
                        </td>
                        <td
                            class="border-b border-gray-200 px-4 py-2 text-right max-w-20"
                        >
                            <p>{{ listing.bedrooms }}</p>
                            <p>{{ listing.bathrooms }}</p>
                        </td>
                        <td class="border-b border-gray-200 px-4 py-2">
                            {{ listing.contract_type_name }}
                        </td>
                        <td class="border-b border-gray-200 px-4 py-2">
                            <p>{{ listing.date_of_listing }}</p>
                            <p>{{ listing.contract_end_date }}</p>
                        </td>
                        <td class="border-b border-gray-200 px-4 py-2">
                            {{ listing.days_in_market }}
                        </td>
                        <td class="border-b px-4 py-2">
                            <Link
                                :href="route('listings.edit', listing.key)"
                                class="text-blue-500 underline hover:text-blue-700"
                            >
                                <Button
                                    icon="pi pi-pencil"
                                    size="small"
                                    class="w-full"
                                    label="Editar"
                                />
                            </Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex justify-center mt-6 mb-2 space-x-2 py-2">
            <button
                v-for="link in listings.links"
                :key="link.label"
                :disabled="!link.url"
                @click="changePage(link.url)"
                v-html="link.label"
                :aria-label="`Ir a la página ${link.label}`"
                class="px-4 py-2 text-sm font-medium border rounded-lg transition-all duration-300"
                :class="{
                    'bg-blue-500 text-white hover:bg-blue-600': link.active,
                    'bg-gray-200 text-gray-600 cursor-not-allowed': !link.url,
                    'bg-white text-gray-800 hover:bg-gray-100':
                        link.url && !link.active,
                }"
            ></button>
        </div>
    </div>
</template>
