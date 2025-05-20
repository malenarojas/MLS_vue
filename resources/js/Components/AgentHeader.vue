<template>
    <div class="flex justify-between items-start bg-white p-6 rounded-lg shadow-md mb-5">
      <!-- Información del Agente -->
      <div class="flex items-center gap-6">
        <!-- Foto del Agente -->
        <div class="relative">
          <img
            :src="agentImageUrl || imagePreview"
            alt="Foto del Agente"
            class="w-32 h-32 rounded-full object-cover border-4 border-blue-100"
          />
          <div
            class="absolute bottom-2 right-2 bg-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center cursor-pointer hover:bg-blue-600 transition-colors"
            @click="openImageEditor"
          >
            ✏️
          </div>
        </div>

        <!-- Detalles del Agente -->
        <div class="flex flex-col justify-between flex-grow">
          <h2 class="text-2xl font-bold text-blue-800 m-0">
            {{ agentDataupdate.user.name_to_show }}
          </h2>
          <p class="text-gray-600">{{ agentDataupdate.region_name }}</p>

          <!-- Iconos de Redes Sociales -->
          <!-- Iconos de Redes Sociales más grandes -->
            <!-- Redes Sociales con FontAwesome -->
            <div class="flex gap-4 mt-2 text-2xl">
            <i class="fab fa-facebook text-[#1877F2]"></i>      <!-- Facebook azul -->
            <i class="fab fa-whatsapp text-[#25D366]"></i>     <!-- WhatsApp verde -->
            <i class="fab fa-instagram text-[#E4405F]"></i>    <!-- Instagram rosa -->
            <i class="fab fa-youtube text-[#FF0000]"></i>      <!-- YouTube rojo -->
            <i class="fab fa-linkedin text-[#0A66C2]"></i>     <!-- LinkedIn azul -->
            </div>
          <!-- Botones de Acción -->
            <div class="flex gap-4 mt-4">
            <button
                class="bg-transparent border border-blue-500 text-blue-500 px-3 py-1.5 text-sm rounded-lg hover:bg-blue-500 hover:text-white transition-colors"
            >
                Ver mi perfil
            </button>
            <button
                class="bg-transparent border border-blue-500 text-blue-500 px-3 py-1.5 text-sm rounded-lg hover:bg-blue-500 hover:text-white transition-colors"
            >
                Ver mis captaciones
            </button>
            <button
                class="bg-transparent border border-blue-500 text-blue-500 px-3 py-1.5 text-sm rounded-lg hover:bg-blue-500 hover:text-white transition-colors"
            >
                Ir a Mi sitio
            </button>
            </div>

        </div>
      </div>

      <!-- Métricas del Agente -->
      <div class="flex items-center gap-10 mt-6">
        <!-- Estadísticas -->
        <div class="text-sm">
            <div class="text-yellow-400 grid grid-cols-5 gap-1 w-full max-w-[260px] text-4xl -mt-2">
            <i class="pi pi-star-fill"></i>
            <i class="pi pi-star-fill"></i>
            <i class="pi pi-star-fill"></i>
            <i class="pi pi-star"></i>
            <i class="pi pi-star"></i>
            </div>


          <ul class="space-y-2 mt-2">
            <li class="text-gray-700 flex justify-between items-center">
              Base de datos de contactos
              <span class="bg-blue-100 text-blue-600 px-2 py-1 rounded-full ml-2">
                {{ agentDataupdate.contacts_count || 0 }}
              </span>
            </li>
            <li class="text-gray-700 flex justify-between items-center">
              Captaciones Activas
              <span class="bg-blue-100 text-blue-600 px-2 py-1 rounded-full ml-2">
                {{ agentDataupdate.listings_count || 0 }}
              </span>
            </li>
            <li class="text-gray-700 flex justify-between items-center">
              Próximos Open Houses
              <span class="bg-blue-100 text-blue-600 px-2 py-1 rounded-full ml-2">
                {{ agentDataupdate.open_houses_count || 0 }}
              </span>
            </li>
          </ul>
        </div>

        <!-- Calidad del Perfil -->
        <div class="flex flex-col items-center space-y-4 text-center">
          <div
            class="relative inline-flex items-center justify-center w-24 h-24 rounded-full border-8"
            :class="{
              'border-green-500': calculateQuality(data) >= 80,
              'border-yellow-300': calculateQuality(data) >= 50 && calculateQuality(data) < 80,
              'border-red-500': calculateQuality(data) < 50
            }"
          >
            <span class="text-2xl font-bold text-gray-700">
              {{ calculateQuality(data) }}%
            </span>
          </div>

          <button
            class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold py-2 px-6 rounded-lg shadow-lg transition transform hover:scale-105"
          >
            Mejorar calidad
          </button>
        </div>
      </div>
    </div>
      <!-- Modal para editar la imagen -->
    <!-- Modal para editar la imagen -->
    <div v-if="showImageEditor" class="image-editor-modal">
    <div class="image-editor-container">
      <h3>Recortar Imagen</h3>
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
  </template>
