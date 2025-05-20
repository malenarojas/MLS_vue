<script setup>
import { computed, onMounted, provide, ref } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { headerLigintCreateOption } from "@/Constants/listingCreateOption";
import DescriptionForm from "./Partial/DescriptionForm.vue";
import MultimediaForm from "./Partial/MultimediaForm.vue";
import DocumentForm from "./Partial/DocumentForm.vue";
import ListingHistory from "./Partial/ListingHistory.vue";
import MainForm from "./Partial/MainForm.vue";
import { Link, useForm, router } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
import ContactTab from "./Partial/ContactTab.vue";
import ListingHeader from "@/Components/Listings/ListingHeader.vue";
import apiClient from "@/src/constansts/axiosClient";
import { useConfirm } from "primevue/useconfirm";
import DateUtils from "../../Utils/date-utils";
import RadioButtonGroup from "@/Components/Common/RadioButtonGroup.vue";
import { useIsLoadingStore } from "@/Stores/isLoadingCharts";
import { useListingEditStore } from "@/Stores/useListingEditStore";

const copiedListingModal = ref(false);
// const multimediaTabRef = ref(null);
const editStore = useListingEditStore();
const loading = useIsLoadingStore();
const toast = useToast();
const confirm = useConfirm();
const props = defineProps({
    listing: Object,
    feature_ids: Array,
    transaction_type: Object,
    rooms: Array,
    filters: Object,
    states: Array,
    provinces: Array,
    cities: Array,
    zones: Array,
    type_floors: Array,
    permissions: Object,
});

const listing = computed(() => props.listing);
const permissions = props.permissions;
const createTransaction = permissions.includes("create transaction");
const disallowedStatusForCreateTransaction = [1, 6, 9, 10];
const trans = ref(
    listing.value?.transaction_type_id ?? props.transaction_type[0]?.id
);
const defaultLocation = {
    state_id: null,
    province_id: null,
    city_id: null,
    zone_id: null,
    number: "",
    unit_department: "",
    first_address: "",
    second_address: "",
    zip_code: "",
    district: "",
    access_number: "",
    show_addres_on_website: null,
    type_floor_id: null,
    latitude: null,
    longitude: null,
};

const location = listing.value?.location ?? defaultLocation;
// const { city, ...location } = locationData;

const transaction = listing.value?.transaction_type_id;
let commision = null;
if (transaction === 1) {
    commision = 2;
} else if (transaction === 2) {
    commision = 50;
} else {
    commision = 2.25;
}

// console.log(`Multimedia: ${JSON.stringify(listing.value?.multimedias)}`);
const form = useForm({
    ...props.listing,
    listing_information: {
        ...props.listing?.listing_information,
        transaction_type_id:
            props.transaction_type?.id ?? props.listing?.transaction_type_id,
        sale_sign: listing?.value?.listing_information?.sale_sign ?? 0,
        available_date:
            listing?.value?.listing_information?.available_date ??
            DateUtils.getCurrentDate(),
        year_construction:
            listing?.value?.listing_information?.year_construction ??
            DateUtils.getCurrentDate(),
        parking_slots: listing?.value?.listing_information?.parking_slots ?? 0,
        plant_numbers: listing?.value?.listing_information?.plant_numbers ?? 0,
        total_number_rooms:
            listing?.value?.listing_information?.total_number_rooms ?? 0,
        number_departments:
            listing?.value?.listing_information?.number_departments ?? 0,
        number_bedrooms:
            listing?.value?.listing_information?.number_bedrooms ?? 0,
        number_bathrooms:
            listing?.value?.listing_information?.number_bathrooms ?? 0,
        number_toiletrooms:
            listing?.value?.listing_information?.number_toiletrooms ?? 0,
    },
    contract_type_id: listing?.value?.contract_type_id ?? 1,
    is_published: listing?.value?.is_published ? true : false,
    rent_timeframe_id: listing?.value?.rent_timeframe_id ?? 3,
    date_of_listing:
        listing?.value?.date_of_listing ?? DateUtils.getCurrentDate(),
    contract_end_date:
        listing?.value?.contract_end_date ??
        DateUtils.getCurrentDatePlusMonths(6),
    location: {
        ...location,
        show_addres_on_website:
            listing.value?.location?.show_addres_on_website === 1
                ? true
                : false,
    },
    price: {
        amount: listing.value.price?.amount ?? 0,
    },
    commission_option: {
        recruitment_commission:
            listing.value?.commission_option?.recruitment_commission ??
            commision,
        type_recruitment_commission:
            listing.value?.commission_option?.type_recruitment_commission ??
            "P",
        sales_commission:
            listing.value?.commission_option?.sales_commission ?? commision,
        sales_commission_type:
            listing.value?.commission_option?.sales_commission_type ?? "P",
    },
    multimedias: listing.value?.multimedias ?? [],
    property_number: listing.value?.property_number ?? null,
    features: props.feature_ids ?? [],
    rooms: props.rooms ?? [],
    images: [],
    logs: [],
    is_sent_to_review: false,
    is_draft: false,
    public_documentation: listing.value?.public_documentation ?? [],
    private_documentation: listing.value?.private_documentation ?? [],
});
// Clonar el objeto `form` para comparar los cambios
const originalData = JSON.parse(JSON.stringify(form.data()));
// Proporcionar el formulario a los componentes secundarios
provide("form", form);

