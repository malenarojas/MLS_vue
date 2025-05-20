<template>
    <AppLayout title="Dashboard">
        <div class="p-2">

            <h1 class="title"> <i class="pi pi-sliders-h mr-1" />Configuraciones de administrador</h1>

            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <ExchangeRate
                    :exchange = "exchange" />
            </div>
        </div>
    </AppLayout>

    <Toast />
</template>

<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import ExchangeRate from '@/Components/Admin/Cards/ExchangeRate.vue';
    import { Toast } from 'primevue';

    const props = defineProps({
        exchange : String
    });
</script>