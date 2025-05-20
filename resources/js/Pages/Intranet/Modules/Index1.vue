<script setup>
defineProps({ modules: Array });
import AppLayout from "@/Layouts/AppLayout.vue";
import { router } from "@inertiajs/vue3";
</script>

<template>
    <AppLayout title="Listings">
        <h1>Módulos disponibles</h1>
        <ul>
            <li v-for="module in modules" :key="module.id">
                <button
                    @click="
                        router.visit(route('intranets.modules.show', module.id))
                    "
                >
                    Abrir {{ module.name }}
                </button>
            </li>
        </ul>
    </AppLayout>
</template>
