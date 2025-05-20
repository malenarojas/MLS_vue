<script setup>
import { computed, ref, watch } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import debounce from "lodash/debounce";
import { useListingFilterStore } from "@/Stores/useListingFilterStore";

const page = usePage();
const props = computed(() => page.props);
const permissions = props.value.permissions;
const showOfficeFilter = permissions.includes("listing.show_offices");
const showAgentFilter = permissions.includes("listing.show_agents");
const filtersStore = useListingFilterStore();

// Inicializar filtros desde los props, si existen
if (props.value.filters) {
    filtersStore.formFilters.office_id = props.value.filters.office_id
        ? Number(props.value.filters.office_id)
        : null;
    filtersStore.formFilters.agent_id = props.value.filters.agent_id
        ? Number(props.value.filters.agent_id)
        : null;
    filtersStore.formFilters.status_id = props.value.filters.status_id
        ? Number(props.value.filters.status_id)
        : null;
    filtersStore.formFilters.search = props.value.filters.search ?? "";
}

const agents = ref(props.value.agents);

const officeOptions = computed(() => [
    { name: "No seleccionado", id: "all" },
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

const fetchAgentsByOffice = () => {
    const filters = {
        office_id: filtersStore.formFilters.office_id,
        status_id: filtersStore.formFilters.status_id,
        search: filtersStore.formFilters.search,
    };

    router.get(route("listings.index"), filters, {
        preserveState: true,
        replace: true,
        only: ["listings", "agents"],
        onSuccess: (response) => {
            agents.value = response.props.agents;
        },
    });
};

const fetchListings = () => {
    const filters = {
        office_id: filtersStore.formFilters.office_id,
        agent_id: filtersStore.formFilters.agent_id,
        status_id: filtersStore.formFilters.status_id,
        search: filtersStore.formFilters.search,
    };

    router.get(route("listings.index"), filters, {
        only: ["listings"],
        preserveState: true,
        replace: true,
    });
};

const debouncedFetchListings = debounce(fetchListings, 700);

const clearFilters = () => {
    filtersStore.formFilters.office_id = null;
    filtersStore.formFilters.agent_id = null;
    filtersStore.formFilters.status_id = null;
    filtersStore.formFilters.search = "";
    agents.value = [];

    router.get(
        route("listings.index"),
        {},
        {
            only: ["listings", "agents"],
            preserveState: true,
            replace: true,
        }
    );
};

// Computed para agrupar los filtros y detectar cambios
const currentFilters = computed(() => ({
    office_id: filtersStore.formFilters.office_id,
    agent_id: filtersStore.formFilters.agent_id,
    status_id: filtersStore.formFilters.status_id,
    search: filtersStore.formFilters.search,
}));

watch(
    currentFilters,
    (newVal, oldVal) => {
        if (newVal.office_id !== oldVal.office_id) {
            console.log(`Se cambió la oficina a ${newVal.office_id}`);
            // Reiniciamos el agente y actualizamos agentes
            filtersStore.formFilters.agent_id = null;
            agents.value = [];
            fetchAgentsByOffice();
        }
        if (newVal.agent_id !== oldVal.agent_id) {
            console.log(`Se cambió el agente a ${newVal.agent_id}`);

            fetchListings();
        }
        if (newVal.status_id !== oldVal.status_id) {
            console.log(`Se cambió el estado a ${newVal.status_id}`);
            debouncedFetchListings();
        }
        if (newVal.search !== oldVal.search) {
            console.log(`Nueva búsqueda: ${newVal.search}`);
            debouncedFetchListings();
        }
    },
    { deep: true }
);
</script>

<template>
    <div
        class="flex flex-wrap gap-4 my-5 mx-2 py-4 px-3 bg-indigo-100 rounded-lg"
    >
        <div class="w-full lg:w-1/5" v-if="showOfficeFilter">
            <label
                for="office_id"
                class="block text-md font-medium text-gray-700"
            >
                Oficina
            </label>
            <Select
                inputId="office_id"
                v-model="filtersStore.formFilters.office_id"
                optionValue="office_id"
                optionLabel="name"
                :options="officeOptions"
                placeholder="No seleccionado"
                filter
                fluid
                class="w-full"
            />
        </div>

        <div class="w-full lg:w-1/5" v-if="showAgentFilter">
            <label
                for="agent_id"
                class="block text-md font-medium text-gray-700"
            >
                Agentes Asociados
            </label>
            <Select
                inputId="agent_id"
                v-model="filtersStore.formFilters.agent_id"
                optionValue="id"
                optionLabel="name_to_show"
                :options="agentOptions"
                placeholder="No seleccionado"
                filter
                fluid
                class="w-full"
            />
        </div>

        <!-- <pre>{{ statusOptions }}</pre> -->

        <div class="w-full lg:w-1/5">
            <label
                for="status_id"
                class="block text-md font-medium text-gray-700"
            >
                Estado de la Captación
            </label>
            <Select
                inputId="status_id"
                v-model="filtersStore.formFilters.status_id"
                :options="statusOptions"
                optionLabel="name"
                optionValue="id"
                filter
                placeholder="No seleccionado"
                class="w-full"
            />
        </div>

        <div class="w-full lg:w-1/3">
            <label for="search" class="block text-md font-medium text-gray-700">
                Búsqueda libre
            </label>
            <InputText
                type="text"
                inputId="search"
                placeholder="Buscar por MLSID"
                v-model="filtersStore.formFilters.search"
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
