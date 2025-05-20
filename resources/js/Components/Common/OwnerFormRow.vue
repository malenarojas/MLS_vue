<script setup>
import { defineProps, defineEmits, ref, computed } from "vue";
import TextInput from "./TextInput.vue";

const props = defineProps({
    owner: Object,
    isNew: Boolean,
});

const emit = defineEmits(["save", "remove", "cancel"]);

const form = ref({
    name: props.owner?.name || "",
    last_name: props.owner?.last_name || "",
    email: props.owner?.email || "",
    mobile: props.owner?.mobile || "",
});

const placeholderText = {
    name: "No especificado",
    last_name: "No especificado",
    email: "Correo no registrado",
    mobile: "Teléfono no registrado",
};

const isDisabled = computed(() => !props.isNew);

// Guardar el propietario
const saveOwner = () => {
    emit("save", { ...form.value });
};
</script>

<template>
    <div
        class="grid grid-cols-6 gap-3 items-center p-3 bg-gray-50 border rounded-lg shadow-sm"
    >
        <div class="flex flex-col">
            <label class="text-xs text-gray-500">Nombre</label>
            <TextInput
                v-model="form.name"
                :placeholder="placeholderText.name"
                class="w-full"
                :disabled="isDisabled"
            />
        </div>

        <div class="flex flex-col">
            <label class="text-xs text-gray-500">Apellido</label>
            <TextInput
                v-model="form.last_name"
                :placeholder="placeholderText.last_name"
                class="w-full"
                :disabled="isDisabled"
            />
        </div>

        <div class="flex flex-col">
            <label class="text-xs text-gray-500">Correo</label>
            <TextInput
                v-model="form.email"
                :placeholder="placeholderText.email"
                class="w-full"
                :disabled="isDisabled"
            />
        </div>

        <div class="flex flex-col">
            <label class="text-xs text-gray-500"># Celular</label>
            <TextInput
                v-model="form.mobile"
                :placeholder="placeholderText.mobile"
                class="w-full"
                :disabled="isDisabled"
            />
        </div>

        <Button
            v-if="!isNew"
            icon="pi pi-trash"
            severity="danger"
            class="p-button-rounded p-button-text"
            style="font-size: 1.5rem; padding: 0.7rem"
            @click="emit('remove', owner.id)"
        />

        <div v-if="isNew" class="flex gap-2 mt-2">
            <Button
                icon="pi pi-check"
                class="p-button-success p-button-rounded"
                size="small"
                @click="saveOwner"
            />
            <Button
                icon="pi pi-times"
                class="p-button-danger p-button-rounded"
                size="small"
                @click="emit('cancel')"
            />
        </div>
    </div>
</template>
