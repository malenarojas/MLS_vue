<script setup>
defineProps({
    owner: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div
        class="flex items-start border border-gray-300 rounded-lg p-4 shadow-sm bg-white"
    >
        <div
            class="w-16 h-16 flex-shrink-0 border border-gray-200 rounded-md overflow-hidden"
        >
            <img
                src="/img/profile-image.png"
                alt="Imagen del propietario"
                class="w-full h-full object-cover"
            />
        </div>
        <!-- Información del usuario -->
        <div class="ml-4 flex-1">
            <h3 class="text-lg font-extralight text-gray-800">
                {{ owner.full_name }}
            </h3>
            <!-- Rating -->
            <div class="flex items-center my-2">
                <div class="flex">
                    <span
                        v-for="star in 5"
                        :key="star"
                        :class="
                            star <= owner.rating
                                ? 'text-yellow-500'
                                : 'text-gray-300'
                        "
                        class="pi pi-star text-xl"
                    ></span>
                </div>
            </div>
            <!-- Contacto -->
            <div class="flex items-center gap-3 text-gray-600 text-sm mt-2">
                <!-- Enlace para llamar al número de teléfono -->
                <a
                    v-if="owner.mobile"
                    :href="'tel:' + owner.mobile"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="p-2 bg-blue-100 rounded-full hover:bg-blue-200"
                >
                    <i class="pi pi-phone text-blue-600"></i>
                </a>

                <!-- Enlace para WhatsApp -->
                <a
                    v-if="owner.whatsapp"
                    :href="'https://wa.me/' + owner.whatsapp"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="p-2 bg-green-100 rounded-full hover:bg-green-200"
                >
                    <i class="pi pi-whatsapp text-green-600"></i>
                </a>

                <!-- Enlace para enviar un correo electrónico -->
                <a
                    v-if="owner.email"
                    :href="'mailto:' + owner.email"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="p-2 bg-red-100 rounded-full hover:bg-red-200"
                >
                    <i class="pi pi-envelope text-red-600"></i>
                </a>
            </div>

            <!-- <div class="flex items-center gap-3 text-gray-600 text-[0.2rem] mt-2">
                        <a
                        v-if="owner.mobile" class="p-2 bg-blue-100 rounded-full hover:bg-blue-200"
                        >
                              <i class="pi pi-phone text-blue-600"></i>
                        </a>
                        <a 
                        v-if="owner.whatsapp" class="p-2 bg-green-100 rounded-full hover:bg-green-200"
                        >
                              <i class="pi pi-whatsapp text-green-600"></i>
                        </a>
                        <a 
                        v-if="owner.email" class="p-2 bg-red-100 rounded-full hover:bg-red-200"
                        >
                              <i class="pi pi-envelope text-red-600"></i>
                        </a>
                  </div> -->
            <!-- Número de teléfono -->
            <div class="mt-2 text-blue-600">
                <a
                    :href="'tel:' + owner.phone"
                    class="text-sm hover:underline"
                    >{{ owner.mobile }}</a
                >
            </div>
        </div>
    </div>
</template>
