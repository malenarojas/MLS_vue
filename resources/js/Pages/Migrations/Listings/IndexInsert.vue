<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm } from "@inertiajs/vue3";

const form = useForm({
    file: null,
});

const submit = () => {
    form.post(route("migrations.listings.storeInsert"), {
        onFinish: () => {},
    });
};
</script>

<template>
    <AppLayout>
        <div class="mx-auto p-6 bg-white rounded-lg shadow">
            <h1 class="text-2xl font-bold mb-4">Insertar Listings</h1>
            <form @submit.prevent="submit" class="space-y-4">
                <div class="flex gap-2">
                    <input
                        type="file"
                        accept=".xlsx,.xls"
                        @change="form.file = $event.target.files[0]"
                        class="block w-full text-sm"
                    />
                    <!-- <div class="flex gap-2">
                        <label class="text-sm" for=""
                            >Activar todos los Listings</label
                        >
                        <Checkbox binary v-model="form.activar" />
                    </div> -->
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
    </AppLayout>
</template>
