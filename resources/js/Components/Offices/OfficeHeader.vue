<template>
    <div class="flex justify-between items-start bg-white p-6 rounded-lg shadow-md mb-5">
      <!-- Sección Izquierda: Logo / Info / Social -->
      <div class="flex gap-6">
        <!-- Logo / Foto Oficina -->
        <div class="relative">
        <div class="border-4 border-blue-100 rounded overflow-hidden" style="width: 300px; height:  180px">
        <img
            :src="officeLogoUrl || imagePreview"
            alt="Logo de la Oficina"
            class="w-full h-full object-contain"
        />
        </div>


          <!-- Botón para abrir el editor de imagen -->
          <div
            class="absolute bottom-2 right-2 bg-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center cursor-pointer hover:bg-blue-600 transition-colors"
            @click="openImageEditor"
          >
            ✏️
          </div>
        </div>

        <!-- Datos principales de la oficina -->
        <div class="flex flex-col justify-between flex-grow">
          <h2 class="text-2xl font-bold text-blue-800 m-0">
            {{ props.office.name }}
          </h2>
          <p class="text-gray-700 text-sm mt-1">
            {{ formattedAddress }}
          </p>

          <!-- Íconos de redes sociales (similares a lo de agentes) -->
          <div class="flex gap-4 mt-2 text-2xl">
            <i class="fab fa-facebook text-[#1877F2]"></i>
            <i class="fab fa-whatsapp text-[#25D366]"></i>
            <i class="fab fa-instagram text-[#E4405F]"></i>
            <i class="fab fa-youtube text-[#FF0000]"></i>
            <i class="fab fa-linkedin text-[#0A66C2]"></i>
          </div>

          <!-- Botones de acción -->
          <div class="flex gap-4 mt-4">
            <button
              class="bg-gray-300 text-gray-700 px-3 py-1.5 text-sm rounded-lg hover:bg-gray-400 transition-colors"
              disabled
            >
              Ir al sitio de la oficina
            </button>
            <button
              class="border border-blue-500 text-blue-500 px-3 py-1.5 text-sm rounded-lg hover:bg-blue-500 hover:text-white transition-colors"
            >
              Ver en la Página Web
            </button>
            <button
              class="border border-blue-500 text-blue-500 px-3 py-1.5 text-sm rounded-lg hover:bg-blue-500 hover:text-white transition-colors"
            >
              View Office Listings
            </button>
          </div>
        </div>
      </div>

      <!-- Sección Derecha: Mini-mapa -->
      <div class="ml-6">
        <div
          ref="mapElement"
          class="w-48 h-48 border border-gray-300 rounded relative z-50"
        ></div>
      </div>

      <!-- Modal para editar la imagen (Cropper) -->
      <div v-if="showImageEditor" class="image-editor-modal">
        <div class="image-editor-container">
          <h3>Recortar Logo</h3>
          <input type="file" @change="loadImage" />
          <div v-if="imageLoaded">
            <img ref="imageToCrop" :src="imagePreview" alt="Imagen para recortar" />
          </div>
          <div class="editor-buttons">
            <button v-if="cropper" @click="cropImage">Recortar</button>
            <button @click="closeImageEditor">Cancelar</button>
          </div>
        </div>
      </div>
    </div>
  </template>

  <script setup>
  /*
    - Se mantienen las mismas props, composables y lógica de Cropper.
    - Simplemente se ajustan las clases Tailwind para igualar estilo al del agente.
  */
  import { ref, computed, watch, onMounted, nextTick } from 'vue';
  import { defineProps, defineEmits } from 'vue';
  import Cropper from 'cropperjs';
  import 'cropperjs/dist/cropper.css';

  import { useLeafletMapOffice } from '@/Composables/useLeafletMapOffice';

  // Props
  const props = defineProps({
    office: {
      type: Object,
      required: true
    }
  });
  watch(
  () => props.office,
  (newVal) => {
    console.log('🟢 props.office actualizado en el header:', newVal);
  },
  { immediate: true }
);

  // Emit para enviar imagen recortada al padre
  const emit = defineEmits(['update-office-logo']);

  // Refs para el editor de imagen
  const showImageEditor = ref(false);
  const imageLoaded = ref(false);
  const cropper = ref(null);
  const imagePreview = ref(null);
  const currentImageName = ref('');
  const imageToCrop = ref(null);

  // Mapa
  const mapElement = ref(null);
  const { initializeMap, addMarker } = useLeafletMapOffice();

  onMounted(() => {
    const lat = parseFloat(props.office.latitude) || -16.2902;
    const lng = parseFloat(props.office.longitude) || -63.5887;

    initializeMap(mapElement.value, {
      center: [lat, lng],
      zoom: 14
    });
    addMarker(lat, lng);
  });

  // Logo de la oficina
  const officeLogoUrl = computed(() => {
  return props.office?.image_url ?? null;
});


  // Dirección formateada
  const formattedAddress = computed(() => {
    const { address, number, zone, city, province } = props.office;
    return `${address || ''} #${number || ''}, ${zone || ''} ${city || ''} ${province || ''}`;
  });

  // ---------- Lógica Cropper -----------
  const openImageEditor = () => {
    showImageEditor.value = true;
  };

  const closeImageEditor = () => {
    showImageEditor.value = false;
    imageLoaded.value = false;
    if (cropper.value) {
      cropper.value.destroy();
      cropper.value = null;
    }
  };

  const loadImage = (event) => {
    const file = event.target.files[0];
    if (file) {
      currentImageName.value = file.name;
      const reader = new FileReader();
      reader.onload = (e) => {
        imagePreview.value = e.target.result;
        imageLoaded.value = true;
        nextTick(() => {
          if (imageToCrop.value) {
            // Inicializar cropper
            cropper.value = new Cropper(imageToCrop.value, {
                viewMode: 1,
                aspectRatio: NaN, // libre
                autoCrop: true,
                autoCropArea: 1, // que recorte toda la imagen al inicio
                movable: true,
                zoomable: true,
                scalable: true,
                cropBoxResizable: true,
                cropBoxMovable: true,
                minCropBoxWidth: 50,
                minCropBoxHeight: 50,
            });
          }
        });
      };
      reader.readAsDataURL(file);
    }
  };

  const cropImage = () => {
    if (cropper.value) {
      const croppedCanvas = cropper.value.getCroppedCanvas({
        width: 595,
        height: 250,
        imageSmoothingEnabled: true,
        imageSmoothingQuality: 'high',
      });
      const croppedBase64 = croppedCanvas.toDataURL('image/png');
      console.log('🟢 Emitiendo imagen al padre:', {
      image_name: 'cropped_' + currentImageName.value,
      image_data: croppedBase64,
    });

      // Emitir al padre
      emit('update-office-logo', {
        image_name: 'cropped_' + currentImageName.value,
        image_data: croppedBase64
      });

      imagePreview.value = croppedBase64;
      closeImageEditor();
    }
  };
  </script>

  <style scoped>
  .image-editor-modal {
    display: flex;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.75);
    z-index: 1050;
    align-items: center;
    justify-content: center;
  }

  .image-editor-container {
    background: white;
    padding: 20px;
    border-radius: 8px;
    width: 80%;
    max-width: 500px;
    text-align: center;
  }
  .image-editor-container img {
  max-width: 595px;
  max-height: 250px;
  width: 100%;
  height: auto;
  object-fit: contain;
  margin: 0 auto;
}


  .image-editor-container h3 {
    margin-bottom: 20px;
  }

  .editor-buttons {
    display: flex;
    justify-content: space-evenly;
    margin-top: 20px;
  }

  .editor-buttons button {
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    border: none;
    cursor: pointer;
    border-radius: 5px;
    transition: background-color 0.3s;
  }

  .editor-buttons button:hover {
    background-color: #0056b3;
  }

  input[type="file"] {
    margin: 10px 0;
  }

  .image-editor-container img {
    max-width: 100%;
    display: block;
  }
  </style>
