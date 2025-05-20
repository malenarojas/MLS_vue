<template>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start mt-6 border p-4 rounded-md bg-white">
      <!-- Columna: Vista previa de la imagen -->
      <div class="w-full max-w-[300px]justify-center items-center ">
        <Image
          :src="previewimg || defaultImageUrl"
          alt="Vista previa"
          width="100%"
          preview
          class="rounded-md border object-contain"
        />
      </div>

      <!-- Columna: FileUpload + instrucciones -->
      <div>
        <label class="font-semibold text-gray-700 block mb-2">Seleccione la imagen que desea cargar.</label>
        <FileUpload
          name="agentPhoto"
          accept="image/*"
          mode="basic"
          chooseLabel="Subir foto del agente"
          @select="pickFile"
          class="mb-3"
        />

        <p class="text-gray-600 text-sm leading-relaxed mb-1">
          Las imágenes deben ser .JPG o .JPEG y, para obtener mejores resultados,
          deben tener al menos 300 píxeles de ancho por 200 píxeles de alto (orientación horizontal).
        </p>
        <p class="text-gray-600 text-sm leading-relaxed">
          Si su imagen no tiene una relación de aspecto de 3:2 o las dimensiones son demasiado grandes,
          se cambiará el tamaño de la manera más uniforme posible.
        </p>
      </div>
    </div>
  </template>

  <script>
import defaultImage from '@/assets/img/default.gif';
import FileUpload from "primevue/fileupload";
import Image from "primevue/image";
import { ref } from "vue";

  export default {
	name: "PhotoTab",
	emits: ["update-image"], // Emitir el evento cuando se actualiza la imagen
	setup(props, { emit }) {
	  const MAX_FILE_SIZE = 2 * 1024 * 1024; // Límite de 2 MB
	  const previewimg = ref(null);
      const defaultImageUrl = defaultImage;

	  const pickFile = (event) => {
		const file = event.files[0];
		if (!file) {
		  console.error("No se seleccionó ningún archivo.");
		  return;
		}

		if (file.size > MAX_FILE_SIZE) {
		  alert("El archivo supera el límite de 2 MB.");
		  return;
		}

		const reader = new FileReader();
		reader.onload = (e) => {
		  // e.target.result es el Base64 de la imagen
		  previewimg.value = e.target.result; // Actualizar la vista previa
		  // Emitir un objeto con image_name y image_data
		  emit("update-image", {
			image_name: file.name,
			image_data: e.target.result
		  });
		};
		reader.readAsDataURL(file);
	  };

	  return {
		previewimg,
		pickFile,
        defaultImageUrl,
	  };
	},
  };
  </script>




 <style scoped>
 .preview {
   display: flex;
   align-items: center;
   justify-content: center;
   border: 1px solid #ccc;
   border-radius: 8px;
   padding: 10px;
   width: 128px;
   height: 128px;
   overflow: hidden;
 }
 </style>
