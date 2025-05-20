<script setup>
import { computed, inject, ref, watch } from "vue";
import { usePage } from "@inertiajs/vue3";
import { useToast } from "primevue";
import TextInput from "@/Components/Common/TextInput.vue";

const toast = useToast();
const form = inject("form", { errors: {} });
const page = usePage();

const activeIndex = ref(0);
const displayCustom = ref(false);
const fileInput = ref(null);
const asociateRoomPopover = ref(null);
const selectedImage = ref(null);
const selectedRoom = ref(null);
const draggingImage = ref(null);
const rooms = computed(
    () => page.props?.listing?.listing_information?.rooms ?? []
);

const virtualViewers = [
    { label: "Ninguno", value: null, placeholder: "" },
    {
        label: "immoviewer",
        value: "immoviewer",
        logo: "/img/immoviewer.png",
        placeholder: "https://app.immoviewer.com/XXXXXXX",
    },
    {
        label: "Matterport",
        value: "matterport",
        logo: "/img/matterport.png",
        placeholder: "https://my.matterport.com/show/?m=XXXXXXX",
    },
    {
        label: "iGUIDE",
        value: "iguide",
        logo: "/img/iguide.png",
        placeholder: "https://youriguide.com/XXXXXXX",
    },
    {
        label: "Other Virtual Viewer",
        value: "other",
        placeholder: "https://virtualviewer.com/XXXXXXX",
    },
];

const responsiveOptions = ref([
    { breakpoint: "1024px", numVisible: 5 },
    { breakpoint: "768px", numVisible: 3 },
    { breakpoint: "560px", numVisible: 1 },
]);

const galleryImages = computed(() =>
    [...form.multimedias]
        .sort((a, b) => b.is_default - a.is_default)
        .map((image) => ({
            ...image,
            id: image.id || null,
            src: image.preview || image.large_url,
            thumbnail: image.preview || image.large_url,
            alt: image.description || "Imagen multimedia",
        }))
);

const handleFileUpload = (event) => {
    const files = event.target.files;

    if (files.length > 20) {
        toast.add({
            severity: "error",
            summary: "Demasiados archivos",
            detail: "Puedes subir hasta 20 imágenes por vez. Intenta dividirlas en grupos más pequeños.",
            life: 5000,
        });
        return;
    }

    const allowedTypes = ["image/jpeg", "image/png", "image/webp", "image/gif"];
    const maxSize = 5 * 1024 * 1024; // 5MB

    const invalidFiles = Array.from(files).filter(
        (file) => !allowedTypes.includes(file.type) || file.size > maxSize
    );

    if (invalidFiles.length > 0) {
        toast.add({
            severity: "error",
            summary: "Archivo inválido",
            detail: "Solo se permiten imágenes JPG, PNG, WEBP, GIF y máximo 5MB por archivo.",
            life: 5000,
        });
        return;
    }

    let id = Date.now();
    const newImages = Array.from(files).map((file) => {
        const preview = URL.createObjectURL(file);
        id++;
        return {
            id,
            file,
            preview,
            is_new: true,
            is_default: 0,
            room_id: null,
            multimedia_type_id: 3, // Imagen normal
        };
    });

    form.multimedias = [...form.multimedias, ...newImages];
};
// const handleFileUpload = (event) => {
//     const files = event.target.files;
//     let id = Date.now();
//     const newImages = Array.from(files).map((file) => {
//         const preview = URL.createObjectURL(file);
//         id++;
//         return {
//             id,
//             file,
//             preview,
//             is_new: true,
//             is_default: 0,
//             room_id: null,
//             multimedia_type_id: 3, // Imagen normal
//         };
//     });

//     form.multimedias = [...form.multimedias, ...newImages];
// };

const removeImage = (index) => {
    const removed = form.multimedias.splice(index, 1)[0];
    if (removed.preview) {
        URL.revokeObjectURL(removed.preview);
    }
};

const selectImage = () => {
    fileInput.value.click();
};

const imageClick = (index) => {
    activeIndex.value = index;
    displayCustom.value = true;
};

const defineDefault = (imageId) => {
    form.multimedias.forEach((img) => {
        img.is_default = img.id === imageId ? 1 : 0;
    });
};

const asociateRoom = (event, image) => {
    selectedImage.value = image;
    asociateRoomPopover.value.toggle(event);
};

const selectRoom = (room) => {
    selectedImage.value.room_id = room?.id ?? null;
    console.log(selectedImage.value, form.multimedias);
    form.multimedias = form.multimedias.map((image) =>
        image.id === selectedImage.value?.id
            ? { ...image, room_id: room?.id ?? null }
            : image
    );
    asociateRoomPopover.value.hide();
};

