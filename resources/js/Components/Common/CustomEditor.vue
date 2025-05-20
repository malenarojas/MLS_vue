<script setup>
import { ref, computed } from "vue";
import Editor from "primevue/editor";

const props = defineProps({
    modelValue: {
        type: String,
        required: true,
    },
    name: {
        type: String,
        required: true,
    },
    label: {
        type: String,
        required: true,
    },
    editorStyle: {
        type: Object,
        default: () => ({ height: "300px" }),
    },
    error: {
        type: String,
        default: null,
    },
    maxlength: {
        type: Number,
        default: null,
    },
});

const emit = defineEmits(["update:modelValue"]);

const editorValue = ref(props.modelValue);
const currentLength = computed(() => plainText(editorValue.value).length);

function plainText(html = "") {
    const div = document.createElement("div");
    div.innerHTML = html;
    return div.textContent || div.innerText || "";
}

// Manejar cambio desde editor
const handleTextChange = (event) => {
    const newHtml = event.htmlValue;
    const textOnly = plainText(newHtml);

    if (props.maxlength && textOnly.length > props.maxlength) {
        return;
    }

    editorValue.value = newHtml;
    emit("update:modelValue", newHtml);
};
</script>

<template>
    <div :class="{ 'has-error': !!error }" class="flex flex-col gap-2 relative">
        <label :for="name" class="font-semibold text-base md:text-lg">
            {{ label }}
        </label>

        <div class="relative pb-7">
            <Editor
                :modelValue="editorValue"
                @text-change="handleTextChange"
                :style="editorStyle"
                class="w-full"
            />
            <span
                v-if="maxlength"
                class="absolute bottom-0 right-4 text-xs text-gray-500 z-10 bg-white px-1"
            >
                {{ currentLength }}/{{ maxlength }}
            </span>
        </div>

        <p v-if="error" class="help-message text-red-500 text-sm">
            {{ error }}
        </p>
    </div>
</template>
