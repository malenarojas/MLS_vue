<script setup>
import { router, useForm, usePage } from "@inertiajs/vue3";
import { useToast } from "primevue";
import { computed } from "vue";

const page = usePage();
const toast = useToast();
const permissions = usePage().props.permissions;
const storeQuality = permissions.includes("quality_control.store");
const listing = computed(() => page.props.listing);
const form = useForm({
    comment:
        listing.value.status_listing_id === 9
            ? ""
            : listing.value?.quality_control[0]?.comment,
    is_approve: false,
    key: listing.value.key,
});

const approveListing = (isApprove = false) => {
    form.is_approve = isApprove;
    form.key = listing.value.key;
    form.post(route("qualitycontrol.store", listing.value.key), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            toast.add({
                severity: "success",
                summary: "Exito",
                detail: "Comentario guardado",
                life: 5000,
            });

            // router.visit(route("qualitycontrol.index"));
        },
        onError: (errors) => {
            toast.add({
                severity: "error",
                summary: "Error",
                detail: errors,
                life: 5000,
            });

            // router.visit(route("qualitycontrol.index"));
        },
    });
};
</script>

<template>
    <div class="audit-logs">
        <!-- <pre>{{ listing.logs }}</pre> -->
        <div v-if="isLoading" class="text-gray-600">Cargando...</div>
        <div v-else-if="error" class="text-red-500">{{ error }}</div>

        <div
            v-else
            class="overflow-x-auto max-h-96 border rounded-lg shadow-lg"
        >
            <table class="table-auto w-full border-collapse bg-white">
                <!-- Cabecera fija -->
                <thead class="bg-blue-100 sticky top-0 z-10">
                    <tr>
                        <th
                            class="px-4 py-2 border text-left text-sm font-semibold"
                        >
                            Campo Editado
                        </th>
                        <th
                            class="px-4 py-2 border text-left text-sm font-semibold"
                        >
                            Valor Anterior
                        </th>
                        <th
                            class="px-4 py-2 border text-left text-sm font-semibold"
                        >
                            Valor Nuevo
                        </th>
                        <th
                            class="px-4 py-2 border text-left text-sm font-semibold"
                        >
                            Usuario
                        </th>
                        <th
                            class="px-4 py-2 border text-left text-sm font-semibold"
                        >
                            Fecha
                        </th>
                    </tr>
                </thead>

                <!-- Cuerpo de la tabla -->
                <tbody>
                    <tr
                        v-for="log in listing.logs"
                        :key="log.id"
                        class="hover:bg-gray-50"
                    >
                        <td
                            class="px-4 py-2 border text-sm truncate-cell"
                            :title="log.field_name"
                        >
                            {{ log.field_name }}
                        </td>
                        <td
                            class="px-4 py-2 border text-sm truncate-cell"
                            :title="log.old_value"
                        >
                            {{ log.old_value ?? "" }}
                        </td>
                        <td
                            class="px-4 py-2 border text-sm truncate-cell"
                            :title="log.new_value"
                        >
                            {{ log.new_value || "-" }}
                        </td>
                        <td
                            class="px-4 py-2 border text-sm truncate-cell"
                            :title="log.user?.name_to_show || 'Desconocido'"
                        >
                            {{ log.user?.name_to_show || "Desconocido" }}
                        </td>
                        <td class="px-4 py-2 border text-sm">
                            {{ log.created_at_human }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- <pre>{{ form }}</pre> -->

    <div class="py-5">
        <h3 class="font-semibold text-xl">Comentarios</h3>
        <Textarea
            v-model="form.comment"
            :readonly="!storeQuality || listing.status_listing_id !== 9"
            placeholder="Escribe un comentario"
            class="w-full h-40"
        />
        <div v-if="$page.props.errors?.error" class="text-red-500 py-2">
            {{ $page.props.errors.error }}
        </div>
        <!-- Mostrar si tiene los permisos y esta en estado revision -->
        <div
            class="flex gap-2"
            v-if="storeQuality && listing.status_listing_id === 9"
        >
            <Button
                type="button"
                label="Aprobar"
                severity="secondary"
                icon="pi pi-save"
                @click="() => approveListing(true)"
                :loading="form.processing"
            />
            <Button
                type="button"
                label="Rechazar"
                severity="secondary"
                icon="pi pi-save"
                @click="() => approveListing(false)"
                :loading="form.processing"
            />
        </div>
    </div>
</template>
