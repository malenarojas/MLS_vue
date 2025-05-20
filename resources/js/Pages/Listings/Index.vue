<script setup>
import { onMounted, ref } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import ListingCard from "./Partial/ListingCard.vue";
import CreateListing from "./Partial/CreateListing.vue";
import ListingTable from "./Partial/ListingTable.vue";
import ListingFilter from "./Partial/ListingFilter.vue";
import { useListingFilterStore } from "@/Stores/useListingFilterStore";
import { useListingEditStore } from "@/Stores/useListingEditStore";

const props = defineProps({
    menus: Object,
    listings: Object,
    permissions: Array,
    role: String,
    filters: Object,
});

const view = ref(0); // 0 card, 1 table
const isModalVisible = ref(false);
const filterStore = useListingFilterStore();
const editStore = useListingEditStore();

onMounted(() => {
    editStore.setTab();
});
</script>

<template>
    <AppLayout title="Listings">
        <ListingFilter :form-filters="formFilters" />
        <!-- <pre>{{ props.listings }}</pre> -->
        <div>
            <div
                class="flex justify-between items-center my-4 px-4 py-2 rounded-lg"
            >
                <h1 class="text-2xl font-semibold tracking-wider">
                    Propiedades
                    <span class="text-base ml-4 font-light tracking-wide">
                        Administra tus propiedades
                    </span>
                </h1>
                <Button
                    label="Nueva Captación"
                    size="small"
                    @click="isModalVisible = true"
                />
            </div>

            <section class="my-4 px-4">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <p class="text-sm text-gray-600">Ordenado por</p>
                    </div>
                    <div class="flex gap-2">
                        <button
                            @click="view = 0"
                            :class="[
                                'rounded-lg p-2 cursor-pointer transition-all',
                                view === 0
                                    ? 'bg-blue-500 text-white shadow-md'
                                    : 'bg-gray-200 text-gray-600 hover:bg-gray-300',
                            ]"
                        >
                            Vista de Galería
                        </button>
                        <button
                            @click="view = 1"
                            :class="[
                                'rounded-lg p-2 cursor-pointer transition-all',
                                view === 1
                                    ? 'bg-blue-500 text-white shadow-md'
                                    : 'bg-gray-200 text-gray-600 hover:bg-gray-300',
                            ]"
                        >
                            Vista de la Lista
                        </button>
                    </div>
                </div>

                <ListingCard v-if="view === 0" />
                <ListingTable v-else-if="view === 1" />
            </section>
        </div>

        <CreateListing
            :visible="isModalVisible"
            @close="isModalVisible = false"
        />
    </AppLayout>
</template>
