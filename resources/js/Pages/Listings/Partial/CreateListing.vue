<script setup>
import { computed, defineProps, defineEmits, watchEffect } from "vue";
import { router, useForm, usePage } from "@inertiajs/vue3";
import { useListingEditStore } from "@/Stores/useListingEditStore";

const props = defineProps({
    visible: Boolean,
});

const emit = defineEmits(["close"]); // Definir evento de cierre

// Datos obtenidos de Inertia
const page = usePage();
const pageProps = usePage().props; // Datos obtenidos de Inertia
const permissions = pageProps.permissions || [];
const showOfficeFilter = permissions.includes("listing.create.select_office");
const showAgentFilter = permissions.includes("listing.create.select_agent");

// Se añade una opción "Ninguno" en los selects
const offices = computed(() => [
    { id: null, name: "Ninguno" },
    ...(page.props.offices || []),
]);
const agents = computed(() => [
    { id: null, name: "Ninguno" },
    ...(page.props.agents || []),
]);
const areas = computed(() => page.props.areas || []);
const transactionTypes = computed(() => page.props.transaction_types || []);
const subtypeProperties = computed(() => page.props.subtype_properties || []);

const form = useForm({
    office_id: null,
    agent_id: null,
    area_id: 2,
    transaction_type_id: 1,
    subtype_property_id: null,
});

watchEffect(() => {
    if (form.office_id) {
        console.log(`Se cambio la oficina ${form.office_id}`);
        router.visit(route("listings.index"), {
            method: "get",
            data: { office_id: form.office_id },
            only: ["agents"], // Solo se actualiza la lista de agentes
            preserveState: true,
            replace: true,
            preserveUrl: true,
        });
    }
    form.agent_id = null; // Resetear el agente seleccionado
});

const editStore = useListingEditStore();

const submitForm = () => {
    form.post(route("listings.store"), {
        preserveScroll: true,
        onSuccess: () => {
            editStore.setTab();
            emit("close"); // Cierra el modal después del éxito
        },
    });
};
</script>

<template>
    <!-- Modal de Captación -->
    <Dialog
        v-model:visible="props.visible"
        modal
        header="Nueva Captación"
        class="w-full md:w-2/3 lg:w-1/2"
        @update:visible="emit('close')"
    >
        <form @submit.prevent="submitForm" class="flex flex-col gap-6">
            <!-- Oficina -->
            <div class="flex flex-col gap-2" v-if="showOfficeFilter">
                <IftaLabel>
                    <Select
                        inputId="office_id"
                        v-model="form.office_id"
                        :options="offices"
                        optionValue="office_id"
                        optionLabel="name"
                        placeholder="Selecciona oficina"
                        fluid
                        filter
                        size="small"
                        class="w-full"
                    />
                    <label for="office_id">Oficina</label>
                </IftaLabel>
                <p v-if="form.errors.office_id" class="text-red-500 text-sm">
                    {{ form.errors.office_id }}
                </p>
            </div>

            <!-- Agente -->
            <div class="flex flex-col gap-2" v-if="showAgentFilter">
                <IftaLabel>
                    <Select
                        inputId="agent_id"
                        v-model="form.agent_id"
                        :options="agents"
                        optionValue="id"
                        optionLabel="name_to_show"
                        placeholder="Selecciona un agente"
                        fluid
                        filter
                        size="small"
                        class="w-full"
                    />
                    <label for="agent_id">Agente</label>
                </IftaLabel>
                <p v-if="form.errors.agent_id" class="text-red-500 text-sm">
                    {{ form.errors.agent_id }}
                </p>
            </div>

            <!-- Segmento del mercado -->
            <div class="flex flex-col gap-2">
                <label class="font-medium" for="area_id"
                    >Segmento del mercado</label
                >
                <RadioButtonGroup v-model="form.area_id">
                    <div
                        v-for="area in areas"
                        :key="area.id"
                        class="flex items-center gap-2 mr-4"
                    >
                        <RadioButton
                            :inputId="area.id ? 'area_' + area.id : 'area_none'"
                            :value="area.id"
                        />
                        <label
                            :for="area.id ? 'area_' + area.id : 'area_none'"
                            >{{ area.name }}</label
                        >
                    </div>
                </RadioButtonGroup>
                <p v-if="form.errors.area_id" class="text-red-500 text-sm">
                    {{ form.errors.area_id }}
                </p>
            </div>

            <!-- Tipo de Transacción -->
            <div class="flex flex-col gap-2">
                <label class="font-medium" for="transaction_type_id"
                    >Tipo de transacción</label
                >
                <RadioButtonGroup v-model="form.transaction_type_id">
                    <div
                        v-for="transaction in transactionTypes"
                        :key="transaction.id"
                        class="flex items-center gap-2 mr-4"
                    >
                        <RadioButton
                            :inputId="
                                transaction.id
                                    ? 'tran_' + transaction.id
                                    : 'tran_none'
                            "
                            :value="transaction.id"
                        />
                        <label
                            :for="
                                transaction.id
                                    ? 'tran_' + transaction.id
                                    : 'tran_none'
                            "
                        >
                            {{ transaction.name }}
                        </label>
                    </div>
                </RadioButtonGroup>
                <p
                    v-if="form.errors.transaction_type_id"
                    class="text-red-500 text-sm"
                >
                    {{ form.errors.transaction_type_id }}
                </p>
            </div>

            <!-- Tipo de Propiedad -->
            <div class="flex flex-col gap-2">
                <IftaLabel>
                    <Select
                        inputId="subtype_property_id"
                        v-model="form.subtype_property_id"
                        :options="subtypeProperties"
                        optionValue="id"
                        optionLabel="name"
                        placeholder="Selecciona tipo de propiedad"
                        fluid
                        filter
                        size="small"
                        class="w-full"
                    />
                    <label for="subtype_property_id">Tipo de Propiedad</label>
                </IftaLabel>
                <p
                    v-if="form.errors.subtype_property_id"
                    class="text-red-500 text-sm"
                >
                    {{ form.errors.subtype_property_id }}
                </p>
            </div>

            <div class="flex justify-between gap-5 mt-10">
                <Button
                    type="button"
                    severity="danger"
                    class="w-full sm:w-1/3 px-4 py-2"
                    @click="emit('close')"
                >
                    Cancelar
                </Button>
                <Button
                    type="submit"
                    class="w-full sm:w-1/3 px-4 py-2"
                    :disabled="form.processing"
                >
                    {{ form.processing ? "Enviando..." : "Siguiente" }}
                </Button>
            </div>
        </form>
    </Dialog>
</template>
