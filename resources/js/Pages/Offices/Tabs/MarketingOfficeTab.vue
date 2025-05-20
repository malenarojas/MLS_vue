<template>
    <div v-if="form">
      <!-- Sección Principal (dos columnas) -->
      <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-6 items-stretch">
        <!-- Columna Izquierda -->
        <div class="flex flex-col justify-between">
        <div class="space-y-4">
          <!-- Eslogan -->
          <div class="field">
            <label for="marketing_slogan" class="font-semibold">Eslogan</label>
            <InputText
              v-model="form.marketing_slogan"
              id="marketing_slogan"
              placeholder="Ingrese el eslogan"
              class="w-full"
            />
          </div>

          <!-- Website Description -->
          <div class="field">
            <label for="website_description" class="font-semibold">Website Description</label>
            <Editor
              v-model="form.website_description"
              id="website_description"
              editorStyle="height: 250px"
              placeholder="Haga clic aquí para escribir"
            />
          </div>

          <!-- Cierre -->
          <div class="field">
            <label for="closure" class="font-semibold">Cierre</label>
            <Editor
              v-model="form.closure"
              id="closure"
              editorStyle="height: 230px"
              placeholder="Escriba el cierre..."
            />
          </div>
        </div>
        </div>
        <div class="flex flex-col justify-between">
        <!-- Columna Derecha -->
        <div class="space-y-4">
          <!-- Enlace corto -->
          <div class="field">
            <label for="short_link">Enlace corto</label>
            <InputText
              v-model="form.short_link"
              id="short_link"
              placeholder="Ej: Altitud"
              class="w-full"
              @input="formatURL"
            />
          </div>

          <!-- Office Website -->
          <div class="field">
            <label for="office_website">Office Website</label>
            <InputText
              v-model="form.office_website"
              id="office_website"
              placeholder="https://"
              class="w-full"
            />
          </div>

          <!-- Redes Sociales -->
          <div>
            <h2 class="font-bold text-lg mb-2">Redes Sociales</h2>
            <div
            v-for="(network, index) in officeSocialNetworks"
            :key="index"
            class="flex items-center gap-3 mb-3"
            >
            <i :class="`${getSocialIcon(network.name)} text-2xl social-icon`"></i>
            <InputText
                v-model="network.url"
                :placeholder="`URL de ${capitalize(network.name)}`"
                class="small-input w-full"
                @input="handleNetworkChange(network)"
            />
            <input
                type="checkbox"
                :id="`checkbox-${index}`"
                v-model="network.state"
                :true-value="1"
                :false-value="0"
                class="w-5 h-5"
                @change="handleNetworkChange(network)"
            />
            </div>
          </div>

          <!-- Bullet Points: uno en cada fila -->
          <div>
            <!-- Cada bullet en su 'field' para una fila distinta -->
            <div class="field">
              <label for="bullet_point_one" class="font-semibold">Bullet Point 1</label>
              <InputText
                v-model="form.bullet_point_one"
                id="bullet_point_one"
                placeholder="Punto clave 1"
                class="w-full"
              />
            </div>
            <div class="field">
              <label for="bullet_point_two" class="font-semibold">Bullet Point 2</label>
              <InputText
                v-model="form.bullet_point_two"
                id="bullet_point_two"
                placeholder="Punto clave 2"
                class="w-full"
              />
            </div>
            <div class="field">
              <label for="bullet_point_three" class="font-semibold">Bullet Point 3</label>
              <InputText
                v-model="form.bullet_point_three"
                id="bullet_point_three"
                placeholder="Punto clave 3"
                class="w-full"
              />
            </div>
            <div class="field">
              <label for="bullet_point_four" class="font-semibold">Bullet Point 4</label>
              <InputText
                v-model="form.bullet_point_four"
                id="bullet_point_four"
                placeholder="Punto clave 4"
                class="w-full"
              />
            </div>
          </div>
        </div>
        </div>
      </div>
      <div class="mt-8">
  <h2 class="font-bold text-lg mb-2">Search Engine Optimization (SEO)</h2>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <!-- Meta Tag Keywords -->
    <div class="field">
      <label for="meta_tag_keywords" class="font-semibold">Meta Tag Keywords</label>
      <InputText
        v-model="form.meta_tag_keywords"
        id="meta_tag_keywords"
        placeholder="Por ej: venta de pisos, compra de oficinas..."
        class="w-full"
      />
    </div>

    <!-- Meta Tag Description -->
    <div class="field">
      <label for="meta_tag_description" class="font-semibold">Meta Tag Description</label>
      <InputText
        v-model="form.meta_tag_description"
        id="meta_tag_description"
        placeholder="Separate descriptions with commas"
        class="w-full"
      />
    </div>
  </div>
