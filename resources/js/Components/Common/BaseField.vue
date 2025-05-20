<script setup>
const props = defineProps({
    label: String,
    error: String,
    required: Boolean,
});
</script>

<template>
    <div class="flex flex-col gap-2">
        <label class="font-semibold inline-flex items-center gap-1">
            {{ label }}
            <span
                v-if="required"
                class="text-red-400 cursor-help"
                title="Campo obligatorio"
                >*</span
            >
        </label>

        <div :class="required ? 'bg-red-500 rounded-md' : ''">
            <div :class="required ? 'pl-1' : ''">
                <slot />
            </div>
        </div>

        <p
            v-if="error"
            class="text-red-500 text-[.8rem] mt-1 break-words max-w-xs"
        >
            {{ error }}
        </p>
    </div>
</template>
