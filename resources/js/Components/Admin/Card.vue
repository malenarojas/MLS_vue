<template>

    <div>
        <div class="font-bold text-sm translate-x-2 opacity-75">
            <i :class="icon" class="mr-1 text-emerald-600" /> {{ title }}
        </div>
        <div class="w-full rounded-lg p-6 border mt-1">
            <slot />
        </div>
    </div>

</template>
<script setup>

defineProps([
    'title',
    'icon'
]);

import { useSlots } from 'vue';
const slots = useSlots();

</script>