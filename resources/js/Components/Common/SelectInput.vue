<script setup>
import { computed, useAttrs } from "vue";
import BaseField from "./BaseField.vue";

const props = defineProps({
    modelValue: [String, Number, Object], // Soporta diferentes tipos de valores
    label: {
        type: String,
        default: "",
    },
    options: {
        type: Array,
        default: () => [],
    },
    optionValue: {
        type: String,
        default: "id",
    },
    optionLabel: {
        type: String,
        default: "name",
    },
    selectClass: {
        type: String,
        default: "",
    },
    error: {
        type: String,
        default: null, // Mensaje de error opcional
    },
    size: {
        type: String,
        default: "small",
    },
    filter: {
        type: Boolean,
        default: true,
    },
    required: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["update:modelValue"]);

// Computed para manejar v-model correctamente
const inputValue = computed({
    get: () => props.modelValue,
    set: (val) => emit("update:modelValue", val),
});

// Agregar una opción por defecto (sin seleccionar)
const computedOptions = computed(() => [
    { [props.optionLabel]: "No seleccionado", [props.optionValue]: null },
    ...props.options,
]);

const attrs = useAttrs();
</script>

<template>
    <BaseField :label="label" :required="required" :error="error">
        <Select
            v-model="inputValue"
            v-bind="attrs"
            :options="computedOptions"
            :optionValue="optionValue"
            :optionLabel="optionLabel"
            placeholder="Seleccione una opción"
            :class="selectClass"
            :size="size"
            :filter="filter"
            class="w-full"
        />
    </BaseField>
</template>
