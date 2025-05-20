<script setup>
import { inject, computed, useAttrs } from "vue";
import BaseField from "./BaseField.vue";
// import DatePicker from "primevue/calendar";

const props = defineProps({
    modelValue: [String, Date], // Permitir texto y objetos Date
    label: {
        type: String,
        required: true,
    },
    format: {
        type: String,
        default: "dd/mm/yy",
    },
    size: {
        type: String,
        default: "small",
    },
    error: {
        type: String,
        default: null, // Permite manejar errores automáticamente
    },
    formKey: {
        type: String,
        default: "form", // Nombre del form a inyectar
    },
});

// Intentar inyectar el form usando la clave dinámica con fallback seguro
const form = inject(props.formKey, { errors: {} });

const emit = defineEmits(["update:modelValue"]);

// Computed para manejar `v-model`
const inputValue = computed({
    get: () => props.modelValue,
    set: (val) => emit("update:modelValue", val),
});

const attrs = useAttrs();
</script>

<template>
    <BaseField :label="label" :required="required" :error="error">
        <DatePicker
            v-model="inputValue"
            :dateFormat="format"
            showIcon
            :size="size"
            v-bind="attrs"
            class="w-full"
        />
    </BaseField>
</template>
