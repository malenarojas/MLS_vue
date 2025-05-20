<script setup>
import { computed } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import { useListingFilterStore } from "@/Stores/useListingFilterStore";
import { useNumberHelpers } from "@/Composables/useNumberHelpers";

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

const statusColors = {
    1: "bg-gray-500",
    2: "bg-green-600",
    3: "bg-yellow-600",
    4: "bg-orange-500",
    5: "bg-indigo-500",
    6: "bg-red-600",
    7: "bg-emerald-600",
    8: "bg-teal-600",
    9: "bg-blue-500",
    10: "bg-rose-600",
};

const getStatusColor = (id) => {
    return statusColors[id] || "bg-gray-700"; // fallback
};
const { currencyFormat } = useNumberHelpers();
</script>

<template>
    <div class="p-4">
        <div
            class="grid auto-rows-max gap-4"
            style="grid-template-columns: repeat(auto-fill, minmax(250px, 1fr))"
        >
            <a
                :href="route('listings.edit', listing.key)"
                v-for="listing in listings.data"
                :key="listing.id"
            >
                <Card
                    class="w-full cursor-pointer bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden"
                >
                    <template #header>
                        <div
                            class="relative bg-cover bg-center h-40 border-b-2 border-gray-200"
                            :style="{
                                backgroundImage: `url(${
                                    listing.default_imagen?.url ??
                                    'listing-default-logo.gif'
                                })`,
                            }"
                        >
                            <div
                                class="absolute bottom-0 right-0 bg-gray-500 text-white text-xs font-light tracking-wider px-2 py-1 rounded"
                            >
                                {{ listing.MLSID }}
                            </div>

                            <div
                                :class="`absolute top-0 left-0 bg-gray-500 text-white text-[.68rem] tracking-wider px-2 py-1 rounded rounded-br-xl ${getStatusColor(
                                    listing.status_id
                                )}`"
                            >
                                {{ listing?.status_name }}
                            </div>
                        </div>
                    </template>
                    <template #content>
                        <p
                            v-if="listing?.price"
                            class="text-md font-semibold text-gray-800"
                        >
                            {{ listing?.currency_symbol }}
                            {{
                                currencyFormat(
                                    parseFloat(listing?.price ?? 0).toFixed(2),
                                    {
                                        style: "decimal",
                                        currency: "BOB",
                                        minimumFractionDigits: 2,
                                    }
                                )
                            }}
                        </p>
                        <div
                            class="py-2 border-b border-gray-300 text-sm text-gray-600 text-center"
                        >
                            <div
                                class="flex justify-center items-center gap-2 mb-2 my-auto"
                            >
                                <span class="bg-gray-200 px-2 py-1 rounded">{{
                                    listing.area_name
                                }}</span>
                                <span
                                    class="bg-blue-200 px-2 py-1 rounded my-auto"
                                    >{{ listing.transaction_type_name }}</span
                                >
                                <span
                                    class="bg-green-200 px-2 py-1 rounded my-auto inline-block max-w-[10ch] overflow-hidden text-ellipsis whitespace-nowrap"
                                >
                                    {{ listing.subtype_property_name }}
                                </span>
                            </div>
                            <div class="flex justify-around gap-4 mb-2">
                                <p>
                                    🛏
                                    {{ listing.bedrooms }}
                                    Dormitorios
                                </p>
                                <p>
                                    🛁
                                    {{ listing.bathrooms }}
                                    Baños
                                </p>
                            </div>
                        </div>
                    </template>
                    <template #footer>
                        <p
                            class="w-full text-center text-gray-700 font-medium py-2"
                        >
                            {{ listing.agent_name }}
                        </p>
                    </template>
                </Card>
            </a>
        </div>

        <!-- Paginación Mejorada -->
        <div class="flex justify-center mt-6 space-x-2">
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
