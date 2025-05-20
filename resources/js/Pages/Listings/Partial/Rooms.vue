<script setup>
import { inject, defineProps } from "vue";
import RoomItem from "./RoomItem.vue";

const form = inject("form");

const props = defineProps({
    roomTypes: {
        type: Array,
        required: true, // ✅ Se recibe como prop desde la página
    },
});

// Agregar habitación
const addRoom = () => {
    form.rooms.push({
        room_type_id: null,
        description: "",
        size: null,
        dimension_x: null,
        dimension_y: null,
    });
};

// Eliminar habitación
const removeRoom = (index) => {
    form.rooms.splice(index, 1);
};
</script>

<template>
    <section class="">
        <RoomItem
            v-for="(room, index) in form.rooms"
            :key="index"
            v-model="form.rooms"
            :index="index"
            :roomTypes="roomTypes"
            @remove="removeRoom"
        />

        <Button
            type="button"
            @click="addRoom"
            severity="success"
            size="small"
            class="w-1/4"
        >
            Agregar habitación
        </Button>
    </section>
</template>
