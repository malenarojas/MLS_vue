<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, onMounted } from 'vue'
import { Head, usePage, Link } from '@inertiajs/vue3';
import { FilterMatchMode } from '@primevue/core/api';
const tabla_categorias = ref()
const titulo = "Roles"

onMounted(() => {
    tabla_categorias.value = usePage().props.roles;
});


const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    name: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    'country.name': { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    representative: { value: null, matchMode: FilterMatchMode.IN },
    status: { value: null, matchMode: FilterMatchMode.EQUALS },
    verified: { value: null, matchMode: FilterMatchMode.EQUALS }
});

</script>
<template>

    <Head title="Roles" />
    <AppLayout :pagina="[{ 'label': titulo, link: false }]">

        <div
            class="px-4 mb-4 bg-white col-span-6 py-5 rounded-lg shadow-lg lg:col-span-5 dark:border-gray-700  dark:bg-gray-800">
            <div class=" px-5 pb-2 col-span-full flex justify-between items-center">
                <h5 class="text-2xl font-medium">{{ titulo }}</h5>
            </div>
            <div class="align-middle">

                <DataTable v-model:filters="filters" :value="tabla_categorias" :paginator="true" :rows="10"
                    :rowsPerPageOptions="[5, 10, 20, 50]">
                    <template #header size="small" class="bg-secondary-900">
                        <div class="flex justify-content-end text-md">
                            <InputText v-model="filters['global'].value" placeholder="Buscar" />
                        </div>
                    </template>
                    <template #empty> No existe Resultado </template>
                    <template #loading> Cargando... </template>
                    <Column field="id" header="ID"></Column>
                    <Column field="name" header="Nombre" sortable></Column>
                    <Column header="Acciones" style="width:100px">
                        <template #body="slotProps">

                            <Link :href="route('roles.edit', slotProps.data.id)"
                                class="inline-block rounded bg-blue-900 px-2 py-1 mx-auto text-white text-sm font-medium hover:bg-blue-700">
                            Permisos
                            </Link>
                        </template>
                    </Column>

                </DataTable>

            </div>

        </div>

    </AppLayout>
</template>


<style type="text/css" scoped></style>
