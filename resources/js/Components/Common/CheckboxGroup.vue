<script setup>
import { computed, useAttrs } from "vue";
// import Checkbox from "primevue/checkbox";

const props = defineProps({
    modelValue: [Boolean, Array], // Soporta checkbox binario y múltiple
    label: {
        type: String,
        default: null,
    },
    options: {
        type: Array,
        default: null, // Si no hay opciones, asumimos un checkbox binario
    },
    optionValue: {
        type: String,
        default: "value",
    },
    optionLabel: {
        type: String,
        default: "label",
    },
    checkboxClass: {
        type: String,
        default: "",
    },
    binary: {
        type: Boolean,
        default: false, // Define si es un checkbox binario
    },
    binaryLabel: {
        type: String,
        default: null,
    },
    error: {
        type: String,
        default: null, // Mensaje de error opcional
    },
});

const emit = defineEmits(["update:modelValue"]);

const inputValue = computed({
    get: () => props.modelValue,
    set: (val) => emit("update:modelValue", val),
});

const attrs = useAttrs();
</script>

<template>
    <div :class="{ 'has-error': !!error }">
        <label v-if="label" class="text-base md:text-lg font-semibold">
            {{ label }}
        </label>

        <!-- Checkbox binario -->
        <div v-if="binary" class="flex items-center gap-2 mt-2">
            <Checkbox
                :inputId="label"
                binary
                v-model="inputValue"
                v-bind="attrs"
                :class="checkboxClass"
            />
            <label v-if="binaryLabel" :for="label">{{ binaryLabel }}</label>
        </div>

        <!-- Checkbox múltiple -->
        <div v-else class="flex flex-wrap gap-4 mt-2">
            <div
                v-for="(option, index) in options"
                :key="index"
                class="flex items-center gap-2"
            >
                <Checkbox
                    :inputId="`${label}-${index}`"
                    :value="option[optionValue]"
                    v-model="inputValue"
                    v-bind="attrs"
                    :class="checkboxClass"
                />
                <label :for="`${label}-${index}`">
                    {{ option[optionLabel] }}
                </label>
            </div>
        </div>

        <!-- Mostrar error si existe -->
        <p v-if="error" class="text-red-500 text-sm mt-1">
            {{ error }}
        </p>
    </div>
</template>
