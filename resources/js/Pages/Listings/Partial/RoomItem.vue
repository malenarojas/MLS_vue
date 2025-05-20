<script setup>
import { computed, defineProps } from "vue";

const props = defineProps({
    modelValue: {
        type: Array,
        required: true, // Recibe rooms desde el formulario
    },
    index: {
        type: Number,
        required: true,
    },
    roomTypes: {
        type: Array,
        required: true,
    },
});

const emit = defineEmits(["remove"]);

// Computed para agregar la opción de "No seleccionado"
const computedRoomTypes = computed(() => [
    { id: null, name: "No seleccionado" },
    ...props.roomTypes,
]);
</script>

<template>
    <div class="flex flex-wrap gap-4 items-center mb-2">
        <!-- Select con opción "No seleccionado" -->
        <div class="pl-1 bg-red-500 rounded-md w-48">
            <Select
                v-model="modelValue[index].room_type_id"
                :options="computedRoomTypes"
                optionLabel="name"
                optionValue="id"
                placeholder="No seleccionado"
                inputId="room-type"
                class="w-full"
            />
        </div>

        <InputText
            v-model="modelValue[index].description"
            inputId="description"
            placeholder="Descripción"
            class="w-48"
        />

        <!-- Tamaño Habitación -->
        <InputNumber
            v-model="modelValue[index].size"
            inputId="size"
            placeholder="Tamaño"
        />

        <!-- Dimensiones -->
        <InputNumber
            v-model="modelValue[index].dimension_x"
            inputId="dimension_x"
            placeholder="Ancho"
        />
        <span class="text-gray-500">x</span>
        <InputNumber
            v-model="modelValue[index].dimension_y"
            inputId="dimension_y"
            placeholder="Alto"
        />

        <!-- Icono Eliminar -->
        <button
            type="button"
            class="text-red-500 hover:text-red-700 transition ml-5"
            @click="emit('remove', index)"
        >
            <i class="pi pi-trash" style="font-size: 1.4rem"></i>
        </button>
    </div>
</template>