const saveDraft = () => {
    form.is_sent_to_review = false;
    form.is_draft = true;
    form.logs = [];
    submit("listings.update.draft");
};

const saveAndActivate = () => {
    form.is_sent_to_review = true;
    form.is_draft = false;
    submit("listings.update");
};

const save = () => {
    form.is_sent_to_review = false;
    form.is_draft = false;
    submit("listings.update");
};

const submit = (routeName = "listings.update") => {
    loading.setLoading(true);
    form.post(route(routeName, props.listing.key), {
        method: "put",
        preserveScroll: true,
        preserveState: true,
        onSuccess: (response) => {
            const listing = response.props?.listing;
            form.status_listing_id = listing.status_listing_id;

            router.visit(route("listings.edit", { key: listing.key }), {
                method: "get",
                only: ["rooms"],
                preserveScroll: true,
                onSuccess: () => {
                    toast.add({
                        severity: "success",
                        summary: "Éxito",
                        detail: "Listing actualizado correctamente",
                        group: "listing-update",
                        life: 5000,
                    });
                },
            });
        },
        onError: (error) => {
            form.errors = error;

            // Verificar si error tiene una estructura válida
            if (error) {
                Object.entries(error).forEach(([key, messages]) => {
                    if (Array.isArray(messages)) {
                        messages.forEach((message) => {
                            toast.add({
                                severity: "error",
                                summary: "Error",
                                life: 5000,
                                detail: message,
                            });
                        });
                    } else {
                        toast.add({
                            severity: "error",
                            summary: "Error",
                            life: 5000,
                            detail: messages,
                        });
                    }
                });
            } else {
                toast.add({
                    severity: "error",
                    summary: "Error",
                    life: 5000,
                    detail: "Ha ocurrido un error desconocido.",
                });
            }
        },
        onFinish: () => {
            loading.setLoading(false);
        },
    });
};

const copiedListing = () => {
    copiedListingModal.value = true;
    const data = {
        transaction_type_id: trans.value,
    };

    router.post(route("listings.copy", props.listing.key), data, {
        method: "post",
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            toast.add({
                severity: "success",
                summary: "Éxito",
                life: 5000,
                detail: "Listing copiado correctamente",
            });
            copiedListingModal.value = false;
        },
        onError: (error) => {
            console.log(error);
            toast.add({
                severity: "error",
                summary: "Error",
                life: 5000,
                detail: error.message,
            });
        },
    });
};

const confirmCreateTransaction = () => {
    confirm.require({
        message: "¿Esta seguro de crear una transación?",
        header: "Crear transacción",
        icon: "pi pi-money-bill",
        rejectProps: {
            label: "Cancelar",
            severity: "secondary",
            outlined: true,
        },
        acceptProps: {
            label: "Aceptar",
        },
        accept: () => {
            crearTransaccion();
            toast.add({
                severity: "success",
                summary: "Success",
                detail: "Aceptaste crear la transacción",
                life: 3000,
            });
        },
        reject: () => {
            toast.add({
                severity: "warn",
                summary: "Rejected",
                detail: "Has rechazado crear la transacción",
                life: 3000,
            });
        },
    });
};

async function crearTransaccion() {
    if (!listing.value?.agents[0]?.id) {
        toast.add({
            severity: "error",
            summary: "Error",
            life: 5000,
            detail: "No hay agente asignado a la propiedad.",
        });
        return;
    }

    const data = {
        agent_id: listing.value?.agents[0]?.id,
        listing_id: listing.value.id,
    };

    const response = await apiClient.post("/transactions/store", data);
    router.visit(
        `/transactions/create?transaction_id=${response.data.internal_id}`
    );
}

const downloadPdf = (key) => {
    window.open(route("listings.download.pdf", key), "_blank");
};

const openInWeb = () => {
    window.open(
        `https://listing.targetbit.com/detail/${listing.value?.key}`,
        "_blank"
    );
};

// onMounted(() => {
//     const el = multimediaTabRef.value?.$el;
//     if (el) el.id = "listing-multimedia";
// });
</script>

