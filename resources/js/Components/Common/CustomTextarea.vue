<script setup>
import { ref, watch, computed, useAttrs } from "vue";

const props = defineProps({
    modelValue: {
        type: String,
        required: true,
    },
    label: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: "",
    },
    maxlength: {
        type: Number,
        default: 350,
    },
    placeholder: {
        type: String,
        default: "",
    },
    error: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(["update:modelValue"]);

const inputValue = ref(props.modelValue);

watch(
    () => props.modelValue,
    (val) => {
        inputValue.value = val;
    }
);

watch(inputValue, (val) => {
    if (val.length <= props.maxlength) {
        emit("update:modelValue", val);
    } else {
        inputValue.value = val.slice(0, props?.maxlength);
    }
});

const currentLength = computed(() => inputValue.value?.length ?? 0);
const attrs = useAttrs();
</script>

<template>
    <div class="flex flex-col gap-1 w-full relative">
        <label class="text-sm font-semibold text-gray-700">
            {{ label }}
            <span
                v-if="description"
                class="text-xs font-normal text-gray-500 ml-1"
            >
                {{ description }}
            </span>
        </label>

        <div class="relative">
            <textarea
                v-model="inputValue"
                :placeholder="placeholder"
                :maxlength="maxlength"
                rows="7"
                class="w-full border border-gray-300 rounded p-3 pr-16 text-sm text-gray-800 focus:outline-none focus:ring-1 focus:ring-green-500 resize-none"
                v-bind="attrs"
            />
            <!-- Contador de caracteres -->
            <span class="absolute bottom-2 right-5 text-xs text-gray-500">
                {{ currentLength }}/{{ maxlength }}
            </span>
        </div>

        <p v-if="error" class="text-red-500 text-sm mt-1">{{ error }}</p>
    </div>
</template>

<!-- <script setup>
import { computed, useAttrs } from "vue";

const props = defineProps({
    modelValue: {
        type: String,
        required: true,
    },
    label: {
        type: String,
        required: true,
    },
    placeholder: {
        type: String,
        default: "",
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
    <div :class="{ 'has-error': !!error }" class="flex flex-col gap-2">
        <label v-if="label" class="font-semibold text-base md:text-lg">
            {{ label }}
        </label>

        <Textarea
            v-model="inputValue"
            v-bind="attrs"
            :placeholder="placeholder"
            class="w-full"
        />

        <p v-if="error" class="text-red-500 text-sm mt-1">
            {{ error }}
        </p>
    </div>
</template> -->
