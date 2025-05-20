import { headerLigintCreateOption } from "@/Constants/listingCreateOption";
import { useLocalStorage } from "@vueuse/core";
import { defineStore } from "pinia";

const key = "listingEdit";

export const useListingEditStore = defineStore(key, {
    state: () => ({
        tab: useLocalStorage(key, headerLigintCreateOption[0].key),
    }),
    actions: {
        setTab(tab = null) {
            if (tab === null) {
                this.tab = headerLigintCreateOption[0].key;
            } else {
                this.tab = tab;
            }
        },
        setHistortTab() {
            this.tab =
                headerLigintCreateOption[
                    headerLigintCreateOption.length - 1
                ].key;
        },
    },
    // persist: true,
});
