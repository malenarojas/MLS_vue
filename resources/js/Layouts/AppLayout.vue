<template>
    <div class="flex h-screen overflow-y-hidden bg-white">
        <!-- Encabezado común -->
        <div
            v-if="isLoading.isLoading"
            class="absolute top-0 left-0 w-full h-screen z-[99] flex justify-center items-center bg-black bg-opacity-10"
        >
            <img
                class="w-14 h-auto absolute z-[99] opacity-60 ld ld-wander-v"
                :src="logo"
                alt="Remax BO"
                srcset=""
            />
            <div class="loader opacity-60"></div>
        </div>

        <div class="flex h-screen w-full overflow-hidden">
            <AppMenu></AppMenu>
            <div
                class="relative flex flex-1 flex-col overflow-y-auto overflow-x-hidden"
            >
                <AppTopBar></AppTopBar>
                <!-- Contenido dinámico de las páginas -->
                <main>
                    <div class="mx-auto animate__fadeIn max-w-screen-2xl p-4">
                        <slot></slot>
                    </div>
                </main>
            </div>
        </div>
    </div>
    <Toast />
    <ConfirmDialog />
</template>
|
<script setup>
import AppMenu from "@/Components/AppMenu.vue";
import AppTopBar from "@/Components/Header/AppTopBar.vue";
import { Toast, ConfirmDialog } from "primevue";
import { useIsLoadingStore } from "@/Stores/isLoadingCharts";
import { useAppStore } from "@/Stores/useAppStore";
import { storeToRefs } from "pinia";
import logo from "@/assets/img/logo-remax-balloon.svg";
import "@/assets/css/loading.min.css";
import "animate.css";

const storeApp = useAppStore();
const { isOpenMenu, getOpenMenu } = storeToRefs(storeApp);
const isLoading = useIsLoadingStore();
</script>

<style>
.loader {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    display: inline-block;
    border-top: 6px solid #004084;
    border-right: 6px solid transparent;
    box-sizing: border-box;
    animation: rotation 1s linear infinite;
}
.loader::after {
    content: "";
    box-sizing: border-box;
    position: absolute;
    left: 0;
    top: 0;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border-left: 6px solid #d11b2b;
    border-bottom: 6px solid transparent;
    animation: rotation 0.5s linear infinite reverse;
}
@keyframes rotation {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}
</style>
