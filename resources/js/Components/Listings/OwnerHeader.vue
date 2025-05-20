<script setup>
defineProps({
    user: {
        type: Object,
        required: true,
    },
});

const getInitials = (name) => {
    if (!name) return "";
    return name
        .split(" ")
        .map((n) => n[0])
        .join("")
        .toUpperCase();
};
</script>

<template>
    <div class="flex items-center gap-4">
        <!-- Círculo con iniciales -->
        <div
            class="w-12 h-12 flex items-center justify-center rounded-full bg-purple-200 text-white font-bold text-lg"
        >
            {{ getInitials(user?.full_name) }}
        </div>

        <!-- Nombre y acciones -->
        <div>
            <p class="text-sm font-medium text-gray-800">
                {{ user?.full_name }}
            </p>
            <div class="flex gap-2 mt-1">
                <a
                    v-if="user.mobile"
                    :href="'tel:' + user.mobile"
                    class="w-8 h-8 flex items-center justify-center bg-blue-500 rounded-full hover:bg-blue-600"
                    title="Llamar"
                >
                    <i class="pi pi-mobile text-white text-sm"></i>
                </a>

                <a
                    v-if="user.phone"
                    :href="'tel:' + user.phone"
                    class="w-8 h-8 flex items-center justify-center bg-sky-300 rounded-full hover:bg-sky-400"
                    title="Teléfono fijo"
                >
                    <i class="pi pi-phone text-white text-sm"></i>
                </a>

                <a
                    v-if="user.email"
                    :href="'mailto:' + user.email"
                    class="w-8 h-8 flex items-center justify-center bg-red-600 rounded-full hover:bg-red-700"
                    title="Correo"
                >
                    <i class="pi pi-envelope text-white text-sm"></i>
                </a>
            </div>
        </div>
    </div>
</template>
