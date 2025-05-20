<script setup>
import { computed, useAttrs } from "vue";
import BaseField from "./BaseField.vue";

const props = defineProps({
    modelValue: Number,
    label: {
        type: String,
        required: true,
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
        default: null,
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
        <InputNumber
            v-model="inputValue"
            v-bind="attrs"
            :placeholder="placeholder"
            :size="size"
            class="w-full"
        />
    </BaseField>
</template>
