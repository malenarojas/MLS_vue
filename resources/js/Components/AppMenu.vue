<template>
    <aside
        class="absolute shadow-xl left-0 top-0 z-[51] flex h-screen w-[16.125rem] flex-col overflow-y-hidden bg-white duration-300 ease-linear dark: lg:static lg:translate-x-0"
        :class="{
            'translate-x-0': isOpenMenu,
            '-translate-x-full': !isOpenMenu,
        }"
        ref="target"
    >
        <!-- SIDEBAR HEADER -->
        <div
            class="flex items-center justify-between md:justify-center gap-2 px-6 py-5.5 lg:py-6.5"
        >
            <a href="/dashboard">
                <img
                    src="https://iconnect.stage.gryphtech.com/images/login/logo-remax-balloon.svg"
                    class="w-6 h-full py-5 mx-auto"
                />
            </a>

            <button
                ref="buttonRef"
                class="block lg:hidden"
                @click="() => storeApp.openCloseMenu()"
            >
                <svg
                    class="fill-current"
                    width="20"
                    height="18"
                    viewBox="0 0 20 18"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        d="M19 8.175H2.98748L9.36248 1.6875C9.69998 1.35 9.69998 0.825 9.36248 0.4875C9.02498 0.15 8.49998 0.15 8.16248 0.4875L0.399976 8.3625C0.0624756 8.7 0.0624756 9.225 0.399976 9.5625L8.16248 17.4375C8.31248 17.5875 8.53748 17.7 8.76248 17.7C8.98748 17.7 9.17498 17.625 9.36248 17.475C9.69998 17.1375 9.69998 16.6125 9.36248 16.275L3.02498 9.8625H19C19.45 9.8625 19.825 9.4875 19.825 9.0375C19.825 8.55 19.45 8.175 19 8.175Z"
                        fill=""
                    />
                </svg>
            </button>
        </div>
        <!-- SIDEBAR HEADER -->

        <div
            class="no-scrollbar flex flex-col overflow-y-auto duration-300 ease-linear"
        >
            <!-- Sidebar Menu -->
            <div
                class="bg-white h-screen rounded-xl dark: shadow-lg mb-2 p-4 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl"
            >
                <PanelMenu
                    :model="menuItems"
                    v-model:expandedKeys="expandedKeys"
                    class="bg-white rounded-xl dark:"
                >
                    <template #item="{ item }">
                        <div
                            @mouseenter="handleMouseEnter(item)"
                            @mouseleave="handleMouseLeave(item)"
                        >
                            <Link
                                v-if="item.route"
                                :href="route(item.route)"
                                class="flex items-center px-4 py-2 text-surface-700 dark:text-surface-0"
                            >
                                <span :class="item.icon" />
                                <span class="ml-2">{{ item.label }}</span>
                            </Link>
                            <a
                                v-else
                                v-ripple
                                class="flex items-center cursor-pointer text-surface-700 dark: px-4 py-2 max-w-72.5"
                                :target="item.target"
                            >
                                <span :class="item.icon" />
                                <span class="ml-2">{{ item.label }}</span>
                                <span
                                    v-if="item.items"
                                    class="pi pi-angle-down text-primary ml-auto"
                                />
                            </a>
                        </div>
                    </template>
                </PanelMenu>
            </div>
            <!-- Sidebar Menu -->
        </div>
    </aside>
</template>

<script setup>
// import { useMenuItems } from "@/Composables/menu-items/useMenuItems";
import { nextTick, onBeforeUnmount, onMounted, ref } from "vue";
import { onClickOutside } from "@vueuse/core";
import { useAppStore } from "@/Stores/useAppStore";
import { storeToRefs } from "pinia";
import { usePage, Link } from "@inertiajs/vue3";

const expandedKeys = ref({});
const target = ref(null);
const page = usePage();
const menuItems = ref(page.props.menus);
const hoverTimeouts = new Map();

// onClickOutside(target, () => {
//     openCloseMenu(false);
// });

function handleMouseEnter(item) {
    if (hoverTimeouts.has(item.key)) {
        clearTimeout(hoverTimeouts.get(item.key));
        hoverTimeouts.delete(item.key);
    }

    if (item.items?.length) {
        expandedKeys.value = {
            ...expandedKeys.value,
            [item.key]: true,
        };
    }
}

function handleMouseLeave(item) {
    if (item.items?.length) {
        // Esperar un poco antes de cerrar, para permitir pasar a los hijos
        const timeout = setTimeout(() => {
            const keys = { ...expandedKeys.value };
            delete keys[item.key];
            expandedKeys.value = keys;
            hoverTimeouts.delete(item.key);
        }, 1000); // Puedes ajustar el tiempo (ms)

        hoverTimeouts.set(item.key, timeout);
    }
}

function handleClickOutside(event) {
    const clickedInsideSidebar = target.value?.contains(event.target);
    const clickedToggleButton = event.target.closest("[data-menu-toggle]");

    console.log(
        `Eventos valores: ${clickedInsideSidebar} -- ${clickedToggleButton}`
    );

    if (!clickedInsideSidebar && !clickedToggleButton) {
        openCloseMenu(false);
    }
}

const storeApp = useAppStore();
const { isOpenMenu } = storeToRefs(storeApp);
const { openCloseMenu } = storeApp;
// const { data: menuItems, isLoading, isError } = useMenuItems();
onMounted(() => {
    document.addEventListener("mousedown", handleClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener("mousedown", handleClickOutside);
    hoverTimeouts.forEach((timeout) => clearTimeout(timeout));
    hoverTimeouts.clear();
});
</script>
