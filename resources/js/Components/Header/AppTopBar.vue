<template>
    <header
        class="sticky top-0 z-[51] flex w-full bg-white dark: drop-shadow-1 shadow-lg dark: dark:"
    >
        <div
            class="flex flex-grow items-center justify-between md:justify-end shadow-2 md:px-2 2xl:px-11"
        >
            <button
                class="p-2 dark: lg:hidden"
                @click.prevent="storeApp.openCloseMenu()"
                data-menu-toggle
            >
                <i class="pi pi-bars" style="font-size: 1.3rem" />
            </button>
            <div class="flex justify-center items-center gap-3 2xsm:gap-7">
                <ul class="flex items-center gap-2 2xsm:gap-4">
                    <li>
                        <!-- Dark Mode Toggler -->
                        <DarkModeSwitcher />
                        <!-- Dark Mode Toggler -->
                    </li>
                    <li>
                        <ThemeSwitcher />
                    </li>

                    <div class="card flex justify-center">
                        <Button
                            type="button"
                            text
                            class="rounded-full"
                            @click="toggle"
                            aria-haspopup="true"
                            aria-controls="overlay_menu"
                        >
                            <img
                                class="w-10 h-10 my-auto rounded-full transition-transform duration-300 hover:scale-110 object-cover"
                                :src="imgUser"
                                alt="Profile"
                            />
                        </Button>

                        <Menu
                            ref="menu"
                            id="overlay_menu"
                            :model="itemsm"
                            :popup="true"
                            class="w-full md:w-60"
                        >
                            <template #submenulabel="{ item }">
                                <span class="text-primary font-bold">{{
                                    item.label
                                }}</span>
                            </template>
                            <template #item="{ item, props }">
                                <a
                                    v-if="item.visible"
                                    v-ripple
                                    class="flex items-center"
                                    v-bind="props.action"
                                >
                                    <span :class="item.icon" />
                                    <span>{{ item.label }}</span>
                                    <Badge
                                        v-if="item.badge"
                                        class="ml-auto"
                                        :value="item.badge"
                                    />
                                    <span
                                        v-if="item.shortcut"
                                        class="ml-auto border border-surface rounded bg-emphasis text-muted-color text-xs p-1"
                                        >{{ item.shortcut }}</span
                                    >
                                </a>
                            </template>
                        </Menu>
                    </div>
                </ul>

                <!-- User Area
				<DropdownUser />
				 -->
                <!-- User Area -->
            </div>
        </div>
    </header>
    <Dialog
        position="top"
        v-model:visible="isLoginModalVisible"
        modal
        header="Login As"
        class="w-full md:w-2/3 lg:w-1/2"
    >
        <FormOfficeAgent />
    </Dialog>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useAppStore } from "@/Stores/useAppStore";
import FormOfficeAgent from "@/Components/Users/FormOfficeAgent.vue";
import { storeToRefs } from "pinia";
import { useIsLoadingStore } from "@/Stores/isLoadingCharts";
import { usePage, router } from "@inertiajs/vue3";
import apiClient from "@/src/constansts/axiosClient";

const storeApp = useAppStore();
const page = usePage();

// pero omita cualquier acción o propiedad no reactiva (no ref/reactiva)
const user = page.props.user;
const role = page.props.role;
let isLoginModalVisible = ref(false);

const { isOpenMenu, getOpenMenu } = storeToRefs(storeApp);

// la acción de incremento simplemente puede ser desestructurada
const { openCloseMenu } = storeApp;

const nombre = ref(null);
const rol = ref(false);
const authUser = ref(false);
const listingFilter = useListingFilterStore();

const changeU = async () => {
    const response = await apiClient.post("/back-to-account");
    if (response.status == 200) {
        const cleanUrl = window.location.pathname;
        console.log(`URL ${cleanUrl}`);

        router.visit(cleanUrl, {
            method: "get",
            replace: true,
            preserveScroll: true,
        });

        listingFilter.clearFilters();
        // router.reload({
        //     preserveUrl: false,
        // });
        // window.location.reload();
    }
};
const loadingStore = useIsLoadingStore();

const toggleLoading = (isLoading) => {
    loadingStore.setLoading(isLoading);
};

const toggle = (event) => {
    menu.value.toggle(event);
};
defineProps({
    topbarMenuActive: false,
    menuActive: false,
    menuMode: "static",
    items: [],
    activeInlineProfile: false,
});

onMounted(() => {
    nombre.value = user.first_name;
    if (
        role?.value == "Super Administrador" ||
        role?.value == "Broker" ||
        role?.value == "Administrador"
    ) {
        rol.value = true;
    } else {
        rol.value = false;
    }

    // if (auth_login.value !== null) {
    // 	authUser.value = true;
    // } else {
    // 	authUser.value = false;
    // }
});
const menu = ref();
const itemsm = ref([
    {
        label: nombre,
        items: [
            {
                visible: page.props.is_logued_as,
                label: "Volver a mi cuenta",
                icon: "pi pi-replay",
                command: () => {
                    changeU();
                },
            },
            {
                visible: [
                    "Super Administrador",
                    "Broker",
                    "Administrador",
                ].includes(role),
                label: "Login as",
                icon: "pi pi-sign-in",
                command: () => {
                    isLoginModalVisible.value = !isLoginModalVisible.value;
                },
            },
            {
                visible: true,
                label: "Salir",
                icon: "pi pi-sign-out",
                command: () => {
                    logout();
                },
            },
        ],
    },
]);
// emits

const logout = async () => {
    // toggleLoading(true)
    // try {
    // 	await storeAuth.logout();
    // 	toggleLoading(false)
    // 	navigateTo("/login"); // Redirige tras el login
    // } catch (err) {}
    localStorage.removeItem("lastFilters");
    localStorage.removeItem("lastCombinedListings");
    localStorage.removeItem("agentFilters");
    router.post("/logout");
};

import imgUser from "@/assets/img/profile-image.png";
import { useListingFilterStore } from "@/Stores/useListingFilterStore";
// methods
</script>

<style lang="scss">
::v-deep(.p-dropdown-label) {
    width: 50px !important;
}
.p-dropdown-label {
    width: 55px !important;
    padding: 5px 5px !important;
}
::v-deep(.p-dropdown-item) {
    width: 100% !important;
    justify-content: space-between !important;
}
.p-dropdown-item {
    width: 100% !important;
    justify-content: space-between !important;
}
</style>
