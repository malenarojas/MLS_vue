import { useLocalStorage } from "@vueuse/core";
import { defineStore } from "pinia";

export const useListingFilterStore = defineStore("listingFilters", {
    state: () => ({
        formFilters: useLocalStorage("listingFilters", {
            office_id: null,
            agent_id: null,
            status_id: null,
            search: "",
        }),
    }),
    actions: {
        setFilter(key, value) {
            this.formFilters[key] = value;
        },
        clearFilters(filters = {}) {
            this.formFilters = {
                office_id: null,
                agent_id: filters?.agent_id ? Number(filters.agent_id) : null,
                status_id: 2,
                search: "",
            };
        },
    },
    // persist: true,
});

/*
export const useListingFilterStore = defineStore("filters", () => {
    const formFilters = ref({
        office_id: null,
        agent_id: null,
        status_id: null,
        search: "",
    });

    function setFilter(key, value) {
        formFilters.value[key] = value;
    }

    function clearFilters() {
        formFilters.value = {
            office_id: null,
            agent_id: null,
            status_id: null,
            search: "",
        };
    }

    return {
        formFilters,
        setFilter,
        clearFilters,
    };
});
*/
