<script setup>
import { useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";

const form = useForm({
    // status: null,
});

const submit = () => {
    form.post(route("migrations.listings.storeReport"), {
        onFinish: () => {
            form.reset(); // Limpia el formulario
        },
    });
};
</script>

<template>
    <AppLayout>
        <div class="mx-auto p-6 bg-white rounded-lg shadow">
            <h1 class="text-2xl font-bold mb-4">
                Migrar Listings desde Reporte
            </h1>

            <form
                @submit.prevent="submit"
                class="space-y-6 flex flex-col w-full max-w-lg"
            >
                <!-- <div class="flex flex-col gap-2">
                    <label for="file" class="font-semibold"
                        >Seleccionar archivo</label
                    >
                    <input
                        id="file"
                        type="file"
                        accept=".xlsx,.xls,.json"
                        @change="(event) => (form.file = event.target.files[0])"
                        class="text-sm"
                        required
                    />
                </div> -->

                <!-- <div class="flex flex-col gap-2">
                    <label for="status" class="font-semibold">Seleccionar estado</label>
                    <Select
                        id="status"
                        v-model="form.status"
                        :options="props.statusOptions"
                        optionLabel="name"
                        optionValue="id"
                        class="w-full"
                        placeholder="Selecciona un estado"
                        required
                    />
                </div> -->

                <Button
                    type="submit"
                    :disabled="form.processing"
                    :loading="form.processing"
                    severity="info"
                    class="w-full"
                >
                    {{
                        !form.processing
                            ? "Subir y migrar archivo"
                            : "...Migrando, por favor espere"
                    }}
                </Button>
            </form>

            <div v-if="$page.props.errors?.error" class="text-red-500 mt-4">
                {{ $page.props.errors.error }}
            </div>
            <div v-if="$page.props.errors?.exception" class="text-red-500 mt-2">
                {{ $page.props.errors.exception }}
            </div>
        </div>
    </AppLayout>
</template>
