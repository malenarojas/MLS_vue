import { ref } from "vue";
import { defineStore } from "pinia";

export const useQualityControlStore = defineStore("qualityControlStore", () => {
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