<script setup>
import '@/assets/css/Agents/Agentsheader.css';
import Cropper from "cropperjs";
import { computed, defineProps, nextTick, ref } from 'vue';
//import backUrl from '@/src/constants/url';
// Asegúrate de importar Cropper.js y su CSS
import 'cropperjs/src/css/cropper.css';


import { useRoute } from "vue-router";

import { defineEmits } from 'vue';
const route = useRoute();
const currentImageName = ref('');

// Función para emitir la imagen en base64 junto con su nombre
const emitImage = (name, base64) => {
  emit('update-image', {
    image_name: name,
    image_data: base64,
  });
};

// Emitir eventos para enviar datos al componente padre
const emit = defineEmits(['update-image']);
// Definir las props
const props = defineProps({
  agentDataupdate: {
    type: Object,
    required: true,
  },
});

const imagePreview = ref(null);
const imageLoaded = ref(false);
const cropper = ref(null);
const showImageEditor = ref(false);

// Computed property para obtener la URL completa de la imagen del agente
const agentImageUrl = computed(() => {
    console.log("imagennnnnnnnnn para el header" ,props.agentDataupdate.image_url);
  if (props.agentDataupdate?.image_url) {
    const relativeUrl = props.agentDataupdate.image_url;
    return relativeUrl;
  }
});

const openImageEditor = () => {
  showImageEditor.value = true;
	cropper.value = true;
};

const closeImageEditor = () => {
  showImageEditor.value = false;
  imageLoaded.value = false;
  if (cropper.value) {
    cropper.value.destroy();
    cropper.value = null;
  }
};

