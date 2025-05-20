<template>
<!-- Datos Principales del Equipo -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
  <!-- Nombre -->
  <div>
    <label class="text-gray-700 text-sm font-medium block mb-1">Nombre del Equipo</label>
    <InputText v-model="form.name" class="w-full text-sm" placeholder="Nombre del equipo" />
  </div>

  <!-- Estado del equipo -->
  <div>
    <label class="text-gray-700 text-sm font-medium block mb-1">Estado</label>
    <Select
    v-model="form.is_active"
    :options="[
        { label: 'Activo', value: true },
        { label: 'Inactivo', value: false }
    ]"
    optionLabel="label"
    optionValue="value"
    placeholder="Seleccione estado"
    class="w-full text-sm"
    />
  </div>
  <!-- Email -->
  <div>
    <label class="text-gray-700 text-sm font-medium block mb-1">Correo</label>
    <InputText v-model="form.email" class="w-full text-sm" placeholder="Correo del equipo" />
  </div>

  <!-- Shortlink -->
  <div>
    <label class="text-gray-700 text-sm font-medium block mb-1">Shortlink</label>
    <InputText v-model="form.shortlink" class="w-full text-sm" placeholder="Shortlink personalizado" />
  </div>
    <!-- Teléfono -->
    <div>
    <label class="text-gray-700 text-sm font-medium block mb-1">Teléfono</label>
    <InputGroup>
      <InputGroupAddon>
        <i class="pi pi-phone"></i>
      </InputGroupAddon>
      <InputText v-model="form.phone_number" placeholder="Teléfono" class="w-full text-sm" />
    </InputGroup>
  </div>

<!-- Chat Habilitado + WhatsApp -->
<div class="flex items-center gap-2 mt-6">
      <Checkbox v-model="form.enable_chat" :binary="true" />
      <i class="pi pi-whatsapp text-green-500 text-lg"></i>
      <span class="text-red-600 font-medium text-sm">WhatsApp</span>
</div>

</div>
<!-- Redes Sociales -->
<h2 class="font-bold text-xl mb-4 mt-6">Redes Sociales</h2>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
  <div
    v-for="(network, index) in socialNetworks"
    :key="index"
    class="flex items-center gap-3 p-2 border rounded-md bg-gray-50"
  >
    <!-- Ícono -->
    <i :class="getSocialIcon(network.name) + ' social-icon'"></i>

    <!-- Input de URL -->
           <InputText
            v-model="network.url"
            :placeholder="`URL de ${network.name}`"
            class="small-input w-full"
        />
        <!-- Checkbox -->
        <input
        type="checkbox"
        :id="`checkbox-${index}`"
        v-model="network.state"
        :true-value="1"
        :false-value="0"
        class="w-5 h-5"
        @change="handleNetworkChange"
        />

  </div>
</div>
<!-- Sección: Foto del Equipo -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-start mt-6 border p-4 rounded-md bg-white">
  <!-- Imagen -->
  <div class="w-full max-w-[300px]">
    <Image
      :src="form.image_url || defaultImage"
      alt="Logo del equipo"
      width="250"
      preview
      class="rounded-md border object-contain"
    />
    <Button
      label="Eliminar foto actual"
      @click="clearImage"
      class="p-button-danger p-button-outlined w-full mt-2"
    />
  </div>

  <!-- Info -->
  <div>
    <label class="font-semibold text-gray-700 block mb-2">Seleccione la imagen que desea cargar.</label>
    <input type="file" @change="handleImageUpload" class="text-sm mb-3" />
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

<!-- Sección: Logo del Equipo -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-start mt-6 border p-4 rounded-md bg-white">
  <!-- Imagen -->
  <div class="w-full max-w-[300px]">
     <Image
      :src="form.logo_url || defaultImage"
      alt="Logo del equipo"
      width="250"
      preview
      class="rounded-md border object-contain"
    />
    <Button
      label="Eliminar logo actual"
      @click="clearLogo"
      class="p-button-danger p-button-outlined w-full mt-2"
    />
  </div>

  <!-- Info -->
  <div>
    <label class="font-semibold text-gray-700 block mb-2">Seleccione la imagen que desea cargar.</label>
    <input type="file" @change="handleLogoUpload" class="text-sm mb-3" />
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

  <script setup>
  import defaultImage from '@/assets/img/default.gif';
import { Button, Checkbox, InputGroup, InputGroupAddon, InputText, Select } from 'primevue';
import { computed, ref, watch } from 'vue';

  const props = defineProps({
    form: Object,
    agents: Array
  });

  const filteredAgents = computed(() => {
  return agents.filter(agent => props.agent.office_id === props.form.office_id)
})


const defaultSocialNetworks = [
  { name: "facebook", state: 0, url: "" },
  { name: "twitter", state: 0, url: "" },
  { name: "youtube", state: 0, url: "" },
  { name: "linkedin", state: 0, url: "" },
  { name: "instagram", state: 0, url: "" },
  { name: "whatsapp", state: 0, url: "" },
];

const socialNetworks = ref([...defaultSocialNetworks]);
watch(
  () => props.form?.socialNetworks,
  (newVal) => {
    if (Array.isArray(newVal) && newVal.length > 0) {
      socialNetworks.value = newVal.map((item) => ({
        id: item.id,
        name: item.name,
        url: item.url || "",
        state: item.state || 0,
      }));
    } else {
      socialNetworks.value = [...defaultSocialNetworks];
    }
  },
  { immediate: true }
);
const emit = defineEmits(["updateSocialNetworks"]);

const handleNetworkChange = () => {
  emit("updateSocialNetworks", socialNetworks.value);
};

  const handleImageUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
      props.form.image_file = file;
      props.form.image_url = URL.createObjectURL(file);
    }
  };
  const handleImageUpload1 = (event) => {
    const file = event.target.files[0];
    if (file) {
      props.form.logo_file = file;
      props.form.logo_url = URL.createObjectURL(file);
    }
  };
  const getSocialIcon = (name) => {
  const icons = {
    facebook: "fab fa-facebook",
    twitter: "fab fa-twitter",
    youtube: "fab fa-youtube",
    linkedin: "fab fa-linkedin",
    instagram: "fab fa-instagram",
    whatsapp: "fab fa-whatsapp",
  };
  return icons[name] || "pi pi-globe";
};

const handleLogoUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    props.form.logo_file = file;
    props.form.logo_url = URL.createObjectURL(file);
  }
};

const clearLogo = () => {
  props.form.logo_file = null;
  props.form.logo_url = null;
};

  const clearImage = () => {
    props.form.image_file = null;
    props.form.image_url = null;
  };
  </script>

  <style scoped>
  /* Add any scoped styles here */
  </style>
