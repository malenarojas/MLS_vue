<script setup>
import { computed, ref, onMounted } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import debounce from "lodash/debounce";
import { useInitializeListingFilters } from "@/Composables/Listings/useInitializeListingFilters";
import { useListingFilterStore } from "@/Stores/useListingFilterStore";

const props = computed(() => usePage().props);
const filtersStore = useListingFilterStore();
const { initializeFilters, isAdmin, canSeeAgents } =
    useInitializeListingFilters(props.value);

const agents = ref(props.value.agents);

const officeOptions = computed(() => [
    { name: "No seleccionado", office_id: null },
    ...props.value.offices,
]);

const agentOptions = computed(() =>
    agents.value.length > 1
        ? [{ name_to_show: "No seleccionado", id: "all" }, ...agents.value]
        : agents.value
);

const statusOptions = computed(() => [
    { name: "No seleccionado", id: "all" },
    ...props.value.status_listings,
]);

onMounted(() => {
    // initializeFilters();
});

const fetchData = (only = ["listings"]) => {
    const filters = sanitizeFilters(filtersStore.formFilters);

    router.get(route("listings.index"), filters, {
        only,
        replace: true,
        onSuccess: (page) => {
            if (only.includes("agents")) {
                agents.value = page.props.agents;
            }
        },
    });
};

const debouncedFetch = debounce(() => fetchData(), 1000);

const clearFilters = () => {
    console.log(props.value?.user?.agent);
    filtersStore.clearFilters({
        agent_id: props.value?.user?.agent?.id,
    });
    const filters = sanitizeFilters(filtersStore.formFilters);

    // console.log(`Filters ${JSON.stringify(filters)}`);

    agents.value = [];
    router.get(route("listings.index"), filters, {
        only: ["listings", "agents"],
        replace: true,
    });
};

const sanitizeFilters = (filters) => {
    const result = {};
    for (const [key, value] of Object.entries(filters)) {
        if (value === "all" || value === 0 || typeof value === "boolean") {
            result[key] = value;
        } else if (value !== null && value !== "") {
            result[key] = value;
        }
    }
    return result;
};

const handleFilterChange = (key, value) => {
    filtersStore.formFilters[key] = value;

    if (key === "office_id") {
        // filtersStore.formFilters.agent_id = null;
        agents.value = [];
        filtersStore.formFilters.agent_id = null;
        fetchData(["listings", "agents"]);
    } else if (key === "agent_id") {
        if (!canSeeAgents) filtersStore.formFilters.agent_id = null;
        else fetchData();
    } else if (key === "status_id") {
        fetchData();
    }
};

const handleSearchChange = (value) => {
    filtersStore.formFilters.search = value;
    debouncedFetch();
};
</script>

<template>
    <div
        class="flex flex-wrap gap-4 my-5 mx-2 py-4 px-3 bg-indigo-100 rounded-lg"
    >
        <!-- <pre>{{ filtersStore.formFilters }}</pre> -->
        <div class="w-full lg:w-1/5" v-if="isAdmin">
            <label
                for="office_id"
                class="block text-md font-medium text-gray-700"
                >Oficina</label
            >
            <Select
                inputId="office_id"
                :modelValue="filtersStore.formFilters.office_id"
                @change="handleFilterChange('office_id', $event.value)"
                optionValue="office_id"
                optionLabel="name"
                :options="officeOptions"
                placeholder="No seleccionado"
                filter
                fluid
                class="w-full"
            />
        </div>

        <div class="w-full lg:w-1/5" v-if="canSeeAgents">
            <label
                for="agent_id"
                class="block text-md font-medium text-gray-700"
                >Agentes Asociados</label
            >
            <Select
                inputId="agent_id"
                :modelValue="filtersStore.formFilters.agent_id"
                @change="handleFilterChange('agent_id', $event.value)"
                optionValue="id"
                optionLabel="name_to_show"
                :options="agentOptions"
                placeholder="No seleccionado"
                filter
                fluid
                class="w-full"
            />
        </div>

        <div class="w-full lg:w-1/5">
            <label
                for="status_id"
                class="block text-md font-medium text-gray-700"
                >Estado de la Captación</label
            >
            <Select
                inputId="status_id"
                :modelValue="filtersStore.formFilters.status_id"
                @change="handleFilterChange('status_id', $event.value)"
                :options="statusOptions"
                optionLabel="name"
                optionValue="id"
                filter
                placeholder="No seleccionado"
                class="w-full"
            />
        </div>

        <div class="w-full lg:w-1/3">
            <label for="search" class="block text-md font-medium text-gray-700"
                >Búsqueda libre</label
            >
            <InputText
                type="text"
                inputId="search"
                placeholder="Buscar por MLSID"
                :modelValue="filtersStore.formFilters.search"
                @update:modelValue="handleSearchChange"
                class="w-full"
            />
        </div>

        <div class="w-full flex justify-end">
            <Button severity="secondary" @click="clearFilters">
                Limpiar Filtros
            </Button>
        </div>
    </div>
</template>