</div>
    </div>
  </template>


<script setup>
import '@fortawesome/fontawesome-free/css/all.css';
import Editor from 'primevue/editor';
import InputText from "primevue/inputtext";
import { computed, ref, watch } from "vue";
const props = defineProps({
  form: {
    type: Object,
    required: true,
  },
});
console.log("REDES SOCAILES CARGADAS", props.form.socialNetworks);
const emit = defineEmits(["updateSocialNetworksoffices"]);

const generatedURL = computed(() => {
  const baseUrl = "https://www.remax.bo/";
  return props.form.url ? `${baseUrl}${props.form.url}` : baseUrl;
});

const formatURL = () => {
  props.form.url = props.form.url.replace(/\s+/g, "-").toLowerCase();
};

// Redes sociales por defecto
const defaultSocialNetworks = [
  { name: "facebook", state: 0, url: "" },
  { name: "twitter", state: 0, url: "" },
  { name: "youtube", state: 0, url: "" },
  { name: "linkedin", state: 0, url: "" },
  { name: "instagram", state: 0, url: "" },
  { name: "whatsapp", state: 0, url: "" },
];

const officeSocialNetworks = ref([...defaultSocialNetworks]);

// Sincronizar si ya existen redes en `form.socialNetworks`
watch(
  () => props.form?.socialNetworks,
  (newNetworks) => {
    if (newNetworks.length > 0) {
        console.log("REDES SOCAILES CARGADAS", props.form.socialNetworks);

      officeSocialNetworks.value = newNetworks.map((network) => ({
        id: network.id,
        name: network.name,
        state: network.state || 0,
        url: network.url || "",
      }));
    } else {
        console.log("no hay redes poniendo las por defecto ", props.form.socialNetworks);

      officeSocialNetworks.value = [...defaultSocialNetworks];
      console.log("no hay redes poniendo las por defecto ", officeSocialNetworks.value);
    }
  },
  { immediate: true }
);

// Emitir cambios al padre
const emitOfficeSocialNetworks = () => {
  const payload = officeSocialNetworks.value.map((network) => ({
    id: network.id,
    name: network.name,
    state: network.state,
    url: network.url,
  }));

  emit("updateSocialNetworksoffices", payload);
};

// Cuando cambia una red
const handleNetworkChange = (network) => {
  emitOfficeSocialNetworks();
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
  return icons[name] || "fas fa-globe";
};

const capitalize = (str) => str.charAt(0).toUpperCase() + str.slice(1);
</script>


  <style>
  .field {
    display: flex;
    flex-direction: column;
  }

  label {
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 4px;
    color: #333;
  }

  .social-icon {
  font-size: 1.8rem;
  width: 40px;
  text-align: center;
}

.fa-facebook { color: #1877f2; }
.fa-twitter { color: #1da1f2; }
.fa-youtube { color: #ff0000; }
.fa-linkedin { color: #0077b5; }
.fa-instagram { color: #e4405f; }
.fa-whatsapp { color: #25d366; }

/* Estilo para inputs pequeños */
.small-input {
  height: 36px;
  font-size: 0.9rem;
  padding: 6px;
}
  </style>
