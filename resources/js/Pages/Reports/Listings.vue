<script setup>
import { computed, ref } from "vue";
import BaseField from "@/Components/Common/BaseField.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { USER_ROLES } from "@/Constants/roles";

const props = defineProps({
    user: Object,
    offices: Object,
    listings_status: Object,
});

const params = ref({
    office_id: null,
    status_id: null,
});

const officeOptions = computed(() => [
    { name: "No seleccionado", office_id: null },
    ...props?.offices,
]);

const statusOptions = computed(() => [
    { name: "No seleccionado", status_id: null },
    ...props?.listings_status,
]);

const viewOffices = computed(
    () => props?.user?.roles[0]?.name === USER_ROLES.ADMIN
);

const handleExport = () => {
    window.open(
        route("listings.download.excel", {
            ...params.value,
        }),
        "_blank"
    );
};
</script>

<template>
    <AppLayout>
        <!-- <pre>{{ user.roles[0].name }}</pre> -->
        <div class="p-2">
            <h1 class="font-bold text-2xl opacity-75">
                <i class="pi pi-file-check"></i> Captaciones
            </h1>
        </div>

        <!-- <pre>{{ params }}</pre> -->

        <div
            class="mt-4 flex justify-between items-end gap-4 bg-indigo-50 border-indigo-200 border p-2"
        >
            <div class="flex gap-2">
                <BaseField label="Oficina" v-if="viewOffices">
                    <Select
                        v-model="params.office_id"
                        :options="officeOptions"
                        option-value="office_id"
                        option-label="name"
                        placeholder="Seleccionar Oficina"
                        filter
                        class="w-56"
                    />
                </BaseField>
                <BaseField label="Estado de Listing">
                    <Select
                        v-model="params.status_id"
                        :options="statusOptions"
                        option-value="id"
                        option-label="name"
                        placeholder="Seleccionar Estado"
                        filter
                        class="w-56"
                    />
                </BaseField>
            </div>
            <div>
                <Button @click="handleExport">Exportar excel</Button>
            </div>
        </div>
    </AppLayout>
</template>
