import { defineStore } from "pinia";
import { ref } from "vue";

export const useAcquisitionStore = defineStore(
    "acquisition",
    () => {
        const isAcquisitionModalVisible = ref(false);

        const showAcquisitionModal = () => {
            isAcquisitionModalVisible.value = true;
        };

        const hideAcquisitionModal = () => {
            isAcquisitionModalVisible.value = false;
        };

        return {
            isAcquisitionModalVisible,
            showAcquisitionModal,
            hideAcquisitionModal,
        };
    },
    {
        persist: true,
    }
);
