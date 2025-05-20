<script setup>
import { computed, useAttrs } from "vue";
import BaseField from "./BaseField.vue";

const props = defineProps({
    modelValue: {
        type: [String, Number],
        required: true,
    },
    label: {
        type: String,
        required: true,
    },
    inputClass: {
        type: [String, Array, Object],
        default: "",
    },
    typeInput: {
        type: String,
        default: "text",
    },
    placeholder: {
        type: String,
        default: "",
    },
    size: {
        type: String,
        default: "small",
    },
    error: {
        type: String,
        default: null, // Mensaje de error opcional
    },
    required: {
        type: Boolean,
        default: false,
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
    <BaseField :label="label" :required="required" :error="error">
        <InputText
            v-model="inputValue"
            v-bind="attrs"
            :type="typeInput"
            :placeholder="placeholder"
            :size="size"
            :class="['w-full', inputClass]"
        />
    </BaseField>
</template>
