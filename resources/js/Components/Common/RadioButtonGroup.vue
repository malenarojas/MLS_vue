<script setup>
import { computed, useAttrs } from "vue";
// import RadioButton from "primevue/radiobutton";

const props = defineProps({
    modelValue: [String, Number], // Permite usar v-model
    label: {
        type: String,
        default: null,
    },
    /*
    Formato de las opciones por defecto:
    options: [
        {
            id: 1,
            name: "Option 1",
        },
        ...
    ],
    */
    options: {
        type: Array,
        required: true,
    },
    optionValue: {
        type: String,
        default: "id",
    },
    optionLabel: {
        type: String,
        default: "name",
    },
    radioClass: {
        type: String,
        default: "",
    },
    error: {
        type: String,
        default: null, // Para manejar errores automáticamente
    },
    idPrefix: {
        type: String,
        default: "", // Prefijo opcional para generar un ID único
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

        <div class="flex flex-wrap gap-4 mt-2">
            <div
                v-for="(option, index) in options"
                :key="`${props.idPrefix}-${option[optionValue]}`"
                class="flex items-center gap-2"
            >
                <RadioButton
                    :inputId="`${props.idPrefix}-${props.name}-${option[optionValue]}`"
                    :name="props.name"
                    :value="option[optionValue]"
                    v-model="inputValue"
                    :class="radioClass"
                    v-bind="attrs"
                />
                <label
                    :for="`${props.idPrefix}-${props.name}-${option[optionValue]}`"
                >
                    {{ option[optionLabel] }}
                </label>
            </div>
        </div>

        <p v-if="error" class="text-red-500 text-sm mt-1">
            {{ error }}
        </p>
    </div>
</template>