const dragStart = (event, index) => {
    draggingImage.value = index;
};
const dragOver = (event) => {
    event.preventDefault();
};
const dropImage = (event, index) => {
    event.preventDefault();
    const draggedItem = form.multimedias.splice(draggingImage.value, 1)[0];
    form.multimedias.splice(index, 0, draggedItem);
};

const virtualViewerPlaceholder = computed(() => {
    const selected = virtualViewers.find(
        (viewer) => viewer.value === form.listing_information.virtual_viewer
    );
    return selected?.placeholder || "";
});

const isVirtualLinkDisabled = computed(
    () => !form.listing_information.virtual_viewer
);

watch(
    () => form.listing_information.virtual_viewer,
    (newValue) => {
        if (!newValue) {
            form.listing_information.virtual_link = null;
        }
    }
);
</script>

<template>
    <!-- <pre>{{ form.multimedias }}</pre> -->
    <div>
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-xl font-semibold mb-2">
                    {{ form.multimedias.length }} Fotografías
                </h2>
                <p>
                    Arrastre y suelte para cambiar el orden de clasificación de
                    las imágenes a continuación
                </p>
            </div>
            <div>
                <Button label="Añadir imágenes" @click="selectImage" />
                <input
                    type="file"
                    multiple
                    ref="fileInput"
                    accept="image/jpeg,image/png,image/webp,image/gif"
                    class="hidden"
                    @change="handleFileUpload"
                />
            </div>
        </div>
    </div>

    <div class="card flex flex-col items-center justify-center">
        <Galleria
            v-model:activeIndex="activeIndex"
            v-model:visible="displayCustom"
            :value="galleryImages"
            :responsiveOptions="responsiveOptions"
            :numVisible="5"
            containerStyle="max-width: 100%; margin: 0 auto"
            :circular="true"
            :fullScreen="true"
            :showItemNavigators="true"
            :showThumbnails="false"
        >
            <template #item="slotProps">
                <img
                    :src="slotProps.item.src"
                    :alt="slotProps.item.alt"
                    class="w-full h-full object-cover"
                />
            </template>
        </Galleria>

        <div
            v-if="galleryImages.length"
            class="grid gap-5 mt-4 w-full sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5"
        >
            <div
                v-for="(image, index) in galleryImages"
                :key="index"
                class="relative rounded-lg overflow-hidden shadow-md transform transition-transform hover:scale-105 group"
                draggable="true"
                @dragstart="dragStart($event, index)"
                @dragover="dragOver($event)"
                @drop="dropImage($event, index)"
            >
                <img
                    :src="image.thumbnail"
                    :alt="image.alt"
                    class="w-full h-full object-cover cursor-pointer"
                    @click="imageClick(index)"
                />

                <div
                    class="absolute bottom-0 left-0 w-full flex items-center justify-between bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 p-1"
                >
                    <div class="flex gap-1">
                        <Button
                            v-if="rooms.length > 0"
                            size="small"
                            type="button"
                            @click.stop="(event) => asociateRoom(event, image)"
                        >
                            <i class="pi pi-link"></i>
                        </Button>

                        <button
                            size="small"
                            type="button"
                            @click.stop="defineDefault(image.id)"
                            :class="`${
                                image.is_default
                                    ? 'bg-yellow-500 hover:bg-yellow-600 text-white'
                                    : 'bg-gray-200 hover:bg-gray-300 text-gray-800'
                            } rounded-md px-2`"
                        >
                            <span v-if="image.is_default">
                                <i class="pi pi-star-fill"></i>
                            </span>
                            <span v-else>
                                <i class="pi pi-star"></i>
                            </span>
                        </button>
                    </div>
                    <div>
                        <Button
                            size="small"
                            severity="danger"
                            type="button"
                            @click.stop="removeImage(index)"
                        >
                            <i class="pi pi-trash"></i>
                        </Button>
                    </div>
                </div>
            </div>
        </div>

        <!-- <pre>{{ selectedImage }}</pre>
        <pre>{{ form.rooms }}</pre> -->

        <Popover ref="asociateRoomPopover" v-if="rooms.length > 0">
            <div class="">
                <ul class="list-none p-0 m-0">
                    <li
                        v-for="room in rooms"
                        :key="room.id"
                        :class="`text-center flex items-center gap-2 hover:bg-gray-200 cursor-pointer rounded p-1 mb-2 ${
                            selectedImage?.room_id === room.id
                                ? 'bg-gray-400 text-white hover:bg-gray-500'
                                : ''
                        }`"
                        @click="selectRoom(room)"
                    >
                        <span class="font-semibold">
                            {{
                                `${room.description ?? ""} (${
                                    room?.room_type?.name ?? ""
                                })`
                            }}
                        </span>
                    </li>
                    <li
                        v-if="selectedImage?.room_id !== null"
                        class="text-center flex items-center gap-2 hover:bg-gray-200 cursor-pointer rounded p-1 mb-2"
                        @click="selectRoom(null)"
                    >
                        <span class="font-semibold">Ninguno</span>
                    </li>
                </ul>
            </div>
        </Popover>
    </div>

    <div class="flex flex-col gap-5 mt-10">
        <h2 class="text-xl font-semibold border-b-2 border-blue-500">Videos</h2>
        <div class="flex items-center gap-3">
            <i
                class="pi pi-youtube"
                :style="{ fontSize: '3rem', color: '#FF0000' }"
            ></i>
            <div class="w-full sm:w-1/2">
                <TextInput
                    v-model="form.listing_information.youtube_link"
                    label="You Tube"
                    :error="form.errors?.listing_information?.youtube_link"
                    placeholder="Ingresa un link de Youtobe, relacionado al listing"
                />
            </div>
        </div>

        <div>
            <p
                class="text-lg text-gray-600 mt-3 font-medium border-b-2 border-blue-500 pb-1"
            >
                (Seleccione un recorrido virtual e ingrese el enlace)
            </p>

            <div class="flex gap-4 mt-2 flex-wrap">
                <label
                    v-for="(viewer, index) in virtualViewers"
                    :key="index"
                    class="flex items-center gap-1 text-sm"
                >
                    <input
                        type="radio"
                        v-model="form.listing_information.virtual_viewer"
                        :value="viewer.value"
                    />
                    <img
                        v-if="viewer.logo"
                        :src="viewer.logo"
                        alt="viewer"
                        class="h-8"
                    />
                    <span v-else>{{ viewer.label }}</span>
                </label>
            </div>

            <div class="flex items-center gap-3 mt-3">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="3em"
                    height="3em"
                    viewBox="0 0 48 48"
                >
                    <defs>
                        <mask id="ipSVirtualRealityGlasses0">
                            <g fill="none">
                                <path
                                    fill="#fff"
                                    stroke="#fff"
                                    stroke-linejoin="round"
                                    stroke-width="4"
                                    d="M5 16h38a1 1 0 0 1 1 1v22a1 1 0 0 1-1 1H30l-5.992-5.999L18 40H5a1 1 0 0 1-1-1V17a1 1 0 0 1 1-1Z"
                                />
                                <path
                                    fill="#000"
                                    stroke="#000"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="4"
                                    d="M14 32a4 4 0 1 0 0-8a4 4 0 0 0 0 8m20 0a4 4 0 1 0 0-8a4 4 0 0 0 0 8"
                                />
                                <path
                                    fill="#fff"
                                    fill-rule="evenodd"
                                    d="M6 10h36z"
                                    clip-rule="evenodd"
                                />
                                <path
                                    stroke="#fff"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="4"
                                    d="M6 10h36"
                                />
                            </g>
                        </mask>
                    </defs>
                    <path
                        fill="#bcbcbc"
                        d="M0 0h48v48H0z"
                        mask="url(#ipSVirtualRealityGlasses0)"
                    />
                </svg>
                <div class="w-full sm:w-1/2">
                    <TextInput
                        v-model="form.listing_information.virtual_link"
                        label="Virtual Tour Link"
                        :error="form.errors?.listing_information?.virtual_link"
                        :placeholder="virtualViewerPlaceholder"
                        v-bind="{ disabled: isVirtualLinkDisabled }"
                    />
                </div>
            </div>
        </div>

        <h2 class="text-xl font-semibold border-b-2 border-blue-500 pb-1">
            Enlaces a otros sitios
        </h2>
        <div class="flex items-center gap-3">
            <i
                class="pi pi-external-link"
                :style="{ fontSize: '2.5rem', color: '#848281' }"
            ></i>
            <div class="w-full sm:w-1/2">
                <TextInput
                    v-model="form.listing_information.external_link"
                    label="External link"
                    :error="form.errors?.listing_information?.external_link"
                    placeholder="Agrega un enlace a otro sitio web"
                />
            </div>
        </div>
    </div>
</template>
