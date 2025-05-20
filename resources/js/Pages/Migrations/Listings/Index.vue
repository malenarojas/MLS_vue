<script setup>
import { useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import { Checkbox } from "primevue";

const props = defineProps({
    status: Object,
});

const form = useForm({
    files: [],
    activar: false,
    status: ["0", "2", "3"],
    migrateAll: false,
    migrateFromFirst: false,
});

const statusForm = useForm({
    file: null,
    status: 6,
});

const statusOptions = [
    { label: "Pendiente", value: "0" },
    { label: "Completado", value: "1" },
    { label: "No found", value: "2" },
    { label: "Error", value: "3" },
];

const submit = () => {
    form.post(route("migrations.listings.store"), {
        onFinish: () => {},
    });
};

const activeSubmit = () => {
    console.log(statusForm.file);
    console.log(statusForm.status);
    statusForm.post(route("migrations.listings.excel"), {
        onFinish: () => {},
    });
};

const handleFileChange = (event) => {
    form.files = Array.from(event.target.files);
};
</script>

<template>
    <AppLayout>
        <!-- <pre>{{ form }}</pre> -->
        <div class="mx-auto p-6 bg-white rounded-lg shadow">
            <h1 class="text-2xl font-bold mb-4">Migración de Listing</h1>
            <form @submit.prevent="submit" class="space-y-4">
                <input
                    type="file"
                    accept=".xlsx,.xls,.json"
                    @change="handleFileChange"
                    multiple
                    class="text-sm"
                />
                <h3 class="text-xl font-medium">Opciones</h3>
                <div class="flex flex-wrap items-center gap-10">
                    <div class="flex gap-2">
                        <label class="font-semibold"
                            >Activar todos los Listings</label
                        >
                        <Checkbox binary v-model="form.activar" />
                    </div>

                    <!-- <pre>{{ form.status }}</pre> -->
                    <div class="flex flex-col gap-2">
                        <label class="font-semibold">Seleccionar estado</label>
                        <MultiSelect
                            v-model="form.status"
                            :options="statusOptions"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Seleccionar estado"
                            class=""
                        ></MultiSelect>
                    </div>

                    <!-- <div class="flex gap-2">
                        <label class="text-sm"
                            >Migrar todos (Ignorando el estado)</label
                        >
                        <Checkbox binary v-model="form.migrateAll" />
                    </div> -->

                    <div class="flex gap-2">
                        <label class="font-semibold"
                            >Migrar desde el primer listing</label
                        >
                        <Checkbox binary v-model="form.migrateFromFirst" />
                    </div>
                </div>

                <Button
                    type="submit"
                    :disabled="form.processing"
                    :loading="form.processing"
                    severity="info"
                    class="mr-2"
                >
                    {{
                        !form.processing
                            ? "Migrar datos"
                            : "...Migrando datos, no recarge la pestaña"
                    }}
                </Button>
                <Button
                    type="button"
                    v-if="form.processing"
                    @click="() => form.cancel()"
                >
                    Cancelar migración
                </Button>
            </form>
        </div>
        <div v-if="$page.props.errors?.error" class="text-red-500 py-2">
            {{ $page.props.errors.error }}
        </div>
        <div v-if="$page.props.errors?.exception" class="text-red-500 py-2">
            {{ $page.props.errors.exception }}
        </div>

        <div class="mt-20">
            <h3 class="text-2xl font-bold mb-5">Cambiar estados desde excel</h3>
            <form
                @submit.prevent="activeSubmit"
                class="space-y-4 flex flex-col"
            >
                <input
                    type="file"
                    accept=".xlsx,.xls,.json"
                    @change="
                        (event) => {
                            statusForm.file = event.target.files[0];
                        }
                    "
                    multiple
                    class="text-sm"
                />

                <Select
                    v-model="statusForm.status"
                    :options="props.status"
                    optionLabel="name"
                    optionValue="id"
                    class="w-1/3"
                />

                <Button
                    type="submit"
                    :disabled="statusForm.processing"
                    :loading="statusForm.processing"
                    severity="info"
                    class="mr-2 w-1/3"
                >
                    {{
                        !statusForm.processing
                            ? "Cambiar estado"
                            : "...Cambiando estado, no recarge la pestaña"
                    }}
                </Button>
            </form>
        </div>
    </AppLayout>
</template>