<template>
    <AppLayout title="Listings">
        <!-- <pre>Valor: {{ editStore.tab }}</pre> -->
        <div
            class="flex flex-wrap justify-between items-start px-1 py-2 bg-indigo-100 mb-2"
        >
            <div class="flex items-center gap-2">
                <h1 class="text-xl font-medium">
                    Número MLS{{ listing?.MLSID }}
                </h1>
                <p class="font-light text-sm text-gray-500">
                    {{ listing.agents[0]?.user?.name_to_show }}
                </p>
            </div>

            <div class="flex gap-2">
                <Link
                    v-if="listing.transactions[0]?.internal_id"
                    :href="
                        route(
                            'transactions.show',
                            listing.transactions[0]?.internal_id
                        )
                    "
                >
                    <Button
                        type="button"
                        severity="info"
                        label="Ir a transacción"
                        class="w-full"
                        size="small"
                    />
                </Link>

                <Button
                    v-else-if="
                        createTransaction &&
                        !disallowedStatusForCreateTransaction.includes(
                            listing.status_listing_id
                        )
                    "
                    type="button"
                    size="small"
                    @click="confirmCreateTransaction"
                    >Crear transanción</Button
                >
                <!-- <Link
                    :href="route('listings.web', listing.key)"
                </Link>
                > -->
                <Button
                    v-if="listing.status_listing_id === 2"
                    type="button"
                    severity="secondary"
                    label="Ver en la Págino Web"
                    size="small"
                    @click="() => openInWeb()"
                />
            </div>
        </div>

        <ListingHeader :listing="listing" />

        <form @submit.prevent="submit">
            <div class="mt-5 mx-2 rounded-lg">
                <Tabs
                    v-model:value="editStore.tab"
                    class="overflow-x-auto"
                    scrollable
                >
                    <TabList id="listings-tabs">
                        <Tab
                            v-for="option in headerLigintCreateOption"
                            :value="option.key"
                            :key="option.key"
                            :id="option.key"
                        >
                            {{ option.title }}
                        </Tab>
                    </TabList>
                    <TabPanels>
                        <TabPanel :value="headerLigintCreateOption[0].key">
                            <MainForm />
                        </TabPanel>
                        <TabPanel :value="headerLigintCreateOption[1].key">
                            <DescriptionForm />
                        </TabPanel>
                        <TabPanel :value="headerLigintCreateOption[2].key">
                            <MultimediaForm />
                        </TabPanel>
                        <TabPanel :value="headerLigintCreateOption[3].key">
                            <DocumentForm />
                        </TabPanel>
                        <TabPanel :value="headerLigintCreateOption[4].key">
                            <ContactTab />
                        </TabPanel>
                        <TabPanel :value="headerLigintCreateOption[5].key">
                            <ListingHistory />
                        </TabPanel>
                    </TabPanels>
                </Tabs>
                <div
                    class="flex flex-wrap justify-between px-2 py-3 bg-indigo-200 rounded-lg w-full"
                >
                    <div class="">
                        <Button
                            type="button"
                            severity="secondary"
                            label="Generar pdf"
                            icon="pi pi-file-pdf"
                            size="small"
                            @click="downloadPdf(listing.key)"
                        />
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <Button
                            type="button"
                            severity="contrast"
                            label="Copiar Listing"
                            icon="pi pi-copy"
                            size="small"
                            :loading="form.processing"
                            @click="copiedListingModal = true"
                        />

                        <Button
                            type="button"
                            severity="info"
                            label="Guardar en borrador"
                            size="small"
                            icon="pi pi-save"
                            :loading="form.processing"
                            @click="saveDraft"
                        />

                        <Button
                            v-if="listing.status_listing_id === 1"
                            type="button"
                            label="Guardar y Activar"
                            size="small"
                            icon="pi pi-save"
                            :loading="form.processing"
                            @click="saveAndActivate"
                        />
                        <Button
                            v-else
                            type="button"
                            label="Guardar"
                            size="small"
                            icon="pi pi-save"
                            :loading="form.processing"
                            @click="save"
                        />
                    </div>
                </div>
            </div>
        </form>

        <Dialog
            v-model:visible="copiedListingModal"
            modal
            header="Copiar Listing"
            :style="{ width: '25rem' }"
        >
            <label
                for="tran"
                class="mb-2 block text-sm font-medium text-gray-900"
            >
                Seleccionar tipo de transanción</label
            >
            <RadioButtonGroup
                v-model="trans"
                idPrefix="tran"
                :options="props.transaction_type"
            />
            <div class="flex justify-between gap-5 mt-5">
                <Button
                    type="button"
                    label="Cancelar"
                    icon="pi pi-times"
                    class="w-full md:w-1/3"
                    size="small"
                    @click="copiedListingModal = false"
                />
                <Button
                    type="button"
                    label="Copiar"
                    icon="pi pi-copy"
                    class="w-full md:w-1/3"
                    size="small"
                    @click="copiedListing()"
                />
            </div>
        </Dialog>

        <Toast group="listing-update" id="listing-update-toast"> </Toast>
    </AppLayout>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.4s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
