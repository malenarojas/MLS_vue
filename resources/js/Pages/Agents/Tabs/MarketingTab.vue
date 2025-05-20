<template>
	<div v-if="agentData" >
	  <!-- Sección Principal -->
	  <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-6">
		<!-- Columna Izquierda -->
		<div class="space-y-4">
		  <div class="field">
			<label for="marketing_slogan" class="font-semibold">Lema del agente</label>
			<InputText
			  v-model="agentData.marketing_slogan"
			  id="marketing_slogan"
			  placeholder="La mejor opción en bienes raíces"
			  class="w-full"
			/>
		  </div>
		  <div class="field">
			<label for="website_description" class="font-semibold">Website Description</label>
			<Editor
			  v-model="agentData.website_description"
			  id="website_description"
			  editorStyle="height: 370px"
			  placeholder="Haga clic aquí para escribir"
			/>
		  </div>
		</div>

		<!-- Columna Derecha -->
		<div class="space-y-4">
			<div class="field">
  <label for="url">URL</label>

  <div class="flex items-center gap-2">
    <!-- Input de URL con ancho reducido -->
    <input
      type="text"
      v-model="agentData.user.url"
      id="url"
      placeholder="https://johndoe.com"
      class="w-3/4"
      :disabled="!agentData.user.state_url"
      @input="formatURL"
    />

    <!-- Checkbox para habilitar/deshabilitar -->
    <div class="flex items-center gap-1">
      <input
        type="checkbox"
        id="state_url"
        v-model="agentData.user.state_url"
        true-value="1"
        false-value="0"
        class="form-checkbox"
      />
      <label for="state_url" class="text-sm text-gray-600">Habilitar URL</label>
    </div>
  </div>

  <!-- URL generada -->
  <p class="text-gray-500 mt-2">
    URL generada:
    <a :href="generatedURL" target="_blank" class="text-blue-600">
      {{ generatedURL }}
    </a>
  </p>
</div>

<!-- Campo adicional: URL alterno -->
<div class="field mt-4">
  <label for="url_alterno" class="font-semibold">URL Alterno</label>
  <input
    type="text"
    id="url_alterno"
    v-model="agentData.url_alterno"
    placeholder="Ej: https://otraweb.com/perfil"
    class="w-full"
  />
</div>

		  <h2 class="font-bold text-xl mb-4">Redes Sociales</h2>
		  <div v-for="(network, index) in agentData.social_networks" :key="index" class="flex items-center gap-4">
			<!-- Ícono de red social -->
			<i :class="getSocialIcon(network.name)" class="social-icon"></i>

			<!-- Input más pequeño -->
			<InputText
				v-model="network.url"
				:placeholder="`URL de ${capitalize(network.name)}`"
				class="small-input w-full"
			/>

			<!-- Checkbox nativo -->
			<input
				type="checkbox"
				:id="`checkbox-${index}`"
				v-model="network.state"
				:true-value="1"
				:false-value="0"
				class="w-5 h-5"
			/>
			</div>
		</div>
	  </div>

	  <!-- Bullet Points -->
	  <div class="w-full mt-8">
		<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
		  <div class="field">
			<label for="bullet_point_one" class="font-semibold">Bullet Point 1</label>
			<InputText
			  v-model="agentData.bullet_point_one"
			  id="bullet_point_one"
			  placeholder="Amplio conocimiento del mercado"
			  class="w-full "
			/>
		  </div>
		  <div class="field">
			<label for="bullet_point_two" class="font-semibold">Bullet Point 2</label>
			<InputText
			  v-model="agentData.bullet_point_two"
			  id="bullet_point_two"
			  placeholder="Certificaciones en ventas"
			  class="w-full"
			/>
		  </div>
		  <div class="field">
			<label for="bullet_point_three" class="font-semibold">Bullet Point 3</label>
			<InputText
			  v-model="agentData.bullet_point_three"
			  id="bullet_point_three"
			  placeholder="Ingrese otro punto clave"
			  class="w-full"
			/>
		  </div>
		</div>
	  </div>
	</div>
  </template>

  <script setup lang="ts">
  import '@fortawesome/fontawesome-free/css/all.css';
import Editor from "primevue/editor";
import InputText from "primevue/inputtext";
import { computed } from 'vue';



  // Props
  const props = defineProps({
	agentData: {
	  type: Object,
	  required: true,
	},
  });
	// Computed property para generar la URL
	const generatedURL = computed(() => {
		const baseUrl = "https://www.remax.bo/";
		return props.agentData.user.url ? `${baseUrl}${props.agentData.user.url}` : baseUrl;
	});

	// Formatear la URL (opcional)
	const formatURL = () => {
		props.agentData.user.url = props.agentData.user.url.replace(/\s+/g, "-").toLowerCase();
	};

  const getSocialIcon = (name: string) => {
  const icons: Record<string, string> = {
    facebook: "fab fa-facebook",
    twitter: "fab fa-twitter",
    youtube: "fab fa-youtube",
    linkedin: "fab fa-linkedin",
    instagram: "fab fa-instagram",
    whatsapp: "fab fa-whatsapp",
  };
  return icons[name] || "pi pi-globe";
};

  // Capitalizar texto
  const capitalize = (str: string) => str.charAt(0).toUpperCase() + str.slice(1);
  </script>

  <style scoped>
  /* Reutilizando las clases CSS de PersonalTab */
  .container {
	max-width: 100%;
	margin: auto;
	background-color: #fff;
	padding: 20px;
	border-radius: 10px;
	box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  }

  .section-title {
	font-size: 1.1rem;
	font-weight: bold;
	color: #080808;
	margin-bottom: 15px;
	border-bottom: 2px solid #007bff;
	padding-bottom: 5px;
  }

  .form-group {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
	gap: 15px;
  }

  .field {
	display: flex;
	flex-direction: column;
  }

  .field label {
	font-size: 0.9rem;
	font-weight: 600;
	margin-bottom: 5px;
	color: #333;
  }
  /* Inputs más pequeños */
.small-input {
  height: 70 px; /* Ajusta la altura */
  font-size: 1.0rem; /* Tamaño de fuente más pequeño */
  padding: 4px; /* Relleno interno reducido */
}

/* Ajuste del contenedor para mejor alineación */
.flex.items-center {
  align-items: center;
}

/* Inputs más pequeños */
.small-input {
  height: 36px; /* Ajuste de altura */
  font-size: 0.9rem; /* Fuente ajustada */
  padding: 6px; /* Espaciado reducido */
}
.social-icon {
  font-size: 1.8rem; /* Ajusta el tamaño del ícono */
  width: 60px;
  text-align: center;
}

/* Colores oficiales de las marcas */
.fa-facebook {
  color: #1877f2; /* Azul de Facebook */
}

.fa-twitter {
  color: #1da1f2; /* Azul de Twitter */
}

.fa-youtube {
  color: #ff0000; /* Rojo de YouTube */
}

.fa-linkedin {
  color: #0077b5; /* Azul de LinkedIn */
}

.fa-instagram {
  color: #e4405f; /* Rosa de Instagram */
}

.fa-whatsapp {
  color: #25d366; /* Verde de WhatsApp */
}

/* Estilo para los checkboxes */
input[type="checkbox"] {
  width: 18px;
  height: 18px;
  cursor: pointer;
  accent-color: #007bff; /* Color opcional */
}

/* Estilo para los íconos */
.social-icon {
  font-size: 1.8rem;
  width: 40px;
  text-align: center;
}
  </style>
