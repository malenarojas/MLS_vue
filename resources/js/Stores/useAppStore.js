import { defineStore } from "pinia";
import { useLocalStorage } from "@vueuse/core";

export const useAppStore = defineStore("appStore", {
    state: () => ({
        isOpenMenu: useLocalStorage("isOpenMenu", true),
        lang: useLocalStorage("lang", "ESB"),
    }),

    getters: {
        getOpenMenu: (state) => state.isOpenMenu,
        getLang: (state) => state.lang,
    },

    actions: {
        openCloseMenu(valor) {
            if (valor === null) this.isOpenMenu = !this.isOpenMenu;
            else this.isOpenMenu = valor;
        },
        setLang(valor) {
            this.lang = valor;
        },
    },
});
