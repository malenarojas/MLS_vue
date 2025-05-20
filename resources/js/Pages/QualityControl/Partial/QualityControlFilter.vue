<script setup>
import { computed, ref, watch } from "vue";
import { useQualityControlStore } from "@/Stores/useQualityControlStore";
import { router, usePage } from "@inertiajs/vue3";
import debounce from "lodash/debounce";

const page = usePage();
const props = computed(() => page.props);
const permissions = props.value.permissions;
const showOfficeFilter = permissions.includes("listing.show_offices");
const showAgentFilter = permissions.includes("listing.show_agents");
const filtersStore = useQualityControlStore();

const agents = ref(props.value.agents);

const officeOptions = computed(() => [
    { name: "No seleccionado" },
    ...props.value?.offices,
]);

const agentOptions = computed(() =>
    props.value?.agents?.length > 1
        ? [
              { name_to_show: "No seleccionado", id: undefined },
              ...props.value?.agents,
          ]
        : props?.value?.agents
);

const statusOptions = computed(() =>
    props.value?.status?.length > 1
        ? [{ name: "No seleccionado", id: "all" }, ...props.value?.status]
        : props?.value?.status
);

const fetchAgentsByOffice = () => {
    const filters = {
        office_id: filtersStore.formFilters.office_id,
        status_id: filtersStore.formFilters.status_id,
        search: filtersStore.formFilters.search,
    };

    router.get(route("qualitycontrol.index"), filters, {
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

    router.get(route("qualitycontrol.index"), filters, {
        only: ["listings"],
        preserveState: true,
        replace: true,
    });
};

const debouncedFetchListings = debounce(fetchListings, 700);

const clearFilters = () => {
    filtersStore.clearFilters();
    agents.value = [];

    router.get(
        route("qualitycontrol.index"),
        {},
        {
            only: ["listings", "agents"],
            preserveState: true,
            replace: true,
        }
    );
};

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
        class="flex flex-wrap gap-4 my-5 mx-2 py-4 px-3 bg-slate-300 rounded-lg"
    >
        <!-- <pre>{{ filtersStore.formFilters }}</pre> -->
        <div class="w-full sm:w-1/2 lg:w-1/5" v-if="showOfficeFilter">
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

        <!-- <pre>{{ props }}</pre> -->
        <div class="w-full sm:w-1/2 lg:w-1/5" v-if="showAgentFilter">
            <label
                for="agent_id"
                class="block text-md font-medium text-gray-700"
            >
                Agentes
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

        <div class="w-full sm:w-1/2 lg:w-1/5">
            <label
                for="agent_id"
                class="block text-md font-medium text-gray-700"
            >
                Estados
            </label>
            <Select
                inputId="agent_id"
                v-model="filtersStore.formFilters.status_id"
                optionValue="id"
                optionLabel="name"
                :options="statusOptions"
                placeholder="No seleccionado"
                filter
                fluid
                class="w-full"
            />
        </div>

        <div class="w-full sm:w-1/2 lg:w-1/5">
            <label for="search" class="block text-md font-medium text-gray-700">
                Búscar por MLSID
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
