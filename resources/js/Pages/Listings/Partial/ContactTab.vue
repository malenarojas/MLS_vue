<script setup>
import { inject } from "vue";
import OwnerCard from "./OwnerCard.vue";

const form = inject("form", { errors: {} });
</script>

<template>
    <h2 class="font-semibold text-2xl mb-2">Propietarios</h2>
    <div
        v-if="form?.owners"
        class="w-full grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2"
    >
        <OwnerCard v-for="owner in form.owners" :owner />
    </div>
    <div v-else>
        <p>No existen contacto del propietario</p>
    </div>
</template>