// Función para calcular la calidad del perfil
const calculateQuality = (agentDataupdate) => {
  const userFields = [
	props.agentDataupdate?.user?.first_name,
  props.agentDataupdate?.user?.middle_name,
  props.agentDataupdate?.user?.last_name,
  props.agentDataupdate?.user?.name_to_show,
  props.agentDataupdate?.user?.ci,
	props.agentDataupdate?.user?.gender,
	props.agentDataupdate?.user?.phone_number,
  props.agentDataupdate?.user?.email,
  props.agentDataupdate?.user?.url,
  props.agentDataupdate?.user?.birthdate,
  props.agentDataupdate?.user?.remax_start_date,
  props.agentDataupdate?.user?.username,
  props.agentDataupdate?.user?.user_type_id,
  props.agentDataupdate?.user?.remax_title_id,
  props.agentDataupdate?.user?.remax_title_to_show_id,
  props.agentDataupdate?.user?.team_status_id,
  props.agentDataupdate?.user?.customer_preference_id,
  ];

  const agentFields = [
  props.agentDataupdate?.date_joined,
  props.agentDataupdate?.date_termination,
  props.agentDataupdate?.agent_status_id,
  props.agentDataupdate?.studies,
  props.agentDataupdate?.additional_education,
  props.agentDataupdate?.image_name,
  props.agentDataupdate?.previous_occupation,
  props.agentDataupdate?.license_type,
  props.agentDataupdate?.license_number,
  props.agentDataupdate?.expiration_date_license,
  props.agentDataupdate?.marketing_slogan,
  props.agentDataupdate?.meta_tag_description,
  props.agentDataupdate?.bullet_point_one,
  props.agentDataupdate?.bullet_point_two,
  props.agentDataupdate?.bullet_point_three,
  ];


  const fieldsToCheck = [...userFields, ...agentFields];
  const completedFields = fieldsToCheck.filter(
    (field) => field && field !== ""
  ).length;

  const totalFields = fieldsToCheck.length;
  return Math.round((completedFields / totalFields) * 100);
};
	const imageToCrop= ref([null]);

	const loadImage = (event) => {
		const file = event.target.files[0];

		if (file) {
			currentImageName.value = file.name;
			const reader = new FileReader();
			reader.onload = (e) => {
				imagePreview.value = e.target.result;
				imageLoaded.value = true;
				nextTick(() => {
					//const imageElement = document.getElementById('imageToCrop');
					console.log(imageToCrop.value);
					if (imageToCrop) {
					/* if (cropper.value) {
							cropper.value.destroy();
						}*/
						cropper.value = new Cropper(imageToCrop.value, {
							aspectRatio: 1,
							viewMode: 1,
							ready: function () {
								console.log('Cropper is ready.'); // Asegúrate de que esto se imprime
							},
							crop(event) {
								console.log('Crop event:', event.detail); // Datos del evento crop
							}
						});
					}
				});
			};
			reader.readAsDataURL(file);
		}
	};

    const cropImage = () => {
  if (cropper.value) {
    // Obtener datos del recorte
    const cropData = cropper.value.getData(true);

    // Establecer un tamaño fijo cuadrado para evitar estiramiento (ideal para perfil)
    const size = Math.min(cropData.width, cropData.height);

    const croppedCanvas = cropper.value.getCroppedCanvas({
      width: size,
      height: size,
      imageSmoothingEnabled: true,
      imageSmoothingQuality: 'high',
    });
    console.log("Canvas size:", croppedCanvas.width, "x", croppedCanvas.height);


    const croppedBase64 = croppedCanvas.toDataURL('image/png');

    emitImage('cropped_' + currentImageName.value, croppedBase64);

    if (croppedBase64.startsWith("data:image/png;base64,")) {
      imagePreview.value = croppedBase64;
    } else {
      console.error("Error: La imagen Base64 no es válida.");
    }

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
  background-color: rgba(0, 0, 0, 0.75); /* Fondo más oscuro para mejor contraste */
  z-index: 1050; /* Asegúrate de que sea suficientemente alto */
  align-items: center;
  justify-content: center;
}

.image-editor-container {
  background: white;
  padding: 20px;
  border-radius: 8px;
  width: 80%; /* Ajuste de ancho para mejor visualización */
  max-width: 500px; /* Máximo ancho del contenedor */
  text-align: center;
}

.image-editor-container h3 {
  margin-bottom: 20px; /* Espacio bajo el título */
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

/* Estilos específicos para el Cropper */
.cropper-view-box,
.cropper-face {
  border-radius: 50%; /* Hace que la caja de recorte sea redonda, quitar si se desea rectangular */
}

.cropper-line,
.cropper-point {
  background-color: #39f;
  border-radius: 50%;
}

.cropper-point {
  width: 12px;
  height: 12px;
}

.cropper-point.point-se {
  right: -5px;
  bottom: -5px;
}
.image-editor-container img {
  max-width: 100%; /* Asegura que la imagen no exceda el ancho del contenedor */
  display: block; /* Elimina margen extra inferior */
}
</style>
